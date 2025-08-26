@vite('resources/css/app.css')

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano Alimentar - {{ $user->name }}</title>
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
                            <p class="text-sm text-gray-300">Plano Alimentar Personalizado</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-200">Meta Diária</p>
                        <p class="text-2xl font-bold text-primaria-500">{{ $planoAlimentar->calorias_alvo_dia }} kcal</p>
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
                    <p class="text-gray-700 mb-6">{{ $planoAlimentar->descricao }}</p>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Macros Alvo (por dia)</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Proteínas:</span>
                                    <span class="font-medium text-primaria-900">{{ $planoAlimentar->macro_alvos->proteina_g }}g</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Carboidratos:</span>
                                    <span class="font-medium text-blue-600">{{ $planoAlimentar->macro_alvos->carboidrato_g }}g</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Gorduras:</span>
                                    <span class="font-medium text-orange-600">{{ $planoAlimentar->macro_alvos->gordura_g }}g</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Observações</h4>
                            <p class="text-gray-600 text-sm">{{ $planoAlimentar->observacoes_gerais }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plano Diário -->
            <div class="mb-8">
                <div class="grid gap-4">
                    @foreach(['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'] as $dia)
                        <div class="border border-primaria-500 rounded-xl bg-gray-50 overflow-hidden print:break-inside-avoid print:my-4">
                            <div class="bg-primaria-500 px-4 py-2">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gunmetal-800">{{ ucfirst($dia) }}</h3>
                                    <span class="text-lg font-bold text-gunmetal-800">
                                        {{ $planoAlimentar->dias->$dia->kcal_dia }} kcal
                                    </span>
                                </div>
                            </div>
                            
                            <div class="divide-y divide-gray-100">
                                @foreach($planoAlimentar->dias->$dia->alimentacao as $refeicao)
                                    <div class="p-4 bg-white m-2 rounded-lg border border-primaria-500">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-semibold text-gunmetal-800 text-base">{{ explode('·', $refeicao->refeicao)[0] }}</h4>
                                            <span class="font-medium text-gunmetal-800 text-base">{{ $refeicao->kcal_total }} kcal</span>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            @foreach($refeicao->itens as $item)
                                                <div class="flex justify-between items-center py-1 border-l-2 border-primaria-500 ml-2 pl-4">
                                                    <div>
                                                        <span class="font-medium text-gunmetal-800 text-sm">{{ $item->alimento }}</span>
                                                        <span class="text-gray-600 text-xs ml-2">({{ $item->quantidade }})</span>
                                                    </div>
                                                    <span class="text-gray-500 font-medium text-sm">{{ $item->kcal }} kcal</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Alimentos Substitutos -->
            <div class="mb-8 border border-terciaria-200 rounded-xl shadow-md overflow-hidden print:break-inside-avoid print:my-10">
                <div class="bg-terciaria-200 px-6 py-4">
                    <h2 class="text-gunmetal-800 font-bold text-lg">Alimentos Substitutos</h2>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($planoAlimentar->alimentos_subtitutos as $substituto)
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                                <h5 class="font-semibold text-gray-700 mb-2 text-lg">{{ $substituto->alimento }}</h5>
                                <p class="text-base text-gray-600">
                                    <strong>Alternativas:</strong> {{ implode(', ', $substituto->alternativas) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Rodapé -->
            <footer class="bg-gunmetal-800 border-t-2 border-primaria-800 rounded-lg">
                <div class="container mx-auto px-6 py-6">
                    <div class="flex flex-col items-center text-center gap-4">
                        <img src="{{ asset('logo_fundo_escuro.svg') }}" alt="Kivvo" class="w-32">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-sm text-gray-300">Este plano alimentar foi gerado automaticamente pelo Coach Kivvo.</p>
                                <p class="text-sm text-gray-300">Para mais informações, consulte um profissional de nutrição.</p>
                            </div>
                        </div>
                        <a href="https://kivvo.app" class="text-2xl text-primaria-500 hover:text-primaria-400 transition-colors font-medium">kivvo.app</a>
                    </div>
                </div>
            </footer>
        </div>

    
</body>
</html>
