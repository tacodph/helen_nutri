<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('briefing_id')->constrained()->onDelete('cascade');

            $table->enum('origem', ['usuario', 'ai']); // Indica se a mensagem é do usuário ou da IA
            $table->enum('tipo', ['texto', 'audio', 'imagem']); // Tipo de conteúdo da mensagem
            $table->unsignedInteger('ordem'); // Ordem da mensagem na conversa
            $table->text('texto')->nullable(); // Para mensagens de texto
            $table->string('audio_url')->nullable(); // Caminho do arquivo de áudio
            $table->string('imagem_url')->nullable(); // Caminho do arquivo de imagem
            $table->string('response_id')->nullable(); // ID da resposta da OpenAI, se aplicável
            $table->unsignedInteger('input_token')->nullable(); // Número de input tokens usado na resposta da OpenAI, se aplicável
            $table->unsignedInteger('output_token')->nullable(); // Número de output tokens usado na resposta da OpenAI, se aplicável

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};
