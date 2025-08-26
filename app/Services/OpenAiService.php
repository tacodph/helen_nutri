<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Briefing;
use App\Services\PromptService;

class OpenAiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function startBriefing(Briefing $briefing): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'instructions' => $this->instructions($briefing),
            'temperature' => 0.3,
            'input' => [
                [
                    'role' => 'system',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => 'Inicie a entrevista com o usuário, pois o mesmo acabou de entrar no chat. Seu objetivo é saber se o usuário deseja uma entrevista detalhada ou rápida.'
                        ]
                    ]
                ]
            ],
        ])->json();

        return $response;
    }

    public function callOpenAIWithTools(Briefing $briefing, string $mensagem, string $imagemUrl = null): array
    {
        $tools = $this->getTools($briefing);
        $input = $this->getInput($briefing, $mensagem, $imagemUrl);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'instructions' => $this->instructions($briefing),
            'temperature' => 0.3,
            'input' => $input,
            'previous_response_id' => $briefing->previous_response_id,
            'tools' => $tools, 
        ])->json();

        // dd($response);

        return $response;
    }

    public function handleFunctionCall(Briefing $briefing, array $initialResponse): array
    {
        $functionOutput = $initialResponse['output'][0];
        $functionName = $functionOutput['name'];
        $functionArgs = json_decode($functionOutput['arguments'], true);

        $functionResult = null;

        $tools = $this->getTools($briefing);

        switch ($functionName) {
            case 'definir_tipo_entrevista':
                $functionResult = $this->definirTipoEntrevista($briefing, $functionArgs);
                break;
            case 'confirmar_dados_briefing':
                $functionResult = $this->confirmarDadosBriefing($briefing, $functionArgs);
                break;
            default:
                $functionResult = ['error' => 'Função não encontrada'];
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'instructions' => $this->instructions($briefing),
            'temperature' => 0.3,
            'input' => [
                [
                    'type' => 'function_call_output',
                    'call_id' => $functionOutput['call_id'],
                    'output' => json_encode($functionResult)
                ]
            ],
            'previous_response_id' => $initialResponse['id'],
            'tools' => $tools
        ])->json();
    }

    public function handleFunctionCallOutputFromJob(array $initialResponse)
    {
        $functionOutput = $initialResponse['output'][0];
        $functionName = $functionOutput['name'];
        $functionArgs = json_decode($functionOutput['arguments'], true);

        // Inicializa $output com um valor padrão
        $output = [
            'error' => true,
            'mensagem' => 'Erro inesperado. Tente novamente mais tarde.'
        ];

        switch ($functionName) {
            case 'processar_dados_briefing':
                $output = [
                    'sucesso' => true,
                    'mensagem' => "Dados do briefing foram processados com sucesso. **Fale exatamente isso**: [Nome], seus dados foram processados com sucesso e agora vou criar o seu plano personalizado de alimentação. Por favor, aguarde só mais um minutinho..."
                ];
                break;
            case 'criar_plano_alimentar':
                $output = [
                    'sucesso' => true,
                    'mensagem' => "Plano de alimentação criado com sucesso. **Fale exatamente isso**: [Nome], seu plano de alimentação foi criado com sucesso. Agora vou criar o seu plano personalizado de treino. Só um pouco mais de paciência... Vai valer à pena! 💪"
                ];
                break;
            case 'criar_plano_treino':
                $output = [
                    'sucesso' => true,
                    'mensagem' => "Plano de treino criado com sucesso. **Fale exatamente isso**: Aeeeeee! [Nome], seus planos personalizados de treino e alimentação estão prontos! Por favor, acesse a página de planos para fazer o download."
                ];
                break;
            default:
                $output = [
                    'error' => true,
                    'mensagem' => "Função não encontrada. **Fale exatamente isso**: [Nome], me desculpe, mas estou com um problema técnico. Por favor, tente novamente mais tarde."
                ];
                break;
        }

        // Timeout de 90 segundos e retry de 2 vezes com 2 segundos de delay
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->timeout(90)->retry(2, 2000)
        ->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o-mini',
            'temperature' => 0.3,
            'input' => [
                [
                    'type' => 'function_call_output',
                    'call_id' => $functionOutput['call_id'],
                    'output' => json_encode($output)
                ]
            ],
            'previous_response_id' => $initialResponse['id'],
        ])->json();  
    }

    private function confirmarDadosBriefing(Briefing $briefing, array $functionArgs): array
    {     
        try {
            if ($functionArgs['confirmou'] === true) {
                $briefing->update([
                    'status' => 'processando_dados',
                    'data_processando_dados' => now()
                ]);

                return [
                    'sucesso' => true,
                    'mensagem' => "Dados do briefing foram confirmados com sucesso. **Fale exatamente isso**: [Nome], obrigado por confirmar os dados. Agora vou iniciar o processamento dos dados. Por favor, aguarde um instante..."
                ];
            } else {
                return [
                    'sucesso' => false,
                    'error' => "Dados do briefing não foram confirmados.",
                    'mensagem' => "Dados do briefing não foram confirmados. Por favor, confirme os dados para iniciar processamento dos dados."
                ];
            }

            
        } catch (\Exception $e) {
            Log::error('Erro ao confirmar dados do briefing', [
                'briefing_id' => $briefing->id,
                'exception' => $e->getMessage()
            ]);
            return [
                'sucesso' => false,
                'error' => "Erro ao confirmar dados do briefing",
                'mensagem' => $e->getMessage()
            ];
        } 
    }

    private function definirTipoEntrevista(Briefing $briefing, array $functionArgs): array
    {     
        try {
            if ($functionArgs['detalhada'] === true) {
                $briefing->update([
                    'tipo_entrevista' => 'detalhada',
                ]);

                return [
                    'sucesso' => true,
                    'mensagem' => "Usuário deseja uma entrevista detalhada."
                ];
            } else {
                $briefing->update([
                    'tipo_entrevista' => 'rapida',
                ]);
                return [
                    'sucesso' => true,
                    'mensagem' => "Usuário deseja uma entrevista rápida."
                ];
            }

            
        } catch (\Exception $e) {
            Log::error('Erro ao definir tipo de entrevista', [
                'briefing_id' => $briefing->id,
                'exception' => $e->getMessage()
            ]);
            return [
                'sucesso' => false,
                'error' => "Erro ao definir tipo de entrevista",
                'mensagem' => $e->getMessage()
            ];
        } 
    }

    // Chamado somente pelos Jobs
    public function functionCallFromJob(Briefing $briefing): array
    {
        $tools = [];
        $input = [];

        switch ($briefing->status) {
            case 'processando_dados':
                $tools[] = $this->getFunctionProcessarDadosBriefing();
                $input[] = $this->getInputSystemProcessandoDadosBriefing();
                break;
            case 'criando_plano_alimentar':
                $tools[] = $this->getFunctionCriarPlanoAlimentacao();
                $input[] = $this->getInputSystemCriarPlanoAlimentacao($briefing);
                break;
            case 'criando_plano_treino':
                $tools[] = $this->getFunctionCriarPlanoTreino();
                $input[] = $this->getInputSystemCriarPlanoTreino($briefing);
                break;
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->timeout(90)->retry(2, 2000)->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4o',
            'instructions' => $this->instructions($briefing),
            'temperature' => 0.3,
            'input' => $input,
            'previous_response_id' => $briefing->previous_response_id,
            'tools' => $tools
        ])->json();

        return $response;
    }
   

    private function getInput(Briefing $briefing, string $mensagem, string $imagemUrl = null)
    {
        $input = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => $mensagem
                    ]
                ]
            ]
        ];

        // se tiver imagem, adiciona a imagem no input no user
        if ($imagemUrl) {
            // Pegar a URL completa da imagem, inclusive o domínio
            $imagemUrl = url($imagemUrl);
            $input[0]['content'][] = [
                'type' => 'input_image',
                'image_url' => $imagemUrl,
                //'image_url' => 'https://tuacasa.uol.com.br/wp-content/uploads/2023/07/cozinha-moderna-51.jpg', // Temporário
                'detail' => 'high'
            ];
        }
        
        if ($briefing->status === 'processando_dados' || $briefing->status === 'criando_plano_alimentar' || $briefing->status === 'criando_plano_treino') {
            $input[] = [
                'role' => 'system',
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => 'Você coletou todos os dados do usuário e está gerando o plano.
                            Enquanto esse processo não termina, **responda sempre**:

                            “{NOME}, estou ocupado na geração do seu plano personalizado. Por favor aguarde um instante. Obrigado!”

                            → Ignore qualquer outra pergunta ou pedido do usuário até o plano ficar pronto.'
                    ]
                ]
            ];
        } else if ($briefing->status === 'concluido') {
            $input[] = [
                'role' => 'system',
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => 'Você já coletou todos os dados do usuário e o plano está disponível para download.
                            - Seja cordial, direto e entusiasmado. 
                            - Diga ao usuário que seus planos de treino e alimentação personalizado estão prontos e que ele pode acessar a página de planos para fazer o download.'
                    ]
                ]
            ];  
        }

        return $input;
    }

    private function instructions(Briefing $briefing)
    {
        $briefing->load('user');
        $nome = explode(' ', $briefing->user->name);
        $nome = $nome[0];

        if ($briefing->status === 'iniciado' && $briefing->tipo_entrevista === null) {
            return PromptService::getInstructionsInicio($nome);
        } else if ($briefing->status === 'iniciado' && $briefing->tipo_entrevista === 'rapida') {
            return PromptService::getInstructionsEntrevistaRapida($nome);
        } else if ($briefing->status === 'iniciado' && $briefing->tipo_entrevista === 'detalhada') {
            return PromptService::getInstructionsEntrevistaDetalhada($nome);
        } else if ($briefing->status === 'processando_dados') {
            return PromptService::getInstructionsProcessando($nome);
        } else if ($briefing->status === 'criando_plano_alimentar') {
            return PromptService::getInstructionsMontarPlanoAlimentar($nome);
        } else if ($briefing->status === 'criando_plano_treino') {
            return PromptService::getInstructionsMontarPlanoTreino($nome);
        }

        return PromptService::getInstructionsEntrevistaDetalhada($nome);
    }

    private function getTools(Briefing $briefing): array
    {
        $functionCalls = [];

        // Function calls
        $definir_tipo_entrevista = [
            'type' => 'function',
            "name" => "definir_tipo_entrevista",
            "description" => "Define o tipo de entrevista que o usuário deseja.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "required" => [
                    "detalhada"
                ],
                "properties" => [
                    "detalhada" => [
                        "type" => "boolean",
                        "description" => "Se o usuário deseja uma entrevista detalhada, defina como true. Se o usuário deseja uma entrevista rápida, defina como false."
                    ]
                ],
                "additionalProperties" => false
            ]
        ];

        $confirmar_dados_briefing = [
            'type' => 'function',
            "name" => "confirmar_dados_briefing",
            "description" => "Confirmação explícita do usuário de que revisou e aprovou todos os dados coletados.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "required" => [
                    "confirmou"
                ],
                "properties" => [
                    "confirmou" => [
                        "type" => "boolean",
                        "description" => "Confirmação explícita do usuário de que revisou e aprovou todos os dados coletados."
                    ]
                ],
                "additionalProperties" => false
            ]
        ];


        if ($briefing->status === 'iniciado') {
            if ($briefing->tipo_entrevista === null) {
                $functionCalls[] = $definir_tipo_entrevista;
            } else {
                $functionCalls[] = $confirmar_dados_briefing;
            }
        } 

        return $functionCalls;
    }

    private function getFunctionProcessarDadosBriefing()
    {
        $nome_apelido = [
            "type" => "string",
            "description" => "Nome completo ou apelido como o usuário prefere ser chamado durante o acompanhamento"
        ];
        
        $idade = [
            "type" => "number",
            "description" => "Idade atual do usuário em anos completos"
        ];
        
        $genero = [
            "type" => "string",
            "enum" => ["masculino", "feminino", "nao_binario", "outro", "prefiro_nao_informar"],
            "description" => "Identidade de gênero do usuário. Importante para ajustes hormonais e fisiológicos no plano"
        ];
        
        $profissao = [
            "type" => "string",
            "description" => "Profissão atual e tipo de trabalho (e.g. Desenvolvedor de Software, Engenheiro de Software, Designer Gráfico, Advogado, Médico, Enfermeiro, Professor, Estudante, etc.)"
        ];

        $cidade_regiao = [
            "type" => "string",
            "description" => "Cidade/estado/país onde mora. Importante para considerar clima, cultura alimentar local e disponibilidade de alimentos."
        ];

        $estado_civil_familia = [
            "type" => "string",
            "description" => "Estado civil e composição familiar (e.g. mora sozinho, com família, tem filhos, etc.). Afeta logística e tempo disponível."
        ];

        $saude = [
            "type" => "object",
            "required" => [
                "condicoes_medicas",
                "medicamentos_uso",
                "lesoes_historico",
                "alergias_alimentares",
                "intolerancias",
                "restricoes_alimentares",
                "historico_familiar",
                "questoes_hormonais"
            ],
            "properties" => [
                "condicoes_medicas" => [
                    "type" => ["array", "null"],
                    "description" => "Lista de condições médicas pré-existentes (diabetes, hipertensão, etc.). Null se não tiver condições médicas pré-existentes.",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da condição médica"
                    ]
                ],
                "medicamentos_uso" => [
                    "type" => "array",
                    "description" => "Lista de medicamentos de uso contínuo que podem afetar treino ou alimentação.",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do medicamento de uso contínuo"
                    ]
                ],
                "lesoes_historico" => [
                    "type" => "array",
                    "description" => "Lista de histórico de lesões ou áreas com limitações de movimento.",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da lesão ou área com limitações de movimento"
                    ]
                ],
                "alergias_alimentares" => [
                    "type" => "array",
                    "description" => "Lista de alergias confirmadas (ex: amendoim, frutos do mar).",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da alergia alimentar"
                    ]
                ],
                "intolerancias" => [
                    "type" => "array",
                    "description" => "Lista de intolerâncias alimentares (ex: lactose, gluten).",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da intolerância alimentar"
                    ]
                ],
                "restricoes_alimentares" => [
                    "type" => "array",
                    "description" => "Lista de restrições alimentares por escolha (e.g. vegetariano, vegano, kosher, halal, etc.).",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da restrição alimentar"
                    ]
                ],
                "historico_familiar" => [
                    "type" => ["string", "null"],
                    "description" => "Condições relevantes na família (e.g. obesidade, diabetes, cardiopatias). Null se não tiver histórico familiar relevante.",
                ],
                "questoes_hormonais" => [
                    "type" => ["string", "null"],
                    "description" => "Somente para mulheres: questões hormonais relevantes para planejamento (ex: gestação, menopausa, etc.). Null se não houver questões hormonais relevantes.",
                ]
            ],
            "additionalProperties" => false
        ];

        $dados_fisicos = [
            "type" => "object",
            "required" => [
                "altura_cm", 
                "peso_atual_kg",
                "peso_meta_kg",
                "percentual_gordura_atual",
                "percentual_gordura_meta",
                "medidas_acompanhar",
            ],
            "properties" => [
                "altura_cm" => [
                    "type" => ["number", "null"],
                    "description" => "Altura em centímetros. Null se não tiver altura."
                ],
                "peso_atual_kg" => [
                    "type" => ["number", "null"],
                    "description" => "Peso corporal atual em quilogramas. Null se não tiver peso atual."
                ],
                "peso_meta_kg" => [
                    "type" => ["number", "null"],
                    "description" => "Peso objetivo em kg. Null se não tiver meta específica de peso."
                ],
                "percentual_gordura_atual" => [
                    "type" => ["number", "null"],
                    "description" => "Percentual de gordura corporal atual, se conhecido (0-50). Null se não tiver percentual de gordura atual."
                ],
                "percentual_gordura_meta" => [
                    "type" => ["number", "null"],
                    "description" => "Percentual de gordura objetivo, se tiver essa meta específica (0-50). Null se não tiver meta específica de percentual de gordura."
                ],
                "medidas_acompanhar" => [
                    "type" => "array",
                    "description" => "Lista de medidas corporais a serem acompanhadas (e.g. cintura, braços, coxas, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da medida corporal"
                    ]
                ]
            ],
            "additionalProperties" => false
        ];

        $objetivos = [
            "type" => "object",
            "required" => [
                "objetivo_principal",
                "objetivo_principal_outro",
                "motivacao_principal",
                "prazo_expectativa",
                "data_evento",
            ],
            "properties" => [
                "objetivo_principal" => [
                    "type" => "string",
                    "enum" => [
                        "perder_peso", 
                        "ganhar_massa_muscular", 
                        "definicao_corporal", 
                        "saude_geral", 
                        "aumentar_energia", 
                        "performance_esportiva", 
                        "outro"
                    ],
                    "description" => "Objetivo primário que guiará todo o planejamento."
                ],
                "objetivo_principal_outro" => [
                    "type" => ["string", "null"],
                    "description" => "Descrição do objetivo se selecionou 'outro'. Null se não selecionou 'outro'."
                ],
                "motivacao_principal" => [
                    "type" => "string",
                    "description" => "Motivação principal que impulsiona o cliente a alcançar o objetivo."
                ],
                "prazo_expectativa" => [
                    "type" => "string",
                    "enum" => [
                        "urgente_com_data",
                        "3_meses",
                        "6_meses",
                        "sem_pressa"
                    ],
                    "description" => "Prazo desejado ou expectativa temporal para alcançar resultados."
                ],
                "data_evento" => [
                    "type" => ["string", "null"],
                    "description" => "Data específica do evento se selecionou 'urgente_com_data' (formato: YYYY-MM-DD)."
                ],
            ],
            "additionalProperties" => false
        ];


        $experiencia_fitness = [
            "type" => "object",
            "required" => [
                "nivel_experiencia", 
                "anos_experiencia", 
                "tipos_treino_praticados", 
                "tecnicas_conhecidas", 
                "exercicios_preferidos", 
                "exercicios_evita", 
                "motivos_abandono", 
                "experiencias_positivas", 
            ],
            "properties" => [
                "nivel_experiencia" => [
                    "type" => "string",
                    "enum" => ["nunca_treinou", "iniciante", "intermediario", "avancado"],
                    "description" => "Nível geral de experiência com musculação e exercícios."
                ],
                "anos_experiencia" => [
                    "type" => "number",
                    "description" => "Quantidade de anos de experiência com treinos, mesmo que intermitente"
                ],
                "tipos_treino_praticados" => [
                    "type" => "array",
                    "description" => "Lista de modalidades já praticadas (musculação, crossfit, corrida, natação, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da modalidade"
                    ]
                ],
                "tecnicas_conhecidas" => [
                    "type" => "array",
                    "description" => "Lista de técnicas de treino que conhece (drop-set, super-série, rest-pause, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome da técnica de treino"
                    ]
                ],
                "exercicios_preferidos" => [
                    "type" => "array",
                    "description" => "Lista de exercícios ou tipos de treino que gosta de fazer. (e.g. supino, agachamento, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do exercício ou tipo de treino"
                    ]
                ],
                "exercicios_evita" => [
                    "type" => "array",  
                    "description" => "Lista de exercícios ou tipos de treino que não gosta de fazer. (e.g. legpress, supino na máquina, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do exercício ou tipo de treino"
                    ]
                ],
                "motivos_abandono" => [
                    "type" => ["string", "null"],
                    "description" => "Motivo que levaram o cliente a abandonar treinos anteriores. Null se não abandonou treinos anteriores."
                ],
                "experiencias_positivas" => [
                    "type" => ["string", "null"],
                    "description" => "O que funcionou bem em experiências anteriores. (e.g. melhoria de saúde, aumento de energia, etc.). Null se não tiver experiências positivas que possam ser aproveitadas."
                ]
            ],
            "additionalProperties" => false
        ];

        $habitos_alimentares = [
            "type" => "object",
            "required" => [
                "refeicoes_por_dia",
                "rotina_alimentar_detalhada",
                "cozinha_em_casa",
                "quem_faz_compras",
                "alimentos_preferidos",
                "alimentos_nao_gosta",
                "hidratacao_diaria_ml",
                "consumo_alcool",
                "suplementos_atuais",
                "contexto_social_alimentar",

            ],
            "properties" => [
                "refeicoes_por_dia" => [
                    "type" => "number",
                    "description" => "Número típico de refeições diárias incluindo lanches."
                ],
                "rotina_alimentar_detalhada" => [
                    "type" => "string",
                    "description" => "Descrição detalhada de um dia típico de alimentação com horários e exemplos."
                ],
                "cozinha_em_casa" => [
                    "type" => "boolean", 
                    "description" => "Indica se prepara suas próprias refeições ou come principalmente fora."
                ],
                "quem_faz_compras" => [
                    "type" => "string",
                    "description" => "Quem é responsável pelas compras de alimentos em casa."
                ],
                "alimentos_preferidos" => [
                    "type" => "array",
                    "description" => "Lista de alimentos preferidos (e.g. sushi, hamburguer, sorvete, picanha, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do alimento preferido"
                    ]
                ],
                "alimentos_nao_gosta" => [
                    "type" => "array",
                    "description" => "Lista de alimentos que não gosta (e.g. abacaxi, alface, repolho, coentro,etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do alimento que não gosta"
                    ]
                ],
                "hidratacao_diaria_ml" => [
                    "type" => ["number", "null"],
                    "description" => "Quantidade de água que bebe por dia em ml. Null se não souber."
                ],
                "consumo_alcool" => [
                    "type" => "string",
                    "enum" => ["nunca", "social", "regular", "etc."],
                    "description" => "Frequência e quantidade de consumo de álcool (e.g. nunca, social, regular, etc.)."
                ],
                "suplementos_atuais" => [
                    "type" => "array",
                    "description" => "Lista de suplementos utilizados atualmente (e.g. whey, creatina, etc.)",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do suplemento"
                    ]
                ],
                "contexto_social_alimentar" => [
                    "type" => "string",
                    "description" => "Como as refeições em família/trabalho/sociais afetam a dieta."
                ]
            ],
            "additionalProperties" => false
        ];


        $estilo_vida = [
            "type" => "object",
            "required" => [
                "horario_acordar", 
                "horario_dormir",
                "horas_sono_media",
                "qualidade_sono",
                "nivel_estresse",
                "nivel_atividade_diaria",
                "viagens_frequencia"
            ],
            "properties" => [
                "horario_acordar" => [
                    "type" => "string",
                    "description" => "Horário típico de acordar no formato HH:MM.",
                ],
                "horario_dormir" => [
                    "type" => "string",
                    "description" => "Horário típico de dormir no formato HH:MM.",
                ],
                "horas_sono_media" => [
                    "type" => "number",
                    "description" => "Média de horas de sono por noite."
                ],
                "qualidade_sono" => [
                    "type" => "string",
                    "enum" => ["pessima", "ruim", "regular", "boa", "excelente"],
                    "description" => "Auto-avaliação da qualidade do sono."
                ],
                "nivel_estresse" => [
                    "type" => "number",
                    "description" => "Nível de estresse geral na vida (escala 1-10).",
                ],
                "nivel_atividade_diaria" => [
                    "type" => "string",
                    "enum" => ["muito_sedentario", "sedentario", "levemente_ativo", "moderadamente_ativo", "muito_ativo"],
                    "description" => "Nível de atividade física no dia a dia sem contar exercícios."
                ],
                "viagens_frequencia" => [
                    "type" => ["string", "null"],
                    "description" => "Frequência de viagens a trabalho ou lazer que podem afetar rotina. Null se não viaja."
                ]
            ],
            "additionalProperties" => false
        ];

       
        $logistica_treino = [
            "type" => "object", 
            "required" => [
                "local_treino",
                "horarios_disponiveis",
                "dias_semana_disponiveis",
                "num_dias_treino_disponiveis",
                "tempo_por_sessao_min",
                "equipamentos_disponiveis",
                "preferencia_treino",
                "orcamento_mensal"
            ],
            "properties" => [
                "local_treino" => [
                    "type" => "string",
                    "enum" => ["academia", "casa", "ar_livre", "misto"],
                    "description" => "Local principal onde pretende treinar."
                ],
                "horarios_disponiveis" => [
                    "type" => "array",
                    "description" => "Lista de horários disponíveis para treino (manhã, tarde, noite).",
                    "items" => [
                        "type" => "string",
                        "enum" => ["manha", "tarde", "noite"],
                        "description" => "Horário disponível para treino."
                    ]
                ],
                "dias_semana_disponiveis" => [
                    "type" => "array",
                    "description" => "Lista de dias da semana disponíveis para treino (segunda a domingo).",
                    "items" => [
                        "type" => "string",
                        "enum" => ["segunda", "terca", "quarta", "quinta", "sexta", "sabado", "domingo"],    
                        "description" => "Dia da semana disponível para treino."
                    ]
                ],
                "num_dias_treino_disponiveis" => [
                    "type" => "number",
                    "description" => "Número realista de dias por semana para treinar (1-7).",
                    "enum" => [1, 2, 3, 4, 5, 6, 7]
                ],
                "tempo_por_sessao_min" => [
                    "type" => "number",
                    "description" => "Tempo disponível por sessão de treino em minutos (e.g. 30, 45, 60, 90, etc.)."
                ],
                "equipamentos_disponiveis" => [
                    "type" => "array",
                    "description" => "Lista de equipamentos disponíveis para treino (e.g. barra, halteres, step, etc.).",
                    "items" => [
                        "type" => "string",
                        "description" => "Nome do equipamento disponível."
                    ]
                ],
                "preferencia_treino" => [
                    "type" => "string",
                    "enum" => ["peso_livre", "maquina", "funcional", "misto"],
                    "description" => "Preferência de treino (e.g. peso livre, máquina, funcional, etc.)."
                ],
                "orcamento_mensal" => [
                    "type" => "string",
                    "description" => "Orçamento mensal disponível para academia, suplementos e alimentação especial."
                ]
            ],
            "additionalProperties" => false
        ];

       
        $desafios_barreiras = [
            "type" => "object",
            "required" => [
                "desafio_principal",
                "desafio_principal_outro",
                "nivel_apoio_social",
                "estrategias_sucesso",
                "estrategia_sucesso_outra"

            ],
            "properties" => [
                "desafio_principal" => [
                    "type" => "string",
                    "enum" => [
                        "falta_tempo",
                        "compulsao_alimentar",
                        "falta_motivacao",
                        "vida_social",
                        "rotina_desorganizada",
                        "outro"
                    ],
                    "description" => "Principal obstáculo que enfrenta com alimentação saudável e treinos regulares."
                ],
                "desafio_principal_outro" => [
                    "type" => ["string", "null"],
                    "description" => "Descrição do desafio principal se selecionou 'outro'. Null se não selecionou 'outro'."
                ],
                "nivel_apoio_social" => [
                    "type" => "string",
                    "enum" => [
                        "total",
                        "parcial",
                        "sem_apoio",
                        "prefiro_nao_envolver"
                    ],
                    "description" => "Nível de apoio de família e amigos para os objetivos de saúde."
                ],
                "estrategias_sucesso" => [
                    "type" => "array",
                    "description" => "Lista de estratégias que funcionaram bem no passado, mesmo que temporariamente.",
                    "items" => [
                        "type" => "string",
                        "enum" => [
                            "plano_estruturado",
                            "acompanhamento_frequente",
                            "treinar_acompanhado",
                            "meal_prep",
                            "metas_com_prazo",
                            "outro"
                        ],
                        "description" => "Nome da estratégia que funcionou bem."
                    ]
                ],
                "estrategia_sucesso_outra" => [
                    "type" => ["string", "null"],
                    "description" => "Descrição da estratégia que funcionou bem se selecionou 'outro'. Null se não selecionou 'outro'."
                ]
            ],
            "additionalProperties" => false
        ];

        $preferencias_acompanhamento = [
            "type" => "object",
            "required" => [
                "preferencia_rotina_variedade",
                "frequencia_acompanhamento",
                "tipo_mensagens",
                "horario_notificacoes",
                "estilo_coach"
            ],
            "properties" => [
                "preferencia_rotina_variedade" => [
                    "type" => "string",
                    "enum" => ["rotina_fixa", "variedade_moderada", "muita_variedade"],
                    "description" => "Preferência entre rotina fixa ou variedade nos treinos e refeições para evitar monotonia."
                ],
                "frequencia_acompanhamento" => [
                    "type" => "string", 
                    "enum" => ["diario", "semanal", "quinzenal", "mensal"],
                    "description" => "Frequência desejada de check-ins e lembretes do coach virtual (diário = no pé todos os dias, mensal = mais independência)."
                ],
                "tipo_mensagens" => [
                    "type" => "string",
                    "enum" => [
                        "lembretes_diretos",
                        "motivacao_positiva",
                        "desafios",
                        "dicas_educativas",
                        "misto"
                    ],
                    "description" => "Tipo de mensagens que mais motivam: lembretes diretos, motivação positiva, desafios, dicas educativas ou mix de todos. Coloque 'misto' se não souber."
                ],
                "horario_notificacoes" => [
                    "type" => "array",
                    "description" => "Lista de momentos do dia preferido para receber notificações do Coach Kivvo.",
                    "items" => [
                        "type" => "string",
                        "enum" => ["manha_cedo", "meio_dia", "meio_tarde", "final_do_dia"],
                        "description" => "Momento do dia preferido para receber notificações. Coloque 'manha_cedo' se não souber."
                    ]
                ],
                "estilo_coach" => [
                    "type" => "string",
                    "enum" => ["apenas_incentivo", "incentivo_com_lembretes", "equilibrado", "cobranca_moderada", "coach_exigente"],
                    "description" => "Estilo de coaching preferido: apenas_incentivo (só positividade), incentivo_com_lembretes (suave), equilibrado (mix), cobranca_moderada (firme), coach_exigente (máxima cobrança)."
                ],
          
            ],
            "additionalProperties" => false
        ];

        $informacoes_adicionais = [
            "type" => ["string", "null"],
            "description" => "Informação adicional relevante compartilhada pelo usuário não coberta nas perguntas anteriores. Null se não houver informações adicionais."
        ];

        $confirmacao_dados = [
            "type" => "boolean",
            "description" => "Confirmação explícita do usuário de que revisou e aprovou todos os dados coletados."
        ];

        $expectativas_alinhamento = [
            "type" => "object",
            "required" => [
                "expectativa_primeiros_resultados",
                "entende_processo_nao_linear",
                "compromisso_mudanca",
            ],
            "properties" => [
                "expectativa_primeiros_resultados" => [
                    "type" => "string",
                    "description" => "Quando espera ver os primeiros resultados."
                ],
                "entende_processo_nao_linear" => [
                    "type" => "boolean",
                    "description" => "Se compreende que o progresso tem altos e baixos."
                ],
                "compromisso_mudanca" => [
                    "type" => "string",
                    "description" => "Nível de comprometimento com mudanças de estilo de vida."
                ]
            ],
            "additionalProperties" => false
        ];

        $resumo = [
            "type" => "string",
            "description" => "Resumo executivo do briefingcom até 120 palavras, destacando: perfil do usuário, objetivo, escopo e principais desafios."
        ];

        $descricao = [
            "type" => "string",
            "description" => "Descrição detalhada e contextualizada do briefing (até 1200 palavras) incluindo: perfil do usuário, objetivos, experiencia fitness, habitos alimentares, objetivos específicos, logistica de treino, desafios, preferencias de acompanhamento, expectativas de alinhamento, informações adicionais, etc."
        ];


        $processar_dados_briefing = [
            'type' => 'function',
            "name" => "processar_dados_briefing",
            "description" => "Salva os dados coletados durante a entrevista com o usuário para geração do plano personalizado de treino e alimentação. IMPORTANTE: Só chamar esta função após confirmação explícita do usuário de que todos os dados estão corretos.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "required" => [
                    "nome_apelido",
                    "idade",
                    "genero",
                    "profissao",
                    "cidade_regiao",
                    "estado_civil_familia",
                    "saude", // Segunda Categoria 
                    "dados_fisicos", // Terceira Categoria
                    "objetivos", // Quarta Categoria
                    "experiencia_fitness", // Quinta Categoria
                    "habitos_alimentares", // Sexta Categoria
                    "estilo_vida", // Sétima Categoria
                    "logistica_treino", // Oitava Categoria
                    "desafios_barreiras", // Nona Categoria
                    "preferencias_acompanhamento", // Décima Categoria
                    "expectativas_alinhamento", // Déima Primeira Categoria
                    "informacoes_adicionais", // Décima Segunda Categoria
                    "confirmacao_dados", 
                    "resumo",
                    "descricao",
                ],
                "properties" => [
                    "nome_apelido" => $nome_apelido,
                    "idade" => $idade,
                    "genero" => $genero,
                    "profissao" => $profissao,
                    "cidade_regiao" => $cidade_regiao,
                    "estado_civil_familia" => $estado_civil_familia,
                    "saude" => $saude, 
                    "dados_fisicos" => $dados_fisicos, 
                    "objetivos" => $objetivos,
                    "experiencia_fitness" => $experiencia_fitness,
                    "habitos_alimentares" => $habitos_alimentares,
                    "estilo_vida" => $estilo_vida,
                    "logistica_treino" => $logistica_treino,
                    "desafios_barreiras" => $desafios_barreiras,
                    "preferencias_acompanhamento" => $preferencias_acompanhamento,
                    "expectativas_alinhamento" => $expectativas_alinhamento,
                    "informacoes_adicionais" => $informacoes_adicionais,
                    "confirmacao_dados" => $confirmacao_dados,
                    "resumo" => $resumo,
                    "descricao" => $descricao,
                ],
                "additionalProperties" => false
            ]
        ];

        return $processar_dados_briefing;
    }

    private function getInputSystemProcessandoDadosBriefing()
    {
        return [
            'role' => 'system',
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => 'Chame imediatamente a função `processar_dados_briefing` para salvar os dados coletados durante a entrevista com o usuário.'
                ]
            ]
        ];
    }

    private function getFunctionCriarPlanoAlimentacao()
    {
        $descricao = [
            "type" => "string",
            "description" => "Descrição detalhada do plano de alimentação e do pensamento utilizado para conseguir cumprir os objetivos do usuário."
        ];

        $calorias_alvo_dia = [
            "type" => "number",
            "description" => "Meta calórica diária calculada para o usuário."
        ];

        $macro_alvos = [
            "type" => "object",
            "required" => [
                "proteina_g",
                "carboidrato_g",
                "gordura_g",
            ],
            "properties" => [
                "proteina_g" => [
                    "type" => "number",
                    "description" => "Gramas de proteína por dia."
                ],
                "carboidrato_g" => [
                    "type" => "number",
                    "description" => "Gramas de carboidrato por dia."
                ],
                "gordura_g" => [
                    "type" => "number",
                    "description" => "Gramas de gordura por dia."
                ]
            ],
            "additionalProperties" => false
        ];

        $alimentacao = [
            "type" => "array",
            "description" => "Lista detalhada de refeições do dia.",
            "items" => [
                "type" => "object",
                "required" => [
                    "refeicao",
                    "itens",
                    "kcal_total"
                ],
                "properties" => [
                    "refeicao" => [
                        "type" => "string",
                        "description" => "Nome e horário da refeição (ex: 'Café da manhã · 07:00')."
                    ],
                    "itens" => [
                        "type" => "array",
                        "description" => "Lista de alimentos da refeição.",
                        "items" => [
                            "type" => "object",
                            "required" => [
                                "alimento",
                                "quantidade",
                                "kcal"
                            ],
                            "properties" => [
                                "alimento" => [
                                    "type" => "string",
                                    "description" => "Nome do alimento (ex: Ovos inteiros, Arroz integral, Feijão preto, Pão integral, Azeite de oliva, Patinho moído, Brócolis no vapor)."
                                ],
                                "quantidade" => [
                                    "type" => "string",
                                    "description" => "Quantidade com unidade (ex: 150 g, 2 fatias, 1 xícara, 1 copo, 1 colher de sopa, 1 colher de chá, 1 unidade)."
                                ],
                                "kcal" => [
                                    "type" => "number",
                                    "description" => "Calorias do alimento."
                                ]
                            ],
                            "additionalProperties" => false
                        ]
                    ],
                    "kcal_total" => [
                        "type" => "number",
                        "description" => "Total de calorias da refeição."
                    ]
                ],
                "additionalProperties" => false
            ],
        ];

        $dia_plano = [
            "type" => "object",
            "required" => [
                "alimentacao",
                "kcal_dia"
            ],
            "properties" => [
                "alimentacao" => $alimentacao,
                "kcal_dia" => [
                    "type" => "number",
                    "description" => "Total de calorias do dia."
                ],
            ],
            "additionalProperties" => false
        ];

        $dias = [
            "type" => "object",
            "required" => [
                "segunda",
                "terca",
                "quarta",
                "quinta",
                "sexta",
                "sabado",
                "domingo",
            ],
            "properties" => [
                "segunda" => $dia_plano,
                "terca" => $dia_plano,
                "quarta" => $dia_plano,
                "quinta" => $dia_plano,
                "sexta" => $dia_plano,
                "sabado" => $dia_plano,
                "domingo" => $dia_plano,
            ],
            "additionalProperties" => false
        ];

        $alimentos_subtitutos = [
            "type" => "array",
            "description" => "Lista de alimentos com suas alternativas para variação do cardápio.",
            "items" => [
              "type" => "object",
              "required" => [
                "alimento", 
                "alternativas"
              ],
              "properties" => [
                "alimento" => [
                  "type" => "string",
                  "description" => "Alimento principal do plano."
                ],
                "alternativas" => [
                  "type" => "array",
                  "description" => "Lista de alimentos que podem substituir o principal mantendo valores nutricionais similares.",
                  "items" => [
                    "type" => "string",
                    "description" => "Nome do alimento substituto."
                  ]
                ]
              ],
              "additionalProperties" => false
            ],
        ];

        $observacoes_gerais = [
            "type" => "string",
            "description" => "Orientações gerais sobre o plano de alimentação, hidratação, suplementação, etc."
        ];

        $criar_plano_alimentar = [
            'type' => 'function',
            "name" => "criar_plano_alimentar",
            "description" => "Cria o plano personalizado de alimentação com base nos dados coletados durante a entrevista com o usuário.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "required" => [
                    "descricao",
                    "calorias_alvo_dia",
                    "macro_alvos",
                    "dias",
                    "alimentos_subtitutos",
                    "observacoes_gerais",
                ],
                "properties" => [
                    "descricao" => $descricao,
                    "calorias_alvo_dia" => $calorias_alvo_dia,
                    "macro_alvos" => $macro_alvos,
                    "dias" => $dias,
                    "alimentos_subtitutos" => $alimentos_subtitutos,
                    "observacoes_gerais" => $observacoes_gerais,
                ],
                "additionalProperties" => false
            ]
        ];

        return $criar_plano_alimentar;
    }

    private function getInputSystemCriarPlanoAlimentacao(Briefing $briefing): array
    {
        // 1. Serializa o array/objeto em JSON (mantém acentos)
        $dadosBriefing = json_encode(
            $briefing->dados_briefing,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        // 2. Monta o array que vai para a OpenAI
        return [
            'role'    => 'system',
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => "Siga as instruções e chame imediatamente a função 'criar_plano_alimentar'. "
                            . "Este JSON contém o levantamento completo do usuário para elaboração do plano de alimentação: "
                            . $dadosBriefing,
                ],
            ],
        ];
    }

    public function getFunctionCriarPlanoTreino()
    {
        $descricao = [
            "type" => "string",
            "description" => "Descrição detalhada do plano de treino e do pensamento utilizado para conseguir cumprir os objetivos do usuário."
        ];

        $treino = [
            "type" => "object",
            "required" => [
                "tipo", 
                "exercicios"
            ],
            "properties" => [
              "tipo" => [
                "type" => "string",
                "description" => "Tipo de treino (ex: 'Push', 'Pull', 'Legs', 'Full Body', 'Peito e Tríceps', 'Costas e Biceps', 'Descanso ativo', 'Rest completo')."
              ],
              "exercicios" => [
                "type" => "array",
                "description" => "Lista de exercícios do treino do dia.",
                "items" => [
                  "type" => "object",
                  "required" => [
                    "nome", 
                    "series", 
                    "reps",
                    "obs"
                  ],
                  "properties" => [
                    "nome" => [
                      "type" => "string",
                      "description" => "Nome do exercício."
                    ],
                    "series" => [
                      "type" => "integer",
                      "description" => "Número de séries.",
                    ],
                    "reps" => [
                      "type" => "string",
                      "description" => "Repetições ou tempo (ex: '8-12', '40 s', '5 min')."
                    ],
                    "obs" => [
                      "type" => ["string", "null"],
                      "description" => "Observações sobre execução, RPE, FC, etc."
                    ],
                  ],
                "additionalProperties" => false
                ]
              ]
            ],
            "additionalProperties" => false
        ];

        $dia_plano = [
            "type" => "object",
            "required" => [
                "treino",
            ],
            "properties" => [
                "treino" => $treino,
            ],
            "additionalProperties" => false
        ];

        $dias = [
            "type" => "object",
            "required" => [
                "segunda",
                "terca",
                "quarta",
                "quinta",
                "sexta",
                "sabado",
                "domingo",
            ],
            "properties" => [
                "segunda" => $dia_plano,
                "terca" => $dia_plano,
                "quarta" => $dia_plano,
                "quinta" => $dia_plano,
                "sexta" => $dia_plano,
                "sabado" => $dia_plano,
                "domingo" => $dia_plano,
            ],
            "additionalProperties" => false
        ];


        $observacoes_gerais = [
            "type" => "string",
            "description" => "Orientações gerais sobre o plano de treino, descanso, repetições, etc."
        ];

        $criar_plano_treino = [
            'type' => 'function',
            "name" => "criar_plano_treino",
            "description" => "Cria o plano personalizado de treino com base nos dados coletados durante a entrevista com o usuário.",
            "strict" => true,
            "parameters" => [
                "type" => "object",
                "required" => [
                    "descricao",
                    "dias",
                    "observacoes_gerais",
                ],
                "properties" => [
                    "descricao" => $descricao,
                    "dias" => $dias,
                    "observacoes_gerais" => $observacoes_gerais,
                ],
                "additionalProperties" => false
            ]
        ];

        return $criar_plano_treino;
    }

    private function getInputSystemCriarPlanoTreino(Briefing $briefing)
    {
        // 1. Serializa o array/objeto em JSON (mantém acentos)
        $dadosBriefing = json_encode(
            $briefing->dados_briefing,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        return [
            'role' => 'system',
            'content' => [
                [
                    'type' => 'input_text', 
                    'text' => "Siga as instruções e chame imediatamente a função 'criar_plano_treino'. Esse json contém o levantamento completo do usuário para elaboração do plano de treino: ${dadosBriefing}"
                ]
            ]
        ];
    }

   
}