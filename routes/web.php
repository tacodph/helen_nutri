<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CoachKivvoController;
use App\Http\Controllers\PlanoAlimentarController;
use App\Http\Controllers\PlanoTreinoController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/termos-de-uso', function () {
    return Inertia::render('Legal/TermosDeUso');
})->name('termos-de-uso');

Route::get('/politica-de-privacidade', function () {
    return Inertia::render('Legal/PoliticaDePrivacidade');
})->name('politica-de-privacidade');

Route::get('/lgpd', function () {
    return Inertia::render('Legal/LGPD');
})->name('lgpd');


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        //return Inertia::render('Dashboard');

        //redirecionar para a route coach-kivvo.index
        return redirect('/coach-kivvo');
    })->name('dashboard');

    // CoachKivvo
    Route::get('/coach-kivvo', [CoachKivvoController::class, 'index'])->name('coach-kivvo.index');
    Route::post('/coach-kivvo/iniciarBriefing', [CoachKivvoController::class, 'iniciarBriefing'])->name('coach-kivvo.iniciarBriefing');
    Route::post('/coach-kivvo/{briefing}/mensagem', [CoachKivvoController::class, 'mensagem'])->name('coach-kivvo.mensagem');


    Route::get('/plano-alimentar', [PlanoAlimentarController::class, 'index'])->name('plano-alimentar.index');
    Route::get('/plano-alimentar/{briefing}',  [PlanoAlimentarController::class,'preview'])
         ->name('plano-alimentar.preview');

    Route::get('/plano-alimentar/{briefing}/pdf',  [PlanoAlimentarController::class,'baixarPdf'])
         ->name('plano-alimentar.baixar');

    Route::get('/plano-treino', [PlanoTreinoController::class, 'index'])->name('plano-treino.index');
    Route::get('/plano-treino/{briefing}',  [PlanoTreinoController::class,'preview'])
         ->name('plano-treino.preview');
    Route::get('/plano-treino/{briefing}/pdf',  [PlanoTreinoController::class,'baixarPdf'])
         ->name('plano-treino.baixar');


});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
