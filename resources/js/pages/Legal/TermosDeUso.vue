<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const currentDate = new Date().toLocaleDateString('pt-BR', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
});

const sections = [
  {
    title: "1. Sobre a Kivvo",
    content: "A Kivvo é uma plataforma tecnológica que utiliza inteligência artificial para oferecer sugestões de treino e alimentação com base nas informações fornecidas pelo próprio usuário. O serviço tem caráter informativo e educacional."
  },
  {
    title: "2. Natureza dos Conteúdos",
    content: `<b class="text-branco">A Kivvo <u>não realiza prescrição de treinos ou dietas</u> nos termos da legislação profissional.</b>

Não substituímos o acompanhamento de profissionais habilitados, como nutricionistas, médicos ou educadores físicos.

As orientações geradas pela Kivvo têm caráter informativo, automatizado e não profissional.

Ao utilizar a plataforma, o usuário reconhece e concorda que os planos sugeridos são apenas sugestões de apoio automatizado.`
  },
  {
    title: "3. Usuários elegíveis",
    content: `A Kivvo é voltada para usuários saudáveis e maiores de 18 anos. Não recomendamos o uso por:

• Gestantes
• Pessoas com doenças crônicas ou agudas
• Indivíduos com histórico de lesões ou limitações físicas
• Maiores de 65 anos sem liberação médica`
  },
  {
    title: "4. Responsabilidades do Usuário",
    content: `O usuário é o único responsável por:

• Informar dados verídicos e atualizados
• Avaliar, por conta própria, se está apto a seguir as sugestões fornecidas
• Buscar orientação profissional antes de iniciar qualquer atividade física ou mudança alimentar
• Interromper o uso e procurar ajuda médica ao primeiro sinal de desconforto ou sintomas adversos`
  },
  {
    title: "5. Acesso e Plano Gratuito",
    subsections: [
      {
        title: "5.1 Período de teste gratuito",
        content: `A Kivvo oferece acesso gratuito por 7 dias, sem necessidade de informar cartão de crédito.

Durante o período de teste, o usuário pode:

• Conversar com a IA (Coach Kivvo)
• Receber planos personalizados de treino e alimentação
• Usar o dashboard completo`
      },
      {
        title: "5.2 Após o período gratuito",
        content: `• Não há cobrança automática.
• O acesso à plataforma será bloqueado ao final dos 7 dias.
• O usuário poderá optar por assinar um plano para continuar utilizando a Kivvo.
• A inserção de dados de pagamento ocorrerá somente após o fim do período gratuito, caso o usuário deseje continuar.`
      }
    ]
  },
  {
    title: "6. Cancelamento",
    content: `O usuário pode cancelar sua assinatura a qualquer momento, sem multa ou fidelização.

O cancelamento impede novas cobranças, mas não retroage valores pagos antes da data de cancelamento.`
  },
  {
    title: "7. Propriedade Intelectual",
    content: "Todo o conteúdo, marca, software, fluxos de IA e identidade visual da Kivvo são protegidos por direitos autorais e marcas registradas. É proibida a reprodução ou uso não autorizado."
  },
  {
    title: "8. Limitação de Responsabilidade",
    content: `A Kivvo não se responsabiliza por eventuais danos decorrentes do uso inadequado da plataforma ou por resultados insatisfatórios decorrentes da não observância das orientações profissionais.

O uso da plataforma é de responsabilidade exclusiva do usuário.`
  },
  {
    title: "9. Atualizações dos Termos",
    content: "Este documento poderá ser atualizado a qualquer momento. Em caso de alterações significativas, notificaremos os usuários por e-mail ou dentro da plataforma."
  }
];
</script>

<template>
  <div class="min-h-screen bg-[#041518] text-[#E0DEDE]">
    <Head title="Termos de Uso - Kivvo" />

    <!-- Header -->
    <header class="border-b border-branco/10 bg-fundo-escuro/90 backdrop-blur-lg">
      <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between">
          <Link :href="route('home')">
            <img src="/logo_fundo_escuro.svg" alt="Kivvo" class="h-8" />
          </Link>
          <span class="text-sm text-branco/70">Última atualização: {{ currentDate }}</span>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
      <!-- Title Section -->
      <div class="max-w-3xl mx-auto text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">
          <span class="bg-gradient-to-r from-verde-primario to-turquesa text-transparent bg-clip-text">
            Termos de Uso
          </span>
        </h1>
        <p class="text-xl text-branco/70">
          Seja bem-vindo à Kivvo! Ao acessar ou utilizar a plataforma Kivvo, você concorda com os termos e condições abaixo. Leia com atenção.
        </p>
      </div>

      <!-- Terms Sections -->
      <div class="max-w-3xl mx-auto space-y-8">
        <div 
          v-for="(section, index) in sections" 
          :key="index"
          class="bg-[#363941] rounded-xl p-6 border border-branco/10 hover:border-verde-primario/50 transition-all duration-300"
        >
          <h2 class="text-xl font-semibold mb-4 text-verde-primario">
            {{ section.title }}
          </h2>

          <!-- Handle subsections if they exist -->
          <template v-if="section.subsections">
            <div class="space-y-6">
              <div 
                v-for="(subsection, subIndex) in section.subsections" 
                :key="subIndex"
                class="pl-4 border-l-2 border-verde-primario/30"
              >
                <h3 class="text-lg font-medium mb-3 text-turquesa">
                  {{ subsection.title }}
                </h3>
                <p class="text-branco/70 whitespace-pre-line leading-relaxed" v-html="subsection.content"></p>
              </div>
            </div>
          </template>

          <!-- Regular content if no subsections -->
          <template v-else>
            <p class="text-branco/70 whitespace-pre-line leading-relaxed" v-html="section.content"></p>
          </template>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="max-w-3xl mx-auto mt-12">
        <div class="bg-verde-primario/10 rounded-xl p-6 border border-verde-primario/30">
          <h2 class="text-xl font-semibold mb-4 text-verde-primario">
            📩 Dúvidas ou solicitações?
          </h2>
          <p class="text-branco/70">
            Entre em contato: 
            <a 
              href="mailto:contato@kivvo.app" 
              class="text-verde-primario hover:underline"
            >
              contato@kivvo.app
            </a>
          </p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-branco/10 mt-12">
      <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
          <p class="text-sm text-branco/70">
            © {{ new Date().getFullYear() }} Kivvo. Todos os direitos reservados.
          </p>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-verde-primario rounded-full"></div>
            <span class="text-sm text-verde-primario">Documento oficial</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.container {
  max-width: 1200px;
}
</style> 