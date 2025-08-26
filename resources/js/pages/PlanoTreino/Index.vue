<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { InfoIcon, ChevronRight, Download } from 'lucide-vue-next';

const props = defineProps({
    planoTreino: {
        type: Object,
        default: null
    },
    briefing: {
        type: Object,
        default: null
    }
});

const diasSemana = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
const diaAtual = computed(() => {
    const dias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
    return dias[new Date().getDay()];
});

const diaSelecionado = ref(diaAtual.value);

const temPlanoTreino = computed(() => {
    return props.planoTreino !== null;
});

const getDiaPlano = (dia) => {
    return props.planoTreino?.dias[dia] || null;
};
</script>

<template>
    <Head title="Plano de Treino" />

    <AppLayout>
        <div class="container mx-auto py-6">
            <!-- Seção de Informações Gerais -->
            <div v-if="temPlanoTreino" class="mb-8">
                <Card class="dark:bg-gunmetal-800/50 bg-branco-100">
                    <CardHeader class="flex flex-row items-stretch justify-between space-y-0 pb-2">
                        <div>
                            <CardTitle class="text-xl font-bold">Seu Plano de Treino</CardTitle>
                            <CardDescription class="text-sm text-cinza-600 dark:text-branco/70 mt-2">{{ planoTreino.descricao }}</CardDescription>
                        </div>
                        <a 
                            v-if="briefing.url_plano_treino" 
                            :href="route('plano-treino.baixar', briefing.id)"
                            class="flex items-center gap-2 bg-verde-primario text-gunmetal-800 px-3 py-2 rounded-lg text-sm font-semibold self-stretch"
                            title="Baixar Plano de Treino"
                        >
                            <Download class="w-5 h-5" />
                            Baixar
                        </a>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-cinza-600 dark:text-branco/70">Observações Gerais</h3>
                                <p class="text-sm mt-2">{{ planoTreino.observacoes_gerais }}</p>
                            </div>
                            <div class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-cinza-600 dark:text-branco/70">Dias de Treino</h3>
                                <div class="mt-2 space-y-1">
                                    <p v-for="dia in diasSemana" :key="dia" class="text-sm">
                                        {{ dia.charAt(0).toUpperCase() + dia.slice(1) }}: 
                                        <span class="font-semibold text-primaria-900 dark:text-primaria-500">
                                            {{ planoTreino.dias[dia].treino.tipo }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Seção do Plano Diário -->
            <div v-if="temPlanoTreino">
                <Card class="dark:bg-gunmetal-800/50 bg-branco-100">
                    <CardHeader>
                        <CardTitle>Plano de Treino Diário</CardTitle>
                        <CardDescription>Seu plano de exercícios para cada dia da semana</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Tabs v-model="diaSelecionado" class="w-full">
                            <TabsList class="grid grid-cols-7 w-full gap-1">
                                <TabsTrigger v-for="dia in diasSemana" :key="dia" :value="dia"
                                    class="capitalize cursor-pointer border border-branco-400 dark:border-gunmetal-600 dark:data-[state=inactive]:bg-gunmetal-800 data-[state=inactive]:hover:bg-primaria-50 data-[state=inactive]:dark:hover:bg-gunmetal-600 data-[state=inactive]:bg-white data-[state=inactive]:text-gunmetal-800 data-[state=active]:bg-primaria-500 data-[state=active]:dark:bg-primaria-500 data-[state=active]:dark:text-gunmetal-800 data-[state=active]:text-gunmetal-800">
                                    {{ dia.slice(0, 3) }}
                                </TabsTrigger>
                            </TabsList>

                            <TabsContent v-for="dia in diasSemana" :key="dia" :value="dia">
                                <div v-if="getDiaPlano(dia)" class="space-y-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold capitalize">{{ dia }}</h3>
                                        <div class="text-lg font-semibold text-cinza-600 dark:text-branco/70">
                                            {{ getDiaPlano(dia).treino.tipo }}
                                        </div>
                                    </div>

                                    <div v-if="getDiaPlano(dia).treino.exercicios.length > 0">
                                        <div v-for="(exercicio, index) in getDiaPlano(dia).treino.exercicios" :key="index"
                                            class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg mb-4">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h4 class="font-medium text-cinza-800 dark:text-branco">
                                                        {{ exercicio.nome }}
                                                    </h4>
                                                    <div class="flex gap-4 mt-1">
                                                        <span class="text-sm text-cinza-600 dark:text-branco/70">
                                                            {{ exercicio.series }} séries
                                                        </span>
                                                        <span class="text-sm text-cinza-600 dark:text-branco/70">
                                                            {{ exercicio.reps }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <p v-if="exercicio.obs" class="text-sm text-cinza-600 dark:text-branco/70 italic mt-2">
                                                <span class="font-medium">Obs:</span> {{ exercicio.obs }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-8 bg-white dark:bg-gunmetal-800 rounded-lg">
                                        <p class="text-cinza-600 dark:text-branco/70">Dia de descanso</p>
                                    </div>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </CardContent>
                </Card>
            </div>

            <!-- Mensagem quando não há plano de treino -->
            <div v-else class="flex flex-col items-center justify-center py-12">
                <Alert class="max-w-2xl">
                    <InfoIcon class="h-4 w-4" />
                    <AlertTitle>Plano de Treino não encontrado</AlertTitle>
                    <AlertDescription class="mt-2">
                        Para receber seu plano de treino personalizado, você precisa primeiro fazer a entrevista com o Coach Kivvo.
                    </AlertDescription>
                </Alert>
                <Button asChild class="mt-6">
                    <Link :href="route('coach-kivvo.index')">
                        Ir para entrevista com Coach Kivvo
                        <ChevronRight class="ml-2 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
