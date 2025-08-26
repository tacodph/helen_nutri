<?php

namespace App\Observers;

use App\Models\Briefing;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessarDadosBriefingJob;
use App\Jobs\CriarPlanoAlimentarJob;
use App\Jobs\CriarPlanoTreinoJob;
use App\Mail\PlanosProntosMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class BriefingObserver
{
    /**
     * Handle the Briefing "created" event.
     */
    public function created(Briefing $briefing): void
    {
        Log::info('Briefing criado', [
            'id' => $briefing->id,
            'user_id' => $briefing->user_id,
            'status' => $briefing->status
        ]);
    }

    public function updated(Briefing $briefing): void
    {
        // Verifica se o status mudou
        if ($briefing->wasChanged('status')) {
            Log::info('Status do briefing mudou', [
                'briefing_id' => $briefing->id,
                'old_status' => $briefing->getOriginal('status'),
                'new_status' => $briefing->status
            ]);
            
            $this->handleStatusChange($briefing);
        }
    }

    private function handleStatusChange(Briefing $briefing)
    {
        switch ($briefing->status) {
            case 'processando_dados':
                Log::info('Briefing confirmado, disparando job de processamento', [
                    'briefing_id' => $briefing->id,
                    'user_id' => $briefing->user_id,
                    'status' => $briefing->status
                ]);
                
                ProcessarDadosBriefingJob::dispatch($briefing)
                    ->onQueue('briefings') // fila específica
                    ->delay(now()->addSeconds(2)); // delay de 2 segundos
                break;
                
            case 'criando_plano_alimentar':
                // Notifica quando o plano estiver pronto
                Log::info('Dados do briefing processados, disparando job de criação do plano de alimentação', [
                    'briefing_id' => $briefing->id
                ]);                
                CriarPlanoAlimentarJob::dispatch($briefing)
                    ->onQueue('briefings') // fila específica
                    ->delay(now()->addSeconds(2)); // delay de 2 segundos
            
                break;
            case 'criando_plano_treino':
                // Notifica quando o plano estiver pronto
                Log::info('Plano de alimentação criado, disparando job de criação do plano de treino', [
                    'briefing_id' => $briefing->id
                ]);

                CriarPlanoTreinoJob::dispatch($briefing)
                    ->onQueue('briefings') // fila específica
                    ->delay(now()->addSeconds(2)); // delay de 2 segundos
                break;

            case 'concluido':
                Log::info('Briefing concluído, enviando email com os PDFs', [
                    'briefing_id' => $briefing->id
                ]);
            
                // valida se os dois arquivos existem
                if ($briefing->url_plano_alimentar && $briefing->url_plano_treino) {
                    Mail::to($briefing->user)->queue(
                        new PlanosProntosMail($briefing)
                    );
                } else {
                    Log::warning('Arquivos PDF não encontrados para o briefing', [
                        'briefing_id' => $briefing->id,
                        'alimentar'   => $briefing->url_plano_alimentar,
                        'treino'      => $briefing->url_plano_treino,
                    ]);
                }
                break;

            case 'erro':
                Log::error('Erro ao processar briefing', [
                    'briefing_id' => $briefing->id,
                    'status' => $briefing->status,
                    'error_message' => $briefing->error_message,    
                    'tentativas' => $briefing->tentativas
                ]);
                break;
        }
    }



    /**
     * Handle the Briefing "deleted" event.
     */
    public function deleted(Briefing $briefing): void
    {
        //
    }

    /**
     * Handle the Briefing "restored" event.
     */
    public function restored(Briefing $briefing): void
    {
        //
    }

    /**
     * Handle the Briefing "force deleted" event.
     */
    public function forceDeleted(Briefing $briefing): void
    {
        //
    }
}
