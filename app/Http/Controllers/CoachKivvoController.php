<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Briefing;
use App\Services\OpenAiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoachKivvoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Pegar o primeiro briefing que não estiver desativado
        $briefing = Briefing::with('mensagens')->where([['user_id', $user->id], ['status', '!=', 'desativado']])->first();

        if (!$briefing) {
            // Criar um briefing, caso não exista nenhum briefing ativo
            $briefing = Briefing::create([
                'user_id' => $user->id,
                'status' => 'iniciado'
            ]);

            $briefing->load('mensagens');
        }

        return Inertia::render('CoachKivvo/Index', [
            'briefing' => $briefing
        ]);
    }

    public function iniciarBriefing(Request $request)
    {
        $user = auth()->user();
        $briefing = Briefing::where([['user_id', $user->id], ['status', 'iniciado']])->first();

        if ($briefing) {
            DB::beginTransaction();
            try {
                $openAiService = new OpenAiService();
                // Envia msg para AI iniciar a entrevista
                $response = $openAiService->startBriefing($briefing);

                // Salva a resposta da IA
                $mensagem = $briefing->mensagens()->create([
                    'origem' => 'ai',
                    'tipo' => 'texto',
                    'texto' => $response['output'][0]['content'][0]['text'],
                    'ordem' => $briefing->mensagens()->count() + 1,
                    'response_id' => $response['id'],
                    'input_token' => $response['usage']['input_tokens'],
                    'output_token' => $response['usage']['output_tokens']
                ]);
    
                // Atualiza o briefing com o previous_response_id, status, data_inicio e data_previous_response
                $briefing->update([
                    'status' => 'iniciado',
                    'data_inicio' => now(),
                    'data_previous_response' => now(),
                    'previous_response_id' => $response['id']
                ]);
    
                DB::commit();
                return back(303)->with([
                    'payload' => $mensagem
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro ao trocar mensagens com a IA', [
                    'exception' => $e,
                    'request' => $request->all()
                ]);
                return back(303)->with('message', [
                    'tipo' => 'erro',
                    'titulo' => 'Erro',
                    'corpo' => 'Erro ao trocar mensagens com a IA.'
                ]);
            }

            
        }

        session(['briefing_token' => $token]);
        return redirect()->route('briefings.entrevista', $token);
        
    }

    public function mensagem(Request $request, Briefing $briefing, OpenAiService $openAiService)
    {
        // if ($briefing->status === 'desativado') {
        //     return redirect()->route('briefings.completo', $briefing->token); // TODO: tem que arrumar isso aqui
        // }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ]);

        $mensagemTexto = $request->input('message', '');
        $imagemUrl = null;
        $imagemBase64 = null;

        // Processa a imagem se foi enviada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Salva a imagem no storage
            $imagePath = $image->store('briefing_images', 'public');
            $imagemUrl = Storage::url($imagePath);
            
            // Converte a imagem para base64 para enviar à OpenAI
            $imageContent = file_get_contents($image->getPathname());
            $imagemBase64 = base64_encode($imageContent);
            
            // Se não há texto, define uma mensagem padrão
            if (empty($mensagemTexto)) {
                $mensagemTexto = 'Imagem de referência';
            }
        }

        //dd($mensagemTexto, $imagemUrl, $imagemBase64);


        DB::beginTransaction();
        try {
            
            // Salva a mensagem do cliente
            $briefing->mensagens()->create([
                'origem' => 'usuario',
                'tipo' => 'texto',
                'texto' => $mensagemTexto,
                'ordem' => $briefing->mensagens()->count() + 1,
                'imagem_url' => $imagemUrl ?? null,
            ]);

            // Chama a IA com as ferramentas
            $response = $openAiService->callOpenAIWithTools($briefing, $mensagemTexto, $imagemUrl);

           


            // Verifica se a resposta da IA é uma função
            if (isset($response['output'][0]['type']) && $response['output'][0]['type'] === 'function_call') {
                $response = $openAiService->handleFunctionCall($briefing, $response);
            }

            //dd($response);

            // Salva a resposta da IA
            $mensagem = $briefing->mensagens()->create([
                'origem' => 'ai',
                'tipo' => 'texto',
                'texto' => $response['output'][0]['content'][0]['text'],
                'ordem' => $briefing->mensagens()->count() + 1,
                'response_id' => $response['id']
            ]);

            // Atualiza o previous_response_id no briefing
            $briefing->update(['previous_response_id' => $response['id']]);

            DB::commit();
            return back(303)->with([
                'payload' => $mensagem
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao trocar mensagens com a IA', [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return back(303)->with('message', [
                'tipo' => 'erro',
                'titulo' => 'Erro',
                'corpo' => 'Erro ao trocar mensagens com a IA.'
            ]);
        }
    }
}
