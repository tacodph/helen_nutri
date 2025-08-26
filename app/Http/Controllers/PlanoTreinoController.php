<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Briefing;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class PlanoTreinoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Busca o briefing do usuário, só com campo id, user_id, status, plano_treino e url_plano_treino
        $briefing = Briefing::where('user_id', $user->id)->select('id', 'user_id', 'status', 'plano_treino', 'url_plano_treino')->first(); 

        $planoTreino = json_decode($briefing->plano_treino, true);

        return Inertia::render('PlanoTreino/Index', [
            'planoTreino' => $planoTreino,
            'briefing' => $briefing
        ]);
    }

    public function gerarPdf(Briefing $briefing)
    {
        $briefing->load('user');
        $planoTreino = json_decode($briefing->plano_treino);

        $template = view('plano-treino', [
            'user' => $briefing->user,
            'planoTreino' => $planoTreino
        ])->render();

        Browsershot::html($template)
            ->showBackground() 
            ->format('A4')
            ->save(storage_path("app/planos-treinos/{$briefing->id}.pdf"));
    }

    /** Preview em HTML (debug / design) */
    public function preview(Briefing $briefing)
    {
        $briefing->load('user');

        return view('plano-treino', [
            'user'             => $briefing->user,
            'planoTreino' => json_decode($briefing->plano_treino),
        ]);
    }

    public function baixarPdf(Briefing $briefing)
    {
        // 1. Garante que o briefing pertence ao user logado
        abort_if($briefing->user_id !== Auth::id(), 403);

        // 2. Caminho salvo no banco (ex.: planos-alimentares/2.pdf)
        $file = $briefing->url_plano_treino;

        // 3. Verifica se o arquivo existe
        if (! Storage::disk('local')->exists($file)) {
            abort(404, 'PDF não encontrado');
        }

        // 4. Faz o download
        return Storage::disk('local')->download(
            $file,
            'plano-treino.pdf',          // nome sugerido
            ['Content-Type' => 'application/pdf']
        );
    }
}
