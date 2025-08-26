<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Briefing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_entrevista',
        'status',
        'titulo',
        'resumo_objetivos',
        'dados_briefing',
        'plano_alimentar',
        'plano_treino',
        'previous_response_id',
        'url_plano_alimentar',
        'url_plano_treino',
        'data_inicio',
        'data_processando_dados',
        'data_criando_plano_alimentar',
        'data_criando_plano_treino',
        'data_concluido',
        'data_revisao_iniciada',
        'data_revisao_concluida',
        'data_desativado',
        'data_erro',
        'data_previous_response',
        'error_message',
        'tentativas',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_processando_dados' => 'datetime',
        'data_criando_plano_alimentar' => 'datetime',
        'data_criando_plano_treino' => 'datetime',
        'data_concluido' => 'datetime',
        'data_revisao_iniciada' => 'datetime',
        'data_revisao_concluida' => 'datetime',
        'data_desativado' => 'datetime',
        'data_erro' => 'datetime',
        'data_previous_response' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mensagens()
    {
        return $this->hasMany(Mensagem::class)->orderBy('ordem');
    }
}
