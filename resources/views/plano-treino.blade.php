@vite('resources/css/app.css')

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano de Treino - {{ $user->name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.03;
            font-size: 72px;
            color: #4CAF50;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-sans text-gray-800 leading-relaxed bg-white">
    <!-- Cabeçalho -->
    <header class="bg-gunmetal-800 border-b-2 border-primaria-800">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logotipo_fundo_escuro.svg') }}" alt="Kivvo" class="w-14 h-14">
                    <div>
                        <h1 class="text-3xl font-bold text-white">KIVVO</h1>
                        <p class="text-sm text-gray-300">Plano de Treino Personalizado</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Informações do Perfil -->
        <div class="mb-8 border border-secundaria-400 rounded-xl shadow-md overflow-hidden">
            <div class="bg-secundaria-400 px-6 py-4">
                <h2 class="text-gunmetal-800 font-bold text-lg">Perfil do Cliente</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-6">{{ $planoTreino->descricao }}</p>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">Observações Gerais</h4>
                    <p class="text-gray-600 text-sm">{{ $planoTreino->observacoes_gerais }}</p>
                </div>
            </div>
        </div>

        <!-- Plano Semanal -->
        <div class="mb-8">
            <div class="grid gap-4">
                @foreach(['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'] as $dia)
                    <div class="border border-primaria-500 rounded-xl bg-gray-50 overflow-hidden print:break-inside-avoid print:my-4">
                        <div class="bg-primaria-500 px-4 py-2">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gunmetal-800">{{ ucfirst($dia) }}</h3>
                                <span class="text-lg font-bold text-gunmetal-800">
                                    {{ $planoTreino->dias->$dia->treino->tipo }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            @if(count($planoTreino->dias->$dia->treino->exercicios) > 0)
                                <div class="space-y-2">
                                    @foreach($planoTreino->dias->$dia->treino->exercicios as $exercicio)
                                        <div class="bg-white p-3 rounded-lg border border-primaria-500">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="font-semibold text-gunmetal-800 text-base">{{ $exercicio->nome }}</h4>
                                                <div class="text-right">
                                                    <span class="text-sm font-medium text-primaria-900">{{ $exercicio->series }} séries</span>
                                                    <span class="text-sm font-medium text-primaria-900 ml-2">{{ $exercicio->reps }}</span>
                                                </div>
                                            </div>
                                            @if($exercicio->obs)
                                                <p class="text-sm text-gray-600 italic mt-1">
                                                    <span class="font-medium">Obs:</span> {{ $exercicio->obs }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-2">
                                    <p class="text-gray-600">Dia de descanso</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rodapé -->
        <footer class="bg-gunmetal-800 border-t-2 border-primaria-800 rounded-lg">
            <div class="container mx-auto px-6 py-6">
                <div class="flex flex-col items-center text-center gap-4">
                    <img src="{{ asset('logo_fundo_escuro.svg') }}" alt="Kivvo" class="w-32">
                    <div class="flex items-center gap-3">
                        <div>
                            <p class="text-sm text-gray-300">Este plano de treino foi gerado automaticamente pelo Coach Kivvo.</p>
                            <p class="text-sm text-gray-300">Para mais informações, consulte um profissional de educação física.</p>
                        </div>
                    </div>
                    <a href="https://kivvo.app" class="text-2xl text-primaria-500 hover:text-primaria-400 transition-colors font-medium">kivvo.app</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
