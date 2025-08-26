<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Dados físicos básicos
            $table->decimal('altura', 5, 2)->nullable(); // em cm
            $table->decimal('peso_atual', 5, 2)->nullable(); // em kg
            $table->decimal('peso_meta', 5, 2)->nullable(); // em kg
            $table->decimal('percentual_gordura_atual', 5, 2)->nullable(); // em %
            $table->decimal('percentual_gordura_meta', 5, 2)->nullable(); // em %
            $table->enum('nivel_atividade', ['sedentario', 'leve', 'moderado', 'intenso', 'muito_intenso'])->nullable();
            $table->enum('objetivo_principal', ['perder_peso', 'ganhar_massa', 'definir', 'manter', 'saude_geral'])->nullable();
            
            // JSONB fields (índices criados em migration separada)
            $table->jsonb('condicoes_saude')->nullable(); // ["diabetes", "hipertensao"]
            $table->jsonb('lesoes')->nullable(); // ["joelho_esquerdo", "ombro_direito", "bacia"]
            $table->jsonb('alergias')->nullable(); // ["lactose", "gluten", "amendoim"]
            $table->jsonb('restricoes_alimentares')->nullable(); // ["vegetariano", "vegano", "sem_lactose"]
            $table->jsonb('local_treino_preferidos')->nullable(); // ["academia", "crossfit", "casa"]
            $table->jsonb('horarios_treino_preferidos')->nullable(); // ["manha", "tarde", "noite"]
            $table->jsonb('equipamentos_disponiveis')->nullable(); // ["halteres", "esteira", "bicicleta"]
            $table->jsonb('alimentos_nao_gosta')->nullable(); // ["brocolis", "peixe", "aveia"]
            $table->jsonb('culinarias_preferidas')->nullable(); // ["italiana", "japonesa", "brasileira"]
            
            // Rotina e disponibilidade
            $table->integer('dias_disponiveis_por_semana')->nullable(); // 1-7
            $table->enum('local_treino', ['academia', 'casa', 'ambos'])->nullable();
            $table->integer('anos_experiencia_treino')->nullable();
            $table->integer('meta_agua_diaria_ml')->default(2000);
            $table->time('horario_acordar')->nullable();
            $table->time('horario_dormir')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};
