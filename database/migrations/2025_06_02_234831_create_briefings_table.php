<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('briefings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->enum('tipo_entrevista', [
                'rapida', // entrevista rápida
                'detalhada', // entrevista detalhada
            ])->nullable();

            $table->enum('status', [
                'iniciado', // começou a conversa
                'processando_dados', // usuário confirmou os dados, processando dados na OpenAI
                'criando_plano_alimentar', // dados forma processados, criando o plano de alimentação pela OpenAI
                'criando_plano_treino', // plano de alimentação criado, criando o plano de treino pela OpenAI
                'concluido', // planos disponíveis para download
                'revisao_iniciada', // revisão do plano iniciada pelo usuário
                'revisao_concluida', // revisão do plano pelo usuário
                'desativado',
                'erro'
            ])->default('iniciado');

            $table->string('titulo')->nullable();
            $table->text('resumo_objetivos')->nullable();

            $table->jsonb('dados_briefing')->nullable(); // Json da function call completa
            $table->jsonb('plano_alimentar')->nullable(); // Json com o plano de alimentação
            $table->jsonb('plano_treino')->nullable(); // Json com o plano de treino

            $table->string('previous_response_id')->nullable(); // ID da resposta anterior da OpenAI
           
            $table->string('url_plano_alimentar')->nullable();
            $table->string('url_plano_treino')->nullable();

            $table->timestamp('data_inicio')->nullable();
            $table->timestamp('data_processando_dados')->nullable();
            $table->timestamp('data_criando_plano_alimentar')->nullable();
            $table->timestamp('data_criando_plano_treino')->nullable();
            $table->timestamp('data_concluido')->nullable();
            $table->timestamp('data_revisao_iniciada')->nullable();
            $table->timestamp('data_revisao_concluida')->nullable();
            $table->timestamp('data_desativado')->nullable();
            $table->timestamp('data_erro')->nullable();
            $table->timestamp('data_previous_response')->nullable(); // Se for maior que 30 dias, a OpenAI não lembra mais da conversa. Se for maior que 30 dias, a OpenAI não lembra mais da conversa.
            $table->text('error_message')->nullable(); // Mensagem de erro
            $table->integer('tentativas')->default(0); // Contador de tentativas de processamento
            $table->timestamps();

            // Índices para performance
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('briefings');
    }
};
