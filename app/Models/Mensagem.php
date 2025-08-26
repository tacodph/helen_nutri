<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';

    protected $fillable = [
        'briefing_id',
        'origem',
        'tipo',
        'texto',
        'audio_url',
        'imagem_url',
        'response_id',
        'input_token',
        'output_token',
        'ordem',
    ];

    public function briefing()
    {
        return $this->belongsTo(Briefing::class);
    }
}

