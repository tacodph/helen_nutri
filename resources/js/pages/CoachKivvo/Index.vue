<script setup>
import { ref, nextTick, watch } from 'vue';
import { Head, router, usePage, usePoll } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Mic, Image as ImageIcon, Send, Paperclip, X } from 'lucide-vue-next';
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    briefing: {
        type: Object,
        required: true
    }
});

usePoll(5000, {
    only: ['briefing']
});

const page = usePage();

const user = page.props.auth.user;

// Converte as mensagens do briefing para o formato usado no componente
const convertBriefingMessage = (mensagem) => {
    return {
        id: mensagem.id,
        content: mensagem.texto,
        type: mensagem.origem === 'usuario' ? 'user' : 'ai',
        timestamp: new Date(mensagem.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
        image: mensagem.imagem_url || null // Adiciona suporte a imagem
    };
};

// Inicializa messages com as mensagens do briefing
const messages = ref(props.briefing.mensagens.map(convertBriefingMessage));

const newMessage = ref('');
const fileInput = ref(null);
const textareaRef = ref(null);
const chatContainerRef = ref(null);
const isAiTyping = ref(false);
const selectedImage = ref(null);
const imagePreview = ref(null);
const isUploading = ref(false);

const scrollToBottom = () => {
    nextTick(() => {
        const container = chatContainerRef.value;
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
};

const adjustTextareaHeight = () => {
    const textarea = textareaRef.value;
    if (textarea) {
        // Armazena a altura mínima definida no CSS
        const minHeight = 44; // 2.75rem = 44px
        
        // Reseta a altura para calcular o scrollHeight corretamente
        textarea.style.height = 'auto';
        
        // Calcula a nova altura baseada no conteúdo
        const newHeight = Math.max(textarea.scrollHeight, minHeight);
        
        // Limita a altura máxima a 150px
        const finalHeight = Math.min(newHeight, 150);
        
        // Aplica a altura calculada
        textarea.style.height = `${finalHeight}px`;
    }
};

const handleInput = (event) => {
    const textarea = event.target;
    if (textarea.value.length > 1000) {
        textarea.value = textarea.value.slice(0, 1000);
    }
    newMessage.value = textarea.value;
    adjustTextareaHeight();
};

const handleFileUpload = (event) => {
    const input = event.target;
    if (input.files?.length) {
        const file = input.files[0];
        
        // Validações
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecione apenas arquivos de imagem.');
            return;
        }
        
        // Limita o tamanho da imagem (ex: 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 10MB.');
            return;
        }
        
        selectedImage.value = file;
        
        // Cria preview da imagem
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeSelectedImage = () => {
    selectedImage.value = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const sendMessage = async () => {
    if ((!newMessage.value.trim() && !selectedImage.value) || props.briefing.status === 'desativado' || isUploading.value) return;

    isUploading.value = true;

    // Prepara os dados para envio
    const formData = new FormData();
    formData.append('message', newMessage.value.trim());
    
    if (selectedImage.value) {
        formData.append('image', selectedImage.value);
    }

    // Cria uma nova mensagem com o formato interno
    const novaMensagem = {
        id: Date.now(),
        content: newMessage.value || (selectedImage.value ? 'Imagem enviada' : ''),
        type: 'user',
        timestamp: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
        image: imagePreview.value // Adiciona a imagem à mensagem do usuário
    };

    // Adiciona a mensagem ao array local
    messages.value.push(novaMensagem);

    // Limpa os campos
    newMessage.value = '';
    removeSelectedImage();

    // Mostra o indicador de digitação após um pequeno delay
    setTimeout(() => {
        isAiTyping.value = true;
        scrollToBottom();
    }, 500);

    // Enviar mensagem para o servidor via router
    router.post(route('coach-kivvo.mensagem', {briefing: props.briefing.id}), formData, {
        preserveScroll: true,
        forceFormData: true, // Força o uso de FormData para upload de arquivos
        onSuccess: (page) => {
            isAiTyping.value = false;
            isUploading.value = false;
            const mensagem = page.props.flash.payload;
            console.log('Mensagem recebida da OpenAI', mensagem);
            if (mensagem) {
                //messages.value.push(convertBriefingMessage(mensagem));
            }
        },
        onError: (errors) => {
            isAiTyping.value = false;
            isUploading.value = false;
            console.error('Erro ao enviar mensagem:', errors);
            alert('Erro ao enviar mensagem. Tente novamente.');
        }
    });

    // Reset da altura do textarea para o tamanho de 2 linhas
    const textarea = textareaRef.value;
    if (textarea) {
        const computedStyle = window.getComputedStyle(textarea);
        const lineHeight = parseInt(computedStyle.lineHeight);
        const paddingTop = parseInt(computedStyle.paddingTop);
        const paddingBottom = parseInt(computedStyle.paddingBottom);
        const height = (lineHeight * 2) + paddingTop + paddingBottom;
        textarea.style.height = `${height}px`;
    }

    scrollToBottom();
};

const startRecording = () => {
    // Aqui você pode implementar a gravação de áudio
    console.log('Iniciando gravação de áudio');
};

// Observa mudanças no array de mensagens para rolar automaticamente
watch(messages, () => {
    scrollToBottom();
}, { deep: true });

// Observa mudanças no briefing.mensagens, se o último item da array for da ai, coloca ele numa variável. Compare o valor do texto da mensagem com as últimas 10 mensagens da ai, se não for igual, coloca ele na array de mensagens
watch(() => props.briefing.mensagens, (newMensagens) => {
    console.log("mudou mensagens");
    if (newMensagens[newMensagens.length - 1].origem === 'ai' && newMensagens[newMensagens.length - 1].id !== messages.value[messages.value.length - 1].id) {
        const ultimaMensagem = newMensagens[newMensagens.length - 1];
        const ultimaMensagemAi = messages.value.filter(mensagem => mensagem.type === 'ai').slice(-10);
        if (!ultimaMensagemAi.some(mensagem => mensagem.content === ultimaMensagem.texto)) {
            messages.value.push(convertBriefingMessage(ultimaMensagem));
        }
    }
}, { deep: true });



// Scroll para o final quando a página carregar
onMounted(() => {
    scrollToBottom();
    
    console.log(props.briefing.mensagens);

    // Verifica se já existe alguma mensagem
    if (!props.briefing.mensagens || props.briefing.mensagens.length === 0) {
        // Mostra o indicador de digitação
        isAiTyping.value = true;
        scrollToBottom();

        // Inicia o briefing se não houver mensagens
        router.post(route('coach-kivvo.iniciarBriefing'), {}, {
            preserveScroll: true,
            onSuccess: (page) => {
                const mensagem = page.props.flash.payload;
                if (mensagem) {
                    // Pequeno delay para simular a digitação
                    setTimeout(() => {
                        isAiTyping.value = false;
                        messages.value.push(convertBriefingMessage(mensagem));
                        scrollToBottom();
                    }, 1000);
                } else {
                    isAiTyping.value = false;
                }
            },
            onError: (errors) => {
                console.error('Erro ao iniciar briefing:', errors);
                isAiTyping.value = false;
            }
        });
    }
});
</script>

<template>
    <Head title="Coach Kivvo" />

    <AppLayout>
        <!-- Container principal com altura máxima da viewport menos o header -->
        <div class="container mx-auto h-[calc(100vh-5rem)] py-4">
            <!-- Container do chat com flex column e altura total -->
            <div class="flex h-full flex-col gap-4">
                <!-- Cabeçalho do Chat -->
                <div class="flex items-center gap-4 rounded-lg bg-branco-200 dark:bg-gunmetal-900 p-4 shadow-lg">
                    <div class="flex w-12 h-12 items-center justify-center rounded-full">
                        <img src="/logotipo_fundo_escuro.svg" alt="Coach Kivvo" class="h-12 hidden dark:block" />
                        <img src="/logotipo_fundo_claro.svg" alt="Coach Kivvo" class="h-12 block dark:hidden" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold text-cinza-800 dark:text-branco-300">Coach Kivvo</h2>
                            <div class="flex items-center gap-1.5">
                                <div class="h-2 w-2 rounded-full bg-primaria-800 dark:bg-primaria-500 animate-pulse"></div>
                                <span class="text-xs text-cinza-600 dark:text-branco-300/70">online</span>
                            </div>
                        </div>
                        <p class="text-sm text-cinza-800 dark:text-branco-300">AI Fitness Coach</p>
                    </div>
                </div>

                <!-- Container do chat com flex-1 para ocupar espaço restante -->
                <div class="flex-1 flex flex-col rounded-lg bg-branco-200 dark:bg-gunmetal-900 shadow-lg min-h-0">
                    <!-- Área de mensagens com overflow -->
                    <div 
                        ref="chatContainerRef"
                        class="flex-1 overflow-y-auto p-4 min-h-0 [scrollbar-width:thin] [scrollbar-color:theme('colors.gunmetal.700')_transparent] hover:[scrollbar-color:theme('colors.gunmetal.600')_transparent]"
                    >
                        <div class="space-y-4">
                            <div
                                v-for="message in messages"
                                :key="message.id"
                                :class="[
                                    message.type === 'user' ? 'flex justify-end' : 'flex justify-start',
                                    'px-4 py-2'
                                ]"
                            >
                                <div
                                    :class="[
                                        message.type === 'user'
                                            ? 'max-w-[80%] rounded-2xl rounded-tr-none bg-primaria-600 dark:bg-primaria-900/80 text-gunmetal-900 dark:text-branco-100'
                                            : 'max-w-[80%] rounded-2xl rounded-tl-none bg-branco-300 dark:bg-gunmetal-800 text-gunmetal-900 dark:text-branco-200'
                                    ]"
                                >
                                    <div class="px-4 py-3">
                                        <!-- Imagem da mensagem -->
                                        <div v-if="message.image" class="mb-3">
                                            <img 
                                                :src="message.image" 
                                                alt="Imagem enviada"
                                                class="max-w-full h-auto rounded-lg shadow-sm max-h-[300px]"
                                            />
                                        </div>
                                        <!-- Texto da mensagem -->
                                        <p v-if="message.content" class="text-base">{{ message.content }}</p>
                                    </div>
                                    <div
                                        :class="[
                                            'px-4 pb-2 text-right text-xs',
                                            message.type === 'user' 
                                                ? 'text-gunmetal-900/70 dark:text-branco/70' 
                                                : 'text-gunmetal-800/70 dark:text-branco/70'
                                        ]"
                                    >
                                        {{ message.timestamp }}
                                    </div>
                                </div>
                            </div>

                            <!-- Indicador de digitação da AI -->
                            <div v-if="isAiTyping" class="flex justify-start px-4 py-2">
                                <div class="max-w-[80%] rounded-2xl rounded-tl-none bg-branco-300 dark:bg-gunmetal-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primaria-900/20 dark:bg-primaria-500/20">
                                            <svg class="h-4 w-4 text-primaria-800 dark:text-primaria-500 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gunmetal-800 dark:text-branco/70">Coach Kivvo está digitando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Área de input fixa na parte inferior -->
                    <div class="border-t border-cinza-300 dark:border-gunmetal-700 bg-branco-200 dark:bg-gunmetal-900 p-4">
                        <div class="w-full">
                            <!-- Preview da imagem selecionada -->
                            <div v-if="imagePreview" class="mb-4 relative inline-block">
                                <img 
                                    :src="imagePreview" 
                                    alt="Preview da imagem"
                                    class="max-w-32 h-auto rounded-lg border border-cinza-300 dark:border-gunmetal-700"
                                />
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="absolute -top-2 -right-2 h-6 w-6 bg-destructive hover:bg-destructive/90 text-branco rounded-full"
                                    @click="removeSelectedImage"
                                >
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="w-full">
                                    <textarea
                                        ref="textareaRef"
                                        v-model="newMessage"
                                        :placeholder="briefing.status === 'desativado' ? 'Chat encerrado. Obrigado!' : 'Digite sua mensagem para o Coach Kivvo...'"
                                        :disabled="briefing.status === 'desativado'"
                                        rows="2"
                                        maxlength="1000"
                                        class="w-full min-h-[2.75rem] max-h-[150px] bg-branco-100 dark:bg-fundo-900 border border-cinza-300 dark:border-gunmetal-700 rounded-lg placeholder:text-cinza-500 dark:placeholder:text-branco/50 text-cinza-800 dark:text-branco px-4 py-3 resize-none overflow-hidden focus:outline-none focus:ring-2 focus:ring-verde-primario/30 disabled:opacity-50"
                                        @input="handleInput"
                                        @keydown.enter.prevent="sendMessage"
                                    />
                                </div>
                                    
                                <div class="flex justify-between items-center">
                                    <!-- Caracteres restantes -->
                                    <div 
                                        class="text-left text-xs" 
                                        :class="{ 
                                            'text-destructive': newMessage.length >= 1000, 
                                            'text-cinza-600 dark:text-branco/70': newMessage.length < 1000 
                                        }"
                                    >
                                        {{ newMessage.length }}/1000
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2">
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            class="hidden"
                                            accept="image/*"
                                            @change="handleFileUpload"
                                        />
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-cinza-600 hover:text-cinza-800 dark:text-branco/70 dark:hover:text-branco hover:bg-branco-300 dark:hover:bg-gunmetal-700 disabled:opacity-50"
                                            :disabled="briefing.status !== 'iniciado' || isUploading"
                                            @click="fileInput?.click()"
                                        >
                                            <ImageIcon class="h-5 w-5" />
                                        </Button>

                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-cinza-600 hover:text-cinza-800 dark:text-branco/70 dark:hover:text-branco hover:bg-branco-300 dark:hover:bg-gunmetal-700 disabled:opacity-50"
                                            :disabled="briefing.status !== 'iniciado' || isUploading"
                                            @click="startRecording"
                                        >
                                            <Mic class="h-5 w-5" />
                                        </Button>

                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-cinza-600 hover:text-cinza-800 dark:text-branco/70 dark:hover:text-branco hover:bg-branco-300 dark:hover:bg-gunmetal-700 disabled:opacity-50"
                                            :disabled="briefing.status !== 'iniciado' || isUploading"
                                            @click="sendMessage"
                                        >
                                            <Send class="h-5 w-5" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>