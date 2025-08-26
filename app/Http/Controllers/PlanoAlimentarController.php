<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Briefing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlanoAlimentarController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Busca o briefing do usuário, só com campo id, user_id, status, plano_alimentar e url_plano_alimentar
        $briefing = Briefing::where('user_id', $user->id)->select('id', 'user_id', 'status', 'plano_alimentar', 'url_plano_alimentar')->first(); 

        $planoAlimentar = json_decode($briefing->plano_alimentar, true);

        return Inertia::render('PlanoAlimentar/Index', [
            'planoAlimentar' => $planoAlimentar,
            'briefing' => $briefing
        ]);
    }

    public function gerarPdf(Briefing $briefing)
    {
        $briefing->load('user');
        $planoAlimentar = json_decode($briefing->plano_alimentar);

        $template = view('plano-alimentar', [
            'user' => $briefing->user,
            'planoAlimentar' => $planoAlimentar
        ])->render();

        Browsershot::html($template)
            ->showBackground() 
            ->format('A4')
            ->save(storage_path("app/planos-alimentares/{$briefing->id}.pdf"));
    }

    /** Preview em HTML (debug / design) */
    public function preview(Briefing $briefing)
    {
        $briefing->load('user');

        return view('plano-alimentar', [
            'user'             => $briefing->user,
            'planoAlimentar' => json_decode($briefing->plano_alimentar),
        ]);
    }

    public function baixarPdf(Briefing $briefing)
    {
        // 1. Garante que o briefing pertence ao user logado
        abort_if($briefing->user_id !== Auth::id(), 403);

        // 2. Caminho salvo no banco (ex.: planos-alimentares/2.pdf)
        $file = $briefing->url_plano_alimentar;

        // 3. Verifica se o arquivo existe
        if (! Storage::disk('local')->exists($file)) {
            abort(404, 'PDF não encontrado');
        }

        // 4. Faz o download
        return Storage::disk('local')->download(
            $file,
            'plano-alimentar.pdf',          // nome sugerido
            ['Content-Type' => 'application/pdf']
        );
    }
}
