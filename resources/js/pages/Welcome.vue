<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, watch, computed, onUnmounted, nextTick } from 'vue';
import { ArrowDownIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import { MessageSquare, FileText, CheckCircle } from 'lucide-vue-next';
import { motion, useScroll, useMotionValue, useMotionValueEvent, useDomRef, animate } from 'motion-v';
import { type CSSProperties } from 'vue';

const testimonials = [
  {
    name: "Diego Fernando",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "Excelente trabalho! Nutriçao simples, sem terrorismo, com comida de verdade! Atendeu a mim e minha esposa, conseguimos atingir nossas metas de perda de peso e o mais importante, manter o peso por ser uma dieta sustentável! Preço justo pelo trabalho desempenhado! Recomendamos a todos!",
    rating: 5,
    result: "8kg perdidos"
  },
 {
    name: "Douver Barros",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "A melhor nutri que eu poderia ter. Não inventa modinha, é assertiva e monta todo um cardápio que faz sentido para a minha realidade. Existe um eu antes e depois desse acompanhamento. Meu abdômen sempre foi um calcanhar de Aquiles, e consegui feitos absurdos com a dieta dela. Hoje já tiro a camisa sem medo de ser feliz. Ah, e o atendimento em si tbm é excelente. Super alto astral. Recomendo demais.",
    rating: 5,
    result: "8kg perdidos"
  },
  {
    name: "Natalia Alves",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "Sempre tive resistência com nutricionista, tive algumas experiências frustradas, infelizmente. E confesso que a Hellen tirou todas essas más impressões. Profissional comprometida com uma dieta saudável e acessível para o paciente. Sem firula, jogo limpo com o corpo e com a pessoa!",
    rating: 5,
    result: "8kg perdidos"
  },
    {
    name: "Karen Santana Victorino",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "Excelente nutricionista! As consultas são muito esclarecedoras, esclarece todas as dúvidas, o plano alimentar é de acordo com a individualidade de cada um.  Profissional muito atenciosa e querida!",
    rating: 5,
    result: "8kg perdidos"
  },
    {
    name: "Raquel  Lucia",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "Não é apenas mais uma nutricionista aplicando fórmulas de déficit calórico, além de super competente, é uma profissional humana que nos enxerga para além do peso. Melhor decisão da vida!",
    rating: 5,
    result: "8kg perdidos"
  },
    {
    name: "Ana Lucia Paula Alvarenga",
    age: 28,
    role: "Designer",
    url: "/storage/imagens/depoimentos/mariana_thumb.jpg",
    content: "Recomendo!! A Helen é uma nutricionista competente, educada, as dicas de receitas são excelentes ,ela nos conduz à realização de uma dieta alimentar mais saudável, visando alcançar nossos objetivos pessoais.",
    rating: 5,
    result: "8kg perdidos"
  },

];

const faqs = ref([
  {
    question: "Qual é a diferença entre os planos?",
    answer: "A principal diferença está na frequência e duração do acompanhamento. O Plano Mensal é para quem deseja começar e entender Sobre  um plano alimentar personalizado.</br> O Plano Semanal é um acompanhamento intensivo de curto prazo. </br> O Plano Trimestral oferece suporte contínuo durante 3 meses, com consultas quinzenais. Já o Plano Semestral é o mais completo, proporcionando aprendizado e acompanhamento por 6 meses, com encontros mensais.",
    isOpen: false
  },
  {
    question: "Posso começar com um plano e depois mudar para outro?",
    answer: "Sim! Se durante o acompanhamento você sentir necessidade de um suporte mais frequente ou quiser prolongar o acompanhamento, basta conversar diretamente comigo para ajustarmos o plano ideal para você.",
    isOpen: false
  },
  {
    question: "O que acontece se eu não puder comparecer a uma consulta?",
    answer: "Entendo que imprevistos acontecem. Se precisar remarcar, pedimos que avise com no mínimo 24 horas de antecedência para reagendarmos sem custos. Cancelamentos de última hora podem ser considerados como consulta realizada.",
    isOpen: false
  },
  {
    question: "As consultas online têm a mesma eficácia das presenciais?",
    answer: "Sim! O atendimento online é estruturado para oferecer a mesma qualidade e atenção que o presencial. Toda a avaliação nutricional, orientação alimentar, ajustes e suporte são realizados de forma segura e eficaz.",
    isOpen: false
  },
  {
    question: "Quais são as formas de pagamento aceitas?",
    answer: "Aceito pagamentos via Pix e cartão de crédito. Para planos de maior valor, é possível parcelar em até 6 vezes no cartão (com acréscimo das taxas da operadora).",
    isOpen: false
  },
  {
    question: "Vou receber suporte fora das consultas?",
    answer: "O aplicativo DIETBOX dispõe de chat para retirar dúvidas, materiais de apoio, orientações específicas para você, metas e o diário alimentar que é um grande aliado para acompanhar sua adesão ao plano alimentar. Porem é durante as consultas que as dúvidas são esclarecidas com mais especificidade e precisão.",
    isOpen: false
    },
  {
    question: "Preciso ter algum material ou exames para a primeira consulta?",
    answer: "Se tiver exames laboratoriais recentes (até 6 meses), é interessante trazer. Caso não tenha, podemos iniciar normalmente e, se necessário, solicitarei posteriormente.",
    isOpen: false
  },
  {
    question: "Os resultados são garantidos?",
    answer: "O acompanhamento é personalizado para te orientar da melhor maneira possível. Os resultados dependem da sua dedicação e da aplicação prática das estratégias passadas. Meu compromisso é te guiar para que o processo seja o mais leve e eficaz possível.",
    isOpen: false
  },

]);

const messages = ref([
  {
    id: 0,
    type: 'ai',
    text: 'Olá João, tudo bem? 👋 Eu sou o Coach Kivvo, seu assistente inteligente aqui na Kivvo! Sou especialista em treino e nutrição, e estou aqui para criar um plano 100% personalizado para você.',
  },
  {
    id: 0,
    type: 'ai',
    text: 'Pense nessa conversa como um bate-papo com seu personal trainer e nutricionista favoritos ao mesmo tempo. Meu objetivo é te conhecer bem para criar algo que realmente funcione para SEU estilo de vida. Podemos começar? 😊',
  },
  {
    id: 1,
    type: 'user',
    text: 'Opa! Podemos sim.',
  },
  {
    id: 2,
    type: 'ai',
    text: 'Ótimo, João! Fico feliz em te conhecer. Para entender melhor seu dia a dia, me conta um pouco sobre você: Qual sua idade?',
  },
  {
    id: 3,
    type: 'user',
    text: 'Tenho 26 anos.',
  },
  {
    id: 4,
    type: 'ai',
    text: 'Legal, João! E o que você faz profissionalmente? Como é sua rotina de trabalho - é mais corrida ou tranquila?',
  },
  {
    id: 5,
    type: 'user',
    text: 'Sou analista de sistemas. Minha rotina é bem corrida, passo muito tempo no computador e às vezes faço horas extras.',
  },
  {
    id: 6,
    type: 'ai',
    text: 'Entendi, deve ser intenso! E você mora em que cidade ou região?',
  },
  {
    id: 7,
    type: 'user',
    text: 'Moro em São Paulo, capital.',
  },
  {
    id: 8,
    type: 'ai',
    text: 'Uma grande cidade! Isso pode nos dar boas opções de academias. E sobre sua vida pessoal, você mora sozinho, com família ou amigos?',
  },
  {
    id: 9,
    type: 'user',
    text: 'Sou solteiro e moro com meus pais.',
  },
  {
    id: 10,
    type: 'ai',
    text: 'Certo, João. Agora, preciso fazer algumas perguntas importantes sobre sua saúde - é fundamental para criar algo seguro e eficaz para você: Você tem alguma condição médica que eu deveria saber? Toma algum medicamento regularmente?',
  },
  {
    id: 11,
    type: 'user',
    text: 'Não tenho condições médicas graves e não tomo medicamentos.',
  },
  {
    id: 12,
    type: 'ai',
    text: 'Que bom! E sobre lesões, você já teve alguma que ainda te incomoda?',
  },
  {
    id: 13,
    type: 'user',
    text: 'Sim, tenho uma lesão no joelho direito de quando jogava futebol. Ainda sinto um pouco de dor se exagerar.',
  },
  {
    id: 14,
    type: 'ai',
    text: 'Entendi, vamos cuidar desse joelho com atenção. E sobre alimentação, você tem alguma alergia ou intolerância alimentar?',
  },
  {
    id: 15,
    type: 'user',
    text: 'Sou intolerante à lactose. Não posso consumir leite e derivados sem sentir desconforto.',
  },
  {
    id: 16,
    type: 'ai',
    text: 'Ok, anotado! Vamos evitar lactose no seu plano. Tem mais alguma restrição alimentar que eu deva saber?',
  },
  {
    id: 17,
    type: 'user',
    text: 'Não, só a intolerância à lactose mesmo.',
  },
  {
    id: 18,
    type: 'ai',
    text: 'Perfeito. Vamos falar sobre sua relação com exercícios: Você já treinou musculação antes? Como foi a experiência?',
  },
  {
    id: 19,
    type: 'user',
    text: 'Sim, treinei por uns 6 meses há dois anos. Gostei, mas parei por falta de tempo.',
  },
  {
    id: 20,
    type: 'ai',
    text: 'Que legal que você gostou! O que mais te agradou na musculação?',
  },
  {
    id: 21,
    type: 'user',
    text: 'Gostava de sentir que estava ficando mais forte e vendo mudanças no corpo.',
  },
  {
    id: 22,
    type: 'ai',
    text: 'Ótimo! E além da musculação, você já praticou outros tipos de atividade física?',
  },
  {
    id: 23,
    type: 'user',
    text: 'Joguei futebol por anos, mas parei por causa da lesão. Também gosto de correr, mas não faço regularmente.',
  },
  {
    id: 24,
    type: 'ai',
    text: 'Entendi. E atualmente, você faz algum exercício? Mesmo algo leve?',
  },
  {
    id: 25,
    type: 'user',
    text: 'Não, estou sedentário agora. Só caminho um pouco nos fins de semana.',
  },
  {
    id: 26,
    type: 'ai',
    text: 'Sem problemas, vamos começar do ponto onde você está! Sobre seus objetivos: Qual é o principal com treino e alimentação?',
  },
  {
    id: 27,
    type: 'user',
    text: 'Quero perder peso e ganhar massa muscular.',
  },
  {
    id: 28,
    type: 'ai',
    text: 'Ótimos objetivos, João! Quanto peso você gostaria de perder? E tem algum prazo em mente?',
  },
  {
    id: 29,
    type: 'user',
    text: 'Estou com 90kg e quero chegar a 75kg. Não tenho pressa, quero fazer de forma saudável, talvez em 6 meses.',
  },
  {
    id: 30,
    type: 'ai',
    text: 'Perfeito, uma abordagem saudável é o melhor caminho. Qual sua altura, João?',
  },
  {
    id: 31,
    type: 'user',
    text: 'Tenho 1,75m.',
  },
  {
    id: 32,
    type: 'ai',
    text: 'Entendi, vamos trabalhar para alcançar esses 75kg de forma sustentável. E o que te motivou a começar agora?',
  },
  {
    id: 33,
    type: 'user',
    text: 'Estou me sentindo cansado, sem energia e não gosto da minha aparência. Quero mudar isso.',
  },
  {
    id: 34,
    type: 'ai',
    text: 'É uma motivação poderosa, João! Como você se imagina daqui a 6 meses com um plano personalizado?',
  },
  {
    id: 35,
    type: 'user',
    text: 'Quero estar mais magro, com músculos definidos e me sentindo mais confiante.',
  },
  {
    id: 36,
    type: 'ai',
    text: 'Perfeito! Vamos fazer isso acontecer. Agora, sobre sua alimentação atual: Quantas refeições você faz por dia?',
  },
  {
    id: 37,
    type: 'user',
    text: 'Faço três refeições: café da manhã, almoço e jantar.',
  },
  {
    id: 38,
    type: 'ai',
    text: 'E como é um dia típico de alimentação para você? O que você come no café da manhã, por exemplo?',
  },
  {
    id: 39,
    type: 'user',
    text: 'No café da manhã, como pão com queijo e café. Às vezes, tomo suco.',
  },
  {
    id: 40,
    type: 'ai',
    text: 'E no almoço, o que você costuma comer?',
  },
  {
    id: 41,
    type: 'user',
    text: 'Arroz, feijão, carne ou frango, e um pouco de salada. Às vezes, tomo uma sobremesa.',
  },
  {
    id: 42,
    type: 'ai',
    text: 'E no jantar?',
  },
  {
    id: 43,
    type: 'user',
    text: 'O jantar é meu ponto fraco. Sempre acabo comendo besteira: sanduíche, pizza, batata frita, refrigerante... Só coisa boa! 🤦‍♂️.',
  },
  {
    id: 44,
    type: 'ai',
    text: 'Entendi. Vamos dar uma melhorada nisso daí! Quais alimentos você mais gosta? E quais evita?',
  },
  {
    id: 45,
    type: 'user',
    text: 'Gosto de massas, como macarrão e pizza, e churrasco. Evito saladas e vegetais crus, além de lactose.',
  },
  {
    id: 46,
    type: 'ai',
    text: 'Ok, vamos tornar a alimentação saudável mais gostosa para você. Você cozinha em casa ou come mais fora?',
  },
  {
    id: 47,
    type: 'user',
    text: 'Cozinho em casa às vezes, mas como fora no trabalho, em restaurantes por quilo.',
  },
  {
    id: 48,
    type: 'ai',
    text: 'E você bebe bastante água? E álcool, com que frequência?',
  },
  {
    id: 49,
    type: 'user',
    text: 'Bebo pouca água, mais suco ou refrigerante. Tomo cerveja nos fins de semana.',
  },
  {
    id: 50,
    type: 'ai',
    text: 'Vamos melhorar essa hidratação! Agora, sobre treino: Que horários você tem disponível? E onde prefere treinar?',
  },
  {
    id: 51,
    type: 'user',
    text: 'Posso treinar à noite, por volta das 19h. Prefiro academia, tem uma perto de casa.',
  },
  {
    id: 52,
    type: 'ai',
    text: 'Ótimo! Quanto tempo você consegue dedicar por treino? E quantos dias por semana seria realista?',
  },
  {
    id: 53,
    type: 'user',
    text: 'Posso dedicar 1 hora por treino, 4 vezes por semana.',
  },
  {
    id: 54,
    type: 'ai',
    text: 'Perfeito, 4 vezes por semana com 1 hora é excelente. Como está seu sono? Consegue dormir bem?',
  },
  {
    id: 55,
    type: 'user',
    text: 'Durmo umas 7 horas, mas às vezes tenho insônia por estresse.',
  },
  {
    id: 56,
    type: 'ai',
    text: 'Entendi, o sono é importante para os resultados. Vamos falar de desafios: Qual sua maior dificuldade com alimentação saudável?',
  },
  {
    id: 57,
    type: 'user',
    text: 'Resistir a doces, especialmente quando estou estressado.',
  },
  {
    id: 58,
    type: 'ai',
    text: 'Muita gente passa por isso, João. E o que mais te atrapalha na hora de treinar?',
  },
  {
    id: 59,
    type: 'user',
    text: 'O cansaço depois do trabalho e às vezes falta de motivação.',
  },
  {
    id: 60,
    type: 'ai',
    text: 'Vamos trabalhar nisso! E quais situações te fazem querer "furar a dieta"?',
  },
  {
    id: 61,
    type: 'user',
    text: 'Quando estou estressado ou saio com amigos.',
  },
  {
    id: 62,
    type: 'ai',
    text: 'Entendido. Sua família ou amigos apoiam seus objetivos de saúde?',
  },
  {
    id: 63,
    type: 'user',
    text: 'Minha família apoia, mas às vezes oferecem doces. Meus amigos gostam de sair para comer e beber.',
  },
  {
    id: 64,
    type: 'ai',
    text: 'Vamos criar estratégias para essas situações. Sobre preferências: Quais sabores ou culinárias você gosta?',
  },
  {
    id: 65,
    type: 'user',
    text: 'Gosto de comida brasileira e italiana.',
  },
  {
    id: 66,
    type: 'ai',
    text: 'E quais alimentos você não come de jeito nenhum, além de lactose?',
  },
  {
    id: 67,
    type: 'user',
    text: 'Não como frutos do mar.',
  },
  {
    id: 68,
    type: 'ai',
    text: 'Ok! E sobre treinos, você prefere algum tipo específico de exercício?',
  },
  {
    id: 69,
    type: 'user',
    text: 'Prefiro musculação com um pouco de cardio.',
  },
  {
    id: 70,
    type: 'ai',
    text: 'Você prefere um plano mais rígido ou com flexibilidade?',
  },
  {
    id: 71,
    type: 'user',
    text: 'Com alguma flexibilidade, para adaptar se precisar.',
  },
  {
    id: 72,
    type: 'ai',
    text: 'E você gosta de variedade nos treinos ou prefere rotina fixa?',
  },
  {
    id: 73,
    type: 'user',
    text: 'Gosto de variedade para não enjoar.',
  },
  {
    id: 74,
    type: 'ai',
    text: 'Perfeito! E sobre acompanhamento, prefere algo frequente ou mais independência?',
  },
  {
    id: 75,
    type: 'user',
    text: 'Acompanhamento frequente no início, depois posso ser mais independente.',
  },
  {
    id: 76,
    type: 'ai',
    text: 'João, que conversa incrível! Consegui entender muito bem seu perfil, objetivos e estilo de vida. Agora vou criar um plano personalizado que seja: - Realista para sua rotina - Gostoso e sustentável - Focado nos seus objetivos - Adaptado às suas preferências.',
  },
  {
    id: 77,
    type: 'ai',
    text: 'Em breve você receberá: ✅ Plano de treino personalizado ✅ Cardápio nutricional detalhado ✅ Orientações práticas ✅ Cronograma semanal. Vai estar tudo disponível no seu dashboard e também enviarei os PDFs por e-mail, blz? Tem alguma dúvida ou algo que esqueci de perguntar?',
  },
  {
    id: 78,
    type: 'user',
    text: 'Acho que cobrimos tudo. Só uma coisa: pode incluir lanches saudáveis para levar ao trabalho?',
  },
  {
    id: 79,
    type: 'ai',
    text: 'Claro, João! Vou incluir opções práticas e sem lactose para você levar ao trabalho. Algo mais?',
  },
  {
    id: 80,
    type: 'user',
    text: 'Não, é só isso. Estou ansioso para começar!',
  },
  {
    id: 81,
    type: 'ai',
    text: 'Ótimo! Em breve você receberá seu plano. Enquanto isso, se tiver qualquer pergunta, estou aqui. Vamos juntos nessa jornada, João! 💪',
  }
]);

const chatContainer = ref(null);
const currentTypingMessage = ref('');
const isTyping = ref(false);
const nextMessageIndex = ref(0);
const isAITyping = ref(false);

const scrollToBottom = () => {
  if (chatContainer.value) {
    // Força um pequeno delay para garantir que o DOM foi atualizado
    setTimeout(() => {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }, 50);
  }
};

const typeAndSendMessage = async (message) => {
  // Simula um tempo aleatório antes de começar a digitar (entre 500ms e 1500ms)
  await new Promise(resolve => setTimeout(resolve, Math.random() * 1000 + 500));

  // Calcula o tempo de digitação baseado no tamanho da mensagem (entre 1s e 3s)
  const typingTime = Math.min(Math.max(message.text.length * 30, 1000), 3000);

  if (message.type === 'user') {
    // Simula digitação do usuário
    isTyping.value = true;
    scrollToBottom();

    await new Promise(resolve => setTimeout(resolve, typingTime));

    isTyping.value = false;
    message.visible = true;
    scrollToBottom();

    // Delay aleatório antes da resposta da AI (entre 1s e 2s)
    await new Promise(resolve => setTimeout(resolve, Math.random() * 1000 + 1000));
  } else {
    // Se for mensagem da AI, simula digitação
    isAITyping.value = true;
    scrollToBottom();

    await new Promise(resolve => setTimeout(resolve, typingTime));

    isAITyping.value = false;
    message.visible = true;
    scrollToBottom();
  }

  nextMessageIndex.value++;

  // Delay aleatório antes da próxima mensagem (entre 1s e 2s)
  await new Promise(resolve => setTimeout(resolve, Math.random() * 1000 + 1000));
  processNextMessage();
};

const processNextMessage = async () => {
  const currentMessage = messages.value[nextMessageIndex.value];

  if (!currentMessage) return; // Fim da conversa

  await typeAndSendMessage(currentMessage);
};

const startChatAnimation = async () => {
  // Reseta os estados
  messages.value.forEach(m => m.visible = false);
  nextMessageIndex.value = 0;
  isTyping.value = false;
  isAITyping.value = false;
  currentTypingMessage.value = '';

  // Força um pequeno delay antes de começar
  await new Promise(resolve => setTimeout(resolve, 500));

  // Começa o processo
  processNextMessage();
};

const chatSectionRef = ref(null);
const chatStarted = ref(false);

onMounted(() => {
  // Criar um Intersection Observer para monitorar a seção do chat
  const observer = new IntersectionObserver((entries) => {
    const [entry] = entries;
    // Se a seção estiver visível e o chat ainda não começou
    if (entry.isIntersecting && !chatStarted.value) {
      chatStarted.value = true;
      startChatAnimation();
    }
  }, {
    // Começar a animação quando 30% da seção estiver visível
    threshold: 0.3
  });

  // Observar a seção do chat
  if (chatSectionRef.value) {
    observer.observe(chatSectionRef.value);
  }
});

// Observa mudanças na visibilidade das mensagens e no estado de digitação
watch([() => messages.value.map(m => m.visible), isAITyping], () => {
  scrollToBottom();
}, { deep: true });

const steps = [
  {
    icon: MessageSquare,
    title: "Cada pessoa é única, e seu caminho para a saúde também.",
    description:
      "Meu trabalho começa com uma avaliação completa da sua saúde, rotina e objetivos, para criar um plano nutricional totalmente personalizado.",
    color: "kivvo-green",
  },
  {
    icon: FileText,
    title: "Acompanhamento próximo e humanizado.",
    description:
      "Mudar hábitos pode ser desafiador, mas você não estará sozinho. Além da orientação nutricional, ofereço suporte emocional e educacional, ajustando o plano conforme suas necessidades.",
    color: "kivvo-turquoise",
  },
  {
    icon: CheckCircle,
    title: "Foco em resultados duradouros.",
    description:
      "Mais do que dietas, promovemos mudanças de estilo de vida reais e sustentáveis, fortalecendo sua relação com a comida de forma equilibrada e prazerosa.",
    color: "kivvo-purple",
  },
];

const comparisonData = [
  {
    feature: "Plano de treino personalizado",
    traditional: "R$ 150-300/sessão",
    kivvo: "Incluído"
  },
  {
    feature: "Plano alimentar adaptado",
    traditional: "R$ 200-400/consulta",
    kivvo: "Incluído"
  },
  {
    feature: "Acompanhamento diário",
    traditional: "Não disponível",
    kivvo: "24/7 (com IA)"
  },
  {
    feature: "Ajustes no plano",
    traditional: "R$ 100-200 cada",
    kivvo: "Ilimitado"
  },
  {
    feature: "Custo mensal total",
    traditional: "R$ 800-1.500",
    kivvo: "R$ 29",
    highlight: true
  }
];

const features = [
  "Plano de treino personalizado",
  "Plano alimentar adaptado",
  "Coach Kivvo 24/7",
  "Dashboard de progresso",
  "Ajustes ilimitados",
  "Suporte prioritário"
];

const beneficios = [
  "leve,  realista e eficaz",
];

const currentText = ref('');
const currentIndex = ref(0);

const typeText = async () => {
  const text = beneficios[currentIndex.value];

  // Limpa o texto atual
  currentText.value = '';

  // Anima cada letra
  for (let i = 0; i < text.length; i++) {
    currentText.value += text[i];
    await new Promise(resolve => setTimeout(resolve, 100)); // Velocidade da digitação
  }

  // Espera um pouco antes de apagar
  await new Promise(resolve => setTimeout(resolve, 2000));

  // Anima apagando
  while (currentText.value.length > 0) {
    currentText.value = currentText.value.slice(0, -1);
    await new Promise(resolve => setTimeout(resolve, 50)); // Velocidade do apagar
  }

  // Próxima palavra
  currentIndex.value = (currentIndex.value + 1) % beneficios.length;

  // Continua o ciclo após um pequeno delay
  await new Promise(resolve => setTimeout(resolve, 500));
  typeText();
};

const handleSendMessage = () => {
  if (!inputMessage.value.trim()) return;

  // Adiciona mensagem do usuário
  messages.value.push({
    type: 'user',
    content: inputMessage.value
  });

  // Limpa input
  inputMessage.value = '';

  // Simula resposta do bot após 1 segundo
  setTimeout(() => {
    messages.value.push({
      type: 'bot',
      content: 'Entendi! Com base nas suas informações, posso criar um plano personalizado que combine exercícios de força com cardio para alcançar seus objetivos. Quer conhecer mais detalhes?'
    });
  }, 1000);
};

onMounted(() => {
  typeText();
});

// Testimonials Animation Control
const containerRef = useDomRef();
const isPaused = ref(false);
const xPos = useMotionValue(0);

// Calculate total width of all cards
const calculateTotalWidth = () => {
  const cardWidth = 350; // Width of each card
  const gap = 24; // Gap between cards
  return (cardWidth + gap) * testimonials.length;
};

let animationController = null;
const animationDuration = 100; // seconds for one complete cycle

// Function to start or resume animation
const startAnimation = () => {
  if (animationController) {
    animationController.stop();
  }

  const currentX = xPos.get();
  const totalWidth = calculateTotalWidth();

  // Calculate remaining duration based on position
  const progress = Math.abs(currentX) / totalWidth;
  const remainingDuration = animationDuration * (1 - progress);

  animationController = animate(xPos, [currentX, -totalWidth], {
    duration: remainingDuration,
    ease: 'linear',
    onComplete: () => {
      // Reset position instantly and restart
      xPos.set(0);
      if (!isPaused.value) {
        startAnimation();
      }
    }
  });
};

// Function to pause animation
const pauseAnimation = () => {
  if (animationController) {
    animationController.stop();
  }
};

// Watch isPaused to control animation
watch(isPaused, (paused) => {
  if (paused) {
    pauseAnimation();
  } else {
    startAnimation();
  }
});

onMounted(() => {
  // Start animation after a small delay
  nextTick(() => {
    if (!isPaused.value) {
      startAnimation();
    }
  });
});

// Animation styles
const testimonialContainer: CSSProperties = {
  width: '100%',
  maxWidth: '100vw',
  position: 'relative',
  overflow: 'hidden',
};

const testimonialTrack: CSSProperties = {
  display: 'flex',
  gap: '24px',
  padding: '20px 0',
};

const testimonialCard: CSSProperties = {
  flex: '0 0 350px',
};
</script>

<template>
  <Head title="Helen Freitas - Nutricionista" />

  <div class="min-h-screen bg-fundo-escuro text-[#E0DEDE] overflow-x-hidden">
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-fundo-escuro/90 backdrop-blur-lg border-b border-cinza">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="flex items-center gap-3">
            <img src="/logo_fundo_escuro.png" alt="Kivvo" class="h-10" />
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex items-center gap-8">
            <a href="#como-funciona" class="text-branco hover:text-verde-primario transition-colors">
              Sobre
            </a>
            <a href="#depoimentos" class="text-branco hover:text-verde-primario transition-colors">
              Depoimentos
            </a>
            <a href="#precos" class="text-branco hover:text-verde-primario transition-colors">
              Planos
            </a>
            <a href="#faq" class="text-branco hover:text-verde-primario transition-colors">
              Perguntas Frequentes
            </a>
          </nav>

          <!-- Auth Buttons -->
          <div class="flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
              class="rounded-full bg-verde-primario px-6 py-2 text-fundo-escuro hover:bg-verde-primario/90 font-semibold transition-colors animate-glow"
                >
                    Dashboard
                </Link>
                <template v-else>

            </template>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 px-4 relative overflow-hidden mt-10">
      <!-- Background Effects -->
      <div class="absolute inset-0 bg-gradient-to-br from-verde-primario/10 via-transparent to-roxo/10"></div>
      <div class="absolute top-20 left-10 w-64 h-64 bg-verde-primario/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-turquesa/20 rounded-full blur-3xl"></div>

      <div class="container mx-auto relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Main Headline -->
          <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
            <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text">
              Você merece um
            </span>
            <br />
            <span class="text-branco">acompanhamento nutricional</span>
            <br />
            <span class="text-verde-primario">{{ currentText }}</span>
            <span class="animate-blink">|</span>
          </h1>

          <!-- Subtitle -->
          <p class="text-xl md:text-2xl text-branco mb-12 max-w-3xl mx-auto leading-relaxed">
            Alimentação personalizada, criada por uma especialista, sempre ao alcance do seu bolso.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">

<a class="rounded-full bg-verde-primario px-8 py-4 text-lg font-semibold text-fundo-escuro hover:bg-verde-primario/90 transition-colors animate-glow"
    href="https://wa.me/5561992098384?text=Quero%20agendar%20a%20minha%20consullta!" target="_blank" rel="noopener nofollow">
                    Saiba mais
            </a>

          </div>

          <!-- Trust Indicators -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-8 text-sm text-[#E0DEDE]/80">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-verde-primario rounded-full"></div>
              <span>Sem terrorismo nutricional. </span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-turquesa rounded-full"></div>
              <span>Sem modismos. </span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-roxo rounded-full"></div>
              <span>Com resultados reais.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 animate-bounce">
        <ArrowDownIcon class="w-6 h-6 text-verde-primario" />
      </div>
    </section>




    <!-- Sobre  -->
    <section id="como-funciona" class="relative py-20 px-4 overflow-hidden">
      <!-- Background Images with Overlay -->
      <div class="absolute inset-0 z-0">
        <!-- Female Image -->
        <div class="absolute top-0 left-0 w-full md:w-1/2 h-1/2 md:h-full">
            <div class="text-center mb-16">

        </div>
        <h2 class="text-4xl md:text-5xl font-bold mb-6">

            </h2>

          <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-tl from-fundo-escuro/90 md:from-fundo-escuro/100 via-fundo-escuro/80 to-transparent"></div>
        </div>
        <!-- Male Image -->
        <div class="absolute bottom-0 right-0 w-full md:w-1/2 h-1/2 md:h-full">
          <img
            src="/storage/imagens/grupo_amigos_expanded.png"
            alt="Fitness Results"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-tr from-fundo-escuro/90 md:from-fundo-escuro/100 via-fundo-escuro/80 to-transparent"></div>
        </div>
        <!-- Center fade overlay -->
        <div class="absolute inset-0">
          <div class="absolute inset-y-0 left-1/2 transform -translate-x-1/2 w-32 bg-gradient-to-r from-transparent via-fundo-escuro/50 to-transparent"></div>
          <div class="absolute inset-x-0 top-1/2 transform -translate-y-1/2 h-32 md:hidden bg-gradient-to-b from-transparent via-fundo-escuro/50 to-transparent"></div>
        </div>
        <!-- Additional overlay for text readability -->
        <div class="absolute inset-0 bg-fundo-escuro/40"></div>
      </div>

      <div class="container mx-auto relative z-10">
        <div class="mb-16">
          <h2 class="text-center text-4xl md:text-5xl font-bold mb-6">
            Olá, sou <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text uppercase">Helen Freitas,</span>
            <br />
            nutricionista há mais de 10 anos, <br />
            mas nem sempre minha relação <br />
            com a comida foi algo saudável….
          </h2>

              <p class=" text-xl text-branco/70 max-w-2xl mx-auto text-justify" >

            </p>
            <br />
             <p class="text-xl text-branco/70 max-w-2xl mx-auto text-justify" >
                  Antes de me formar, também vivi a luta contra a balança: enfrentei
dietas restritivas, desenvolvi compulsão alimentar e conheci de
perto a frustração do "começa e desiste".
                </p>
                <br />
                <p class="text-xl text-branco/70 max-w-2xl mx-auto text-justify" >
                    Essa experiência pessoal me tornou ainda mais preparada para
                    acolher e compreender cada paciente. Desde o início da minha
                    carreira, escolhi ser uma profissional que escuta, entende e
                    caminha junto.
                    </p>
                    <br />
                    <p class="text-xl text-branco/70 max-w-2xl mx-auto text-justify" >

                    Sei que a jornada vai muito além de "força de vontade" e que impor
                    regras rígidas só afasta os resultados verdadeiros.
                    </p>

                    <br />

                    <p class="text-xl text-branco/70 max-w-2xl mx-auto text-justify" >
                                    Meu compromisso é construir, junto com você, uma estratégia
                    nutricional realista, respeitosa e duradoura. Se você busca
                    mudanças de verdade e sustentáveis, está no lugar certo!

          </p>

        </div>




        <!-- Bottom CTA -->
        <div class="text-center mt-16">
          <div class="inline-flex items-center gap-2 bg-verde-primario/10 px-6 py-3 rounded-full border border-verde-primario/30">
            <div class="w-2 h-2 bg-verde-primario rounded-full animate-pulse"></div>
            <span class="text-verde-primario font-medium">Acompanhamento nutricional humano, realista e duradouro.</span>
          </div>
        </div>
      </div>
    </section>


    <!-- Testimonials -->
    <section id="depoimentos" class="py-20 px-4 bg-verde-escuro/30 overflow-hidden">
      <div class="container mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Resultados <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text">reais</span> de pessoas reais
          </h2>
          <p class="text-xl text-branco/70 max-w-2xl mx-auto">
            Nada melhor do que ouvir quem já passou pela experiência de transformação. Veja o que algumas pacientes dizem
          </p>
        </div>

        <div id="testimonials" :style="testimonialContainer" class="overflow-hidden">
          <!-- Testimonials Track -->
          <div class="relative overflow-hidden" ref="containerRef">
            <motion.div
              :style="{
                ...testimonialTrack,
                x: xPos,
              }"
              @mouseenter="isPaused = true"
              @mouseleave="isPaused = false"
              class="pb-4"
            >
              <div
                v-for="(testimonial, index) in [...testimonials, ...testimonials]"
                :key="index"
                :style="testimonialCard"
                class="bg-cinza/50 hover:bg-cinza rounded-xl border border-branco/10 p-6 hover:border-verde-primario/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-verde-primario/20"
              >
                <!-- Rating -->
                <div class="flex mb-4 text-lg">
                  <template v-for="star in 5" :key="star">
                    <span :class="star <= testimonial.rating ? 'text-verde-primario' : 'text-branco/20'">★</span>
                  </template>
                </div>

                <!-- Content -->
                <p class="text-branco/90 mb-6 leading-relaxed text-sm">
                  "{{ testimonial.content }}"
                </p>

                <!-- Result Badge -->
                <div class="mb-4">
                  <span class="inline-block bg-verde-primario/20 text-verde-primario px-3 py-1 rounded-full text-xs font-medium">
                    {{ testimonial.result }}
                  </span>
                </div>

                <!-- Author -->
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-turquesa/20 flex items-center justify-center">
                    <img
                      :src="testimonial.url"
                      :alt="testimonial.name"
                      class="w-10 h-10 rounded-full object-cover"
                    />
                  </div>
                  <div>
                    <h4 class="font-semibold text-branco text-sm">
                      {{ testimonial.name }}
                    </h4>
                    <p class="text-branco/50 text-xs">
                      {{ testimonial.role }}, {{ testimonial.age }} anos
                    </p>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>

        <!-- Social Proof -->
        <div class="text-center mt-16">
          <div class="inline-flex items-center gap-6 bg-[#363941] px-8 py-4 rounded-2xl border border-branco/10">
            <div class="text-center">
              <div class="text-2xl font-bold text-verde-primario">6k+</div>
              <div class="text-sm text-branco/50">Seguidores</div>
            </div>
            <div class="w-px h-8 bg-branco/10"></div>
            <div class="text-center">
              <div class="text-2xl font-bold text-turquesa">4.9★</div>
              <div class="text-sm text-branco/50">Avaliação média</div>
            </div>
            <div class="w-px h-8 bg-branco/10"></div>
            <div class="text-center">
              <div class="text-2xl font-bold text-roxo">95%</div>
              <div class="text-sm text-branco/50">Taxa de sucesso</div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- Sobre  -->
    <section id="como-funciona" class="relative py-20 px-4 overflow-hidden">
      <!-- Background Images with Overlay -->
      <div class="absolute inset-0 z-0">
        <!-- Female Image -->
        <div class="absolute top-0 left-0 w-full md:w-1/2 h-1/2 md:h-full">
          <img
            src="/storage/imagens/fitness_model_female_800.png"
            alt="Fitness Results"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-tl from-fundo-escuro/90 md:from-fundo-escuro/100 via-fundo-escuro/80 to-transparent"></div>
        </div>
        <!-- Male Image -->
        <div class="absolute bottom-0 right-0 w-full md:w-1/2 h-1/2 md:h-full">
          <img
            src="/storage/imagens/fitness_model_male2_crop.png"
            alt="Fitness Results"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-tr from-fundo-escuro/90 md:from-fundo-escuro/100 via-fundo-escuro/80 to-transparent"></div>
        </div>
        <!-- Center fade overlay -->
        <div class="absolute inset-0">
          <div class="absolute inset-y-0 left-1/2 transform -translate-x-1/2 w-32 bg-gradient-to-r from-transparent via-fundo-escuro/50 to-transparent"></div>
          <div class="absolute inset-x-0 top-1/2 transform -translate-y-1/2 h-32 md:hidden bg-gradient-to-b from-transparent via-fundo-escuro/50 to-transparent"></div>
        </div>
        <!-- Additional overlay for text readability -->
        <div class="absolute inset-0 bg-fundo-escuro/40"></div>
      </div>

      <div class="container mx-auto relative z-10">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Chegou a hora de <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text uppercase">cuidar de você!</span>
          </h2>
          <p class="text-xl text-branco/70 max-w-2xl mx-auto">
            Transforme seu corpo  com a ajuda da Nutri
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div
            v-for="(step, index) in steps"
            :key="index"
            class="relative group"
          >
            <!-- Connection Line -->
            <div
              v-if="index < steps.length - 1"
              class="hidden md:block absolute top-16 left-full w-full h-0.5 bg-gradient-to-r from-verde-primario to-turquesa opacity-30 z-0"
            ></div>

            <!-- Step Card -->
            <div class="relative z-10 bg-[#363941]/90 backdrop-blur-sm rounded-2xl p-8 text-center border border-branco/20 hover:border-verde-primario/50 transition-all duration-300 hover:-translate-y-2 group-hover:shadow-2xl group-hover:shadow-verde-primario/20">
              <!-- Step Number -->
              <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-verde-primario rounded-full flex items-center justify-center text-fundo-escuro font-bold text-sm shadow-lg">
                {{ index + 1 }}
              </div>

              <!-- Icon -->
              <div
                class="w-16 h-16 mx-auto mb-6 rounded-2xl flex items-center justify-center"
                :class="{
                  'bg-verde-primario/20': step.color === 'kivvo-green',
                  'bg-turquesa/20': step.color === 'kivvo-turquoise',
                  'bg-roxo/20': step.color === 'kivvo-purple'
                }"
              >
                <component
                  :is="step.icon"
                  class="w-8 h-8"
                  :class="{
                    'text-verde-primario': step.color === 'kivvo-green',
                    'text-turquesa': step.color === 'kivvo-turquoise',
                    'text-roxo': step.color === 'kivvo-purple'
                  }"
                />
              </div>

              <!-- Content -->
              <h3 class="text-xl font-semibold mb-4 text-branco">
                {{ step.title }}
              </h3>
              <p class="text-branco/70 leading-relaxed">
                {{ step.description }}
              </p>
            </div>
          </div>
        </div>

        <p class="mt-4 text-center text-sm text-branco/70 ">
           Nutrição personalizada, com cuidado humano e foco em resultados reais e duradouros.
        </p>


        <!-- Bottom CTA -->
        <div class="text-center mt-16">
          <div class="inline-flex items-center gap-2 bg-verde-primario/10 px-6 py-3 rounded-full border border-verde-primario/30">
            <div class="w-2 h-2 bg-verde-primario rounded-full animate-pulse"></div>
            <span class="text-verde-primario font-medium">Acompanhamento 100% personalizado</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Comparison -->
    <section id="precos" class="py-20 px-4">
      <div class="container mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text">Qual o seu momento?</span> Escolha seu plano ideal:
          </h2>
          <p class="text-xl text-branco/70 max-w-2xl mx-auto">

          </p>
        </div>


        <!-- Pricing Card -->
        <div class="max-w-4xl mx-auto">
          <div class="grid md:grid-cols-2 gap-6">
            <!-- Plano Básico -->
            <div class="bg-[#363941] border border-branco/10 hover:border-verde-primario/50 rounded-xl pt-12 px-8 pb-8 text-center relative overflow-visible transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-verde-primario/20 grid grid-rows-[auto_1fr_auto] h-full">
              <!-- Flexible badge -->
              <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gray-300 text-fundo-escuro px-6 py-2 rounded-full text-sm font-semibold">
                Mais Flexível
              </div>

              <!-- Header -->
              <div>
                <h3 class="text-2xl font-bold text-branco mb-2">Plano Básico</h3>
                <p class="text-branco/70 mb-6">1 consulta</p>

                <!-- Pricing -->
                <div class="mb-6">

                </div>
              </div>

              <!-- Features -->
              <div>
                <ul class="space-y-3 mb-8 text-left">
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">1 consulta + 1 retorno em até 30 dias</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Presencial ou online</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Avaliação física completa</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Plano alimentar calculado para o seu objetivo</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Lista de compras e metas para o retorno</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Foto comparativa</span>
                  </li>
                </ul>
              </div>

              <!-- Footer -->
              <div>
                <a class="block w-full bg-[#363941] border-2 border-verde-primario text-verde-primario hover:bg-verde-primario hover:text-fundo-escuro font-semibold py-4 rounded-full mb-4 transition-colors" href="https://wa.me/5561992098384?text=Gostaris%20de%20mais%20insformações%20sobre%20o%20Plano%20Básico" target="_blank" rel="noopener nofollow">
                    Saiba mais
            </a>

                <p class="text-xs text-branco/70">
                  Agende sua consulta
                </p>
              </div>
            </div>

            <!-- Plano Gold -->
            <div class="bg-gradient-to-b from-[#363941] to-verde-escuro/20 border border-verde-primario/50 rounded-xl pt-12 px-8 pb-8 text-center relative overflow-visible grid grid-rows-[auto_1fr_auto] h-full">
              <!-- Popular badge -->
              <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-verde-primario text-fundo-escuro px-6 py-2 rounded-full text-sm font-semibold">
                Mais Popular
              </div>

              <!-- Header -->
              <div>
                <h3 class="text-2xl font-bold text-branco mb-2">Plano Gold</h3>
                <p class="text-branco/70 mb-6">4 consultas</p>

                <!-- Pricing -->
                <div class="mb-6">

                </div>
              </div>

             <!-- Features -->
              <div>
                <ul class="space-y-3 mb-8 text-left">
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">4 consultas presenciais em 4 semanas</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Presencial</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Avaliação física completa em todas as consultas</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Novo cardápio e ajustes semanais</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Lista de compras e metas para o retorno</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Acompanhamento com foto comparativa</span>
                  </li>
                </ul>
              </div>

              <!-- Footer -->
              <div>
                <a class="block w-full bg-[#363941] border-2 border-verde-primario text-verde-primario hover:bg-verde-primario hover:text-fundo-escuro font-semibold py-4 rounded-full mb-4 transition-colors" href="https://wa.me/5561992098384?text=Gostaris%20de%20mais%20insformações%20sobre%20o%20Plano%20Gold" target="_blank" rel="noopener nofollow">
                    Saiba mais
                </a>

                <p class="text-xs text-branco/70">
                  Agende sua consulta
                </p>
              </div>
            </div>
          </div>
        </div>



        <!-- Pricing Card -->
        <div class="max-w-4xl mx-auto mt-13">
          <div class="grid md:grid-cols-2 gap-6">
            <!-- Plano Básico -->
            <div class="bg-[#363941] border border-branco/10 hover:border-verde-primario/50 rounded-xl pt-12 px-8 pb-8 text-center relative overflow-visible transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-verde-primario/20 grid grid-rows-[auto_1fr_auto] h-full">
              <!-- Flexible badge -->
              <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gray-300 text-fundo-escuro px-6 py-2 rounded-full text-sm font-semibold">
                Mais Flexível
              </div>

              <!-- Header -->
              <div>
                <h3 class="text-2xl font-bold text-branco mb-2">Plano Vip</h3>
                <p class="text-branco/70 mb-6">6 consultas</p>

                <!-- Pricing -->
                <div class="mb-6">

                </div>
              </div>

              <!-- Features -->
              <div>
                <ul class="space-y-3 mb-8 text-left">
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">6 consultas</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">3 presenciais + 3 online</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Avaliação física em todas as consultas presenciais</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Ajustes de dieta a cada 15 dias</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Lista de compras e metas personalizadas</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Acompanhamento de evolução com fotos comparativas</span>
                  </li>
                </ul>
              </div>

              <!-- Footer -->
              <div>
                <a class="block w-full bg-[#363941] border-2 border-verde-primario text-verde-primario hover:bg-verde-primario hover:text-fundo-escuro font-semibold py-4 rounded-full mb-4 transition-colors" href="https://wa.me/5561992098384?text=Gostaris%20de%20mais%20insformações%20sobre%20o%20Plano%20Vip" target="_blank" rel="noopener nofollow">
                    Saiba mais
            </a>

                <p class="text-xs text-branco/70">
                  Agende sua consulta
                </p>
              </div>
            </div>

            <!-- Plano Gold -->
            <div class="bg-gradient-to-b from-[#363941] to-verde-escuro/20 border border-verde-primario/50 rounded-xl pt-12 px-8 pb-8 text-center relative overflow-visible grid grid-rows-[auto_1fr_auto] h-full">
              <!-- Popular badge -->
               <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gray-300 text-fundo-escuro px-6 py-2 rounded-full text-sm font-semibold">
                Mais Flexível
              </div>

              <!-- Header -->
              <div>
                <h3 class="text-2xl font-bold text-branco mb-2">Plano Premium</h3>
                <p class="text-branco/70 mb-6">6 consultas</p>

                <!-- Pricing -->
                <div class="mb-6">

                </div>
              </div>

             <!-- Features -->
              <div>
                <ul class="space-y-3 mb-8 text-left">
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">6 consultas mensais presenciais (1 Presencial por mês)</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Avaliação física completa em todas as consultas</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Estratégias práticas para alimentação na rotina</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Plano alimentar adaptado a imprevistos e vida real</span>
                  </li>
                  <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Acompanhamento próximo e personalizado</span>
                  </li>
                   <li class="flex items-center gap-3">
                    <span class="text-verde-primario w-5 h-5 flex-shrink-0">✓</span>
                    <span class="text-branco">Educação nutricional para autonomia</span>
                  </li>
                </ul>
              </div>

              <!-- Footer -->
              <div>

            <a class="block w-full bg-[#363941] border-2 border-verde-primario text-verde-primario hover:bg-verde-primario hover:text-fundo-escuro font-semibold py-4 rounded-full mb-4 transition-colors" href="https://wa.me/5561992098384?text=Gostaris%20de%20mais%20insformações%20sobre%20o%20Plano%20Premium" target="_blank" rel="noopener nofollow">
                    Saiba mais
            </a>
                <p class="text-xs text-branco/70">
                  Agende sua consulta
                </p>
              </div>
            </div>
          </div>





          <!-- Money Back Guarantee -->
          <div class="text-center mt-12">
            <div class="inline-flex items-center gap-3 bg-verde-primario/10 px-6 py-3 rounded-full border border-verde-primario/30">
              <div class="w-8 h-8 rounded-full bg-verde-primario flex items-center justify-center">
                <span class="text-fundo-escuro">✓</span>
              </div>
              <span class="text-verde-primario font-medium">
                Teste grátis de 7 dias, sem compromisso
              </span>
            </div>
          </div>
          <!-- Money Back Guarantee -->
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 px-4 bg-verde-escuro/30 relative">

      <div class="container mx-auto relative z-10">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Perguntas <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text">Frequentes</span>
          </h2>
          <p class="text-xl text-branco/70 max-w-2xl mx-auto">
            Tudo que você precisa saber sobre o Kivvo
          </p>
        </div>

        <div class="max-w-3xl mx-auto">
          <div class="space-y-4">
            <div
              v-for="(faq, index) in faqs"
              :key="index"
              class="bg-cinza border border-branco/10 rounded-lg overflow-hidden transition-all duration-300 hover:border-verde-primario/50"
            >
              <button
                @click="faq.isOpen = !faq.isOpen"
                class="w-full px-6 py-6 text-left flex items-center justify-between hover:text-verde-primario transition-colors group"
              >
                <span class="font-semibold text-branco group-hover:text-verde-primario group-hover:underline transition-all duration-300 group-hover:underline-offset-4">{{ faq.question }}</span>
                <span
                  class="transform transition-transform duration-300"
                  :class="{ 'rotate-180': faq.isOpen }"
                >
                  <ChevronDownIcon   n class="w-6 h-6" />
                </span>
              </button>
              <div
                v-show="faq.isOpen"
                class="px-6 pb-6 text-branco/70 leading-relaxed"
              >
                {{ faq.answer }}
              </div>
            </div>
          </div>
        </div>





      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-verde-escuro/30 py-16 px-4">
      <div class="container mx-auto">
        <!-- Main Footer Content -->
        <div class="grid md:grid-cols-4 gap-8 mb-12">
          <!-- Logo and Description -->
          <div class="md:col-span-1">
            <div class="mb-4">
              <img src="/logo_fundo_escuro3.svg" alt="Kivvo" class="h-8" />
            </div>
            <p class="text-branco/70 leading-relaxed mb-6">
              Está pronta para transformar sua alimentação e sua relação com a comida?
            </p>
            <!-- Social Media -->
            <div class="flex gap-4">
              <a href="https://www.instagram.com/helenfreitasnutri/" class="w-10 h-10 rounded-full bg-cinza flex items-center justify-center text-verde-primario hover:bg-verde-primario hover:text-fundo-escuro transition-colors">
                <span class="text-sm font-bold">@</span>
              </a>

            </div>
          </div>

          <!-- Product Links -->
          <div>
            <h3 class="font-semibold text-branco mb-4">Produto</h3>
            <ul class="space-y-3">
              <li><a href="#como-funciona" class="text-branco/70 hover:text-verde-primario transition-colors">Sobre </a></li>
              <li><a href="#depoimentos" class="text-branco/70 hover:text-verde-primario transition-colors">Depoimentos</a></li>
              <li><a href="#precos" class="text-branco/70 hover:text-verde-primario transition-colors">Planos</a></li>
            </ul>
          </div>

          <!-- Support Links -->
          <div>
            <h3 class="font-semibold text-branco mb-4">Suporte</h3>
            <ul class="space-y-3">
              <li><a href="#faq" class="text-branco/70 hover:text-verde-primario transition-colors">Perguntas Frequentes</a></li>

            </ul>
          </div>

        </div>

        <!-- Bottom Section -->
        <div class="border-t border-branco/10 pt-8">
          <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-branco/70 text-sm">
              © {{ new Date().getFullYear() }} Kivvo. Todos os direitos reservados.
            </p>
            <div class="flex items-center gap-6 text-sm">
              <span class="text-branco/70">🇧🇷 Feito no Brasil</span>
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-verde-primario rounded-full animate-pulse"></div>
                <span class="text-verde-primario">Todos os sistemas operacionais</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    </div>
</template>

<style>
html {
  scroll-behavior: smooth;
}

.animate-blink {
  animation: blink 1s step-end infinite;
  display: inline-block;
  margin-left: 1px;
  font-weight: 300;
}

@keyframes blink {
  from, to {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
  opacity: 0;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.thin-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #E2E8F0 transparent;
  padding-right: 6px;
}

.thin-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.thin-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.thin-scrollbar::-webkit-scrollbar-thumb {
  background-color: #E2E8F0;
  border-radius: 3px;
}

.thin-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #CBD5E1;
}

#testimonials ::-webkit-scrollbar {
  height: 5px;
  width: 5px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

#testimonials ::-webkit-scrollbar-thumb {
  background: var(--verde-primario);
  border-radius: 4px;
}

#testimonials ::-webkit-scrollbar-corner {
  background: rgba(255, 255, 255, 0.1);
}

:root {
  --verde-primario: #4ADE80;
}

</style>

