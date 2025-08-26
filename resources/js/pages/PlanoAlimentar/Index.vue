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
    planoAlimentar: {
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

const temPlanoAlimentacao = computed(() => {
    return props.planoAlimentar !== null;
});

const getDiaPlano = (dia) => {
    return props.planoAlimentar?.dias[dia] || null;
};

const formatarHora = (refeicao) => {
    return refeicao.split('·')[1].trim();
};

const formatarNomeRefeicao = (refeicao) => {
    return refeicao.split('·')[0].trim();
};
</script>

<template>
    <Head title="Plano Alimentar" />

    <AppLayout>
        <div class="container mx-auto py-6">
            <!-- Seção de Informações Gerais -->
            <div v-if="temPlanoAlimentacao" class="mb-8">
                <Card class="dark:bg-gunmetal-800/50 bg-branco-100">
                    <CardHeader class="flex flex-row items-stretch justify-between space-y-0 pb-2">
                        <div>
                            <CardTitle class="text-xl font-bold">Seu Plano Alimentar</CardTitle>
                            <CardDescription class="text-sm text-cinza-600 dark:text-branco/70 mt-2">{{ planoAlimentar.descricao }}</CardDescription>
                        </div>
                        <a 
                            v-if="briefing.url_plano_alimentar" 
                            :href="route('plano-alimentar.baixar', briefing.id)"
                            class="flex items-center gap-2 bg-verde-primario text-gunmetal-800 px-3 py-2 rounded-lg text-sm font-semibold self-stretch"
                            title="Baixar Plano Alimentar"
                        >
                            <Download class="w-5 h-5" />
                            Baixar
                        </a>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-cinza-600 dark:text-branco/70">Calorias Diárias</h3>
                                <p class="text-2xl font-bold dark:text-primaria-500 text-primaria-800">{{ planoAlimentar.calorias_alvo_dia }} kcal</p>
                            </div>
                            <div class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-cinza-600 dark:text-branco/70">Macro Alvos (por dia)</h3>
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm">Proteínas: <span class="font-semibold text-primaria-800 dark:text-primaria-500">{{ planoAlimentar.macro_alvos.proteina_g }}g</span></p>
                                    <p class="text-sm">Carboidratos: <span class="font-semibold text-blue-600 dark:text-blue-500">{{ planoAlimentar.macro_alvos.carboidrato_g }}g</span></p>
                                    <p class="text-sm">Gorduras: <span class="font-semibold text-red-600 dark:text-red-500">{{ planoAlimentar.macro_alvos.gordura_g }}g</span></p>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="text-sm font-medium text-cinza-600 dark:text-branco/70">Observações Gerais</h3>
                                <p class="text-sm mt-2">{{ planoAlimentar.observacoes_gerais }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>


            <!-- Seção do Plano Diário -->
            <div v-if="temPlanoAlimentacao">
                <Card class="dark:bg-gunmetal-800/50 bg-branco-100">
                    <CardHeader>
                        <CardTitle>Plano Alimentar Diário</CardTitle>
                        <CardDescription>Seu plano de refeições para cada dia da semana</CardDescription>
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
                                            {{ getDiaPlano(dia).kcal_dia }} kcal
                                        </div>
                                    </div>

                                    <div v-for="(refeicao, index) in getDiaPlano(dia).alimentacao" :key="index"
                                        class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-medium text-cinza-800 dark:text-branco">
                                                    {{ formatarNomeRefeicao(refeicao.refeicao) }}
                                                </h4>
                                                <p class="text-sm text-cinza-600 dark:text-branco/70">
                                                    {{ formatarHora(refeicao.refeicao) }}
                                                </p>
                                            </div>
                                            <span class="text-sm font-medium text-primaria-800 dark:text-primaria-500">
                                                {{ refeicao.kcal_total }} kcal
                                            </span>
                                        </div>

                                        <ul class="space-y-2">
                                            <li v-for="(item, itemIndex) in refeicao.itens" :key="itemIndex"
                                                class="flex justify-between items-center text-sm">
                                                <span class="text-cinza-800 dark:text-branco">
                                                    {{ item.alimento }} ({{ item.quantidade }})
                                                </span>
                                                <span class="text-cinza-600 dark:text-branco/70">
                                                    {{ item.kcal }} kcal
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </CardContent>
                </Card>
            </div>

            <!-- Seção de Alimentos Substitutos -->
            <div v-if="temPlanoAlimentacao" class="my-8">
                <Card class="dark:bg-gunmetal-800/50 bg-branco-100">
                    <CardHeader>
                        <CardTitle>Alimentos Substitutos</CardTitle>
                        <CardDescription>Alternativas para substituir alimentos do plano</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="(substituto, index) in planoAlimentar.alimentos_subtitutos" :key="index" 
                                 class="bg-white dark:bg-gunmetal-800 p-4 rounded-lg">
                                <h3 class="font-medium text-cinza-800 dark:text-branco">{{ substituto.alimento }}</h3>
                                <ul class="mt-2 space-y-1">
                                    <li v-for="(alternativa, altIndex) in substituto.alternativas" :key="altIndex"
                                        class="text-sm text-cinza-600 dark:text-branco/70">
                                        • {{ alternativa }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Mensagem quando não há plano alimentar -->
            <div v-else class="flex flex-col items-center justify-center py-12">
                <Alert class="max-w-2xl">
                    <InfoIcon class="h-4 w-4" />
                    <AlertTitle>Plano Alimentar não encontrado</AlertTitle>
                    <AlertDescription class="mt-2">
                        Para receber seu plano alimentar personalizado, você precisa primeiro fazer a entrevista com o Coach Kivvo.
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
