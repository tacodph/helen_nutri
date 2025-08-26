<?php

namespace App\Jobs;

use App\Models\Briefing;
use App\Services\OpenAIService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class CriarPlanoAlimentarJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 300; // 5 minutos
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry após 1, 2 e 5 minutos

    protected $briefing;

    /**
     * Create a new job instance.
     */
    public function __construct(Briefing $briefing)
    {
        $this->briefing = $briefing;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAI): void
    {
        try {
            Log::info('Iniciando a criação do plano alimentar', [
                'briefing_id' => $this->briefing->id,
                'status' => $this->briefing->status,
                'attempt' => $this->attempts() // 
            ]);

            // Atualiza status com refresh para garantir dados atualizados
            $this->briefing->refresh();
            $this->briefing->update([
                'tentativas' => $this->attempts()
            ]);

            // Chamada complexa para OpenAI (que pode demorar)
            $response = $openAI->functionCallFromJob($this->briefing);

            // Valida se a resposta da IA é uma função
            if (!isset($response['output'][0]['type']) || 
                $response['output'][0]['type'] !== 'function_call') {
                throw new \Exception('Resposta inválida da OpenAI');
            }

            $functionOutput = $response['output'][0];
            $functionName = $functionOutput['name'];
            $functionArgs = json_decode($functionOutput['arguments'], true);

            // Valida se o JSON foi decodificado corretamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Erro ao decodificar argumentos: ' . json_last_error_msg());
            }

            // Salva os dados do plano de alimentação gerados pela IA
            $this->briefing->update([
                'plano_alimentar' => $functionArgs,
                'error_message' => null, // Limpa erros anteriores
                'data_erro' => null
            ]);

            Log::info('Plano alimentar criado com sucesso', [
                'briefing_id' => $this->briefing->id,
                'dados_campos' => count($functionArgs)
            ]);  

            // Chama a função para processar os dados do briefing
            $response = $openAI->handleFunctionCallOutputFromJob($response);

            // Salva a resposta da IA
            $mensagem = $this->briefing->mensagens()->create([
                'origem' => 'ai',
                'tipo' => 'texto',
                'texto' => $response['output'][0]['content'][0]['text'],
                'ordem' => $this->briefing->mensagens()->count() + 1,
                'response_id' => $response['id']
            ]);

            // Crie o pdf do plano alimentar
            $this->criarPdfPlanoAlimentar($this->briefing);


            // Atualiza o briefing e modifica o status para criar o plano de treino
            $this->briefing->update([
                'status' => 'criando_plano_treino',
                'data_criando_plano_treino' => now(),
                'previous_response_id' => $response['id'],
                'data_previous_response' => now()
            ]);

            Log::info('Mensagem da IA salva no banco', [
                'briefing_id' => $this->briefing->id,
                'mensagem' => $mensagem->texto
            ]);  
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar plano alimentar', [
                'briefing_id' => $this->briefing->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->briefing->update([
                'status' => 'erro',
                'error_message' => $e->getMessage(),
                'data_erro' => now()
            ]);

            // Re-throw para retry automático
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        // Quando todas as tentativas falharem
        $this->briefing->update([
            'status' => 'erro',
            'error_message' => $exception->getMessage(),
            'data_erro' => now()
        ]);

        // Notifica admin ou usuário sobre a falha
        Log::critical('Criação de plano alimentar falhou permanentemente', [
            'briefing_id' => $this->briefing->id,
            'error' => $exception->getMessage()
        ]);
    }

    private function criarPdfPlanoAlimentar(Briefing $briefing): void
    {

        Log::info('Criando o pdf do plano alimentar', [
            'briefing_id' => $briefing->id,
            'status' => $briefing->status,
            'attempt' => $this->attempts() 
        ]);
        
        // 1. Carregar o usuário e o JSON já salvo
        $briefing->load('user');
        // $briefing->plano_alimentar é array → converte para objeto recursivamente
        $plano = json_decode(json_encode($briefing->plano_alimentar));

        // 2. Renderizar o Blade (o mesmo usado no preview)
        $template = view('plano-alimentar', [
            'user'             => $briefing->user,
            'planoAlimentar' => $plano,
        ])->render();

        // 3. Gerar o PDF com Browsershot
        $file   = "planos-alimentares/{$briefing->id}.pdf";
        $path = Storage::disk('local')->path($file);  // garante pasta /private

        Browsershot::html($template)
            ->showBackground()          // necessário p/ bg-image, gradientes
            ->format('A4')     // capa full-bleed? ajuste se quiser
            ->save($path);

        Log::info('Pdf do plano alimentar criado com sucesso', [
            'briefing_id' => $briefing->id,
            'status' => $briefing->status,
            'attempt' => $this->attempts() 
        ]);

        // 4. Opcional: salvar caminho no banco
        $briefing->update(['url_plano_alimentar' => $file]);

        // 5. Enviar e-mail em fila (anexa PDF)
        // Mail::to($briefing->user->email)
        //     ->queue(new PlanoAlimentacaoProntoMail($briefing, $path));
    }
}
