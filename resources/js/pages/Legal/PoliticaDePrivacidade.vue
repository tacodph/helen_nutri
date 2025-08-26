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
    title: "1. Quem somos",
    content: "Somos a Kivvo, uma plataforma que utiliza inteligência artificial para gerar planos personalizados de treino e alimentação. Atuamos como Controlador dos dados pessoais fornecidos por você."
  },
  {
    title: "2. Quais dados coletamos",
    content: `Coletamos os seguintes dados fornecidos por você no uso da plataforma:

• Nome, e-mail, idade, sexo
• Peso, altura, nível de atividade física
• Informações de saúde e restrições alimentares
• Preferências de treino e alimentação
• Dados de uso (acessos, tempo, progresso)`
  },
  {
    title: "3. Finalidade do tratamento",
    content: `Seus dados são utilizados para:

• Gerar planos personalizados com IA
• Oferecer uma experiência interativa e adaptativa
• Monitorar evolução e sugerir ajustes
• Enviar e-mails informativos (com seu consentimento)
• Cumprir obrigações legais ou regulatórias`
  },
  {
    title: "4. Como armazenamos seus dados",
    content: `• Utilizamos servidores com criptografia e controle de acesso

• Seus dados são mantidos apenas pelo tempo necessário para cumprir as finalidades aqui descritas

• Após a revogação do consentimento ou inatividade prolongada, seus dados poderão ser anonimizados ou excluídos, conforme política interna`
  },
  {
    title: "5. Compartilhamento de dados",
    content: `Seus dados não são vendidos nem repassados para terceiros com fins comerciais.

Podemos compartilhar dados com prestadores de serviço essenciais para funcionamento da Kivvo (como hospedagem, analytics, e-mail), sempre sob contrato e responsabilidade legal.`
  },
  {
    title: "6. Seus direitos como titular (Art. 18 da LGPD)",
    content: `Você pode, a qualquer momento:

• Confirmar a existência de tratamento de dados
• Acessar seus dados
• Corrigir dados incompletos, inexatos ou desatualizados
• Solicitar a portabilidade dos dados
• Revogar o consentimento
• Solicitar a exclusão dos dados, quando cabível

Basta enviar um e-mail para: privacidade@kivvo.app`
  },
  {
    title: "7. Uso de cookies",
    content: `A Kivvo utiliza cookies funcionais e analíticos para:

• Melhorar a navegação e performance
• Registrar preferências do usuário
• Gerar estatísticas de uso

Você pode gerenciar ou desativar os cookies nas configurações do seu navegador.`
  },
  {
    title: "8. Segurança da informação",
    content: `• Seus dados são protegidos por criptografia e monitoramento constante

• Adotamos práticas de segurança da informação com base no princípio do menor privilégio e acesso controlado

• Nossos colaboradores e parceiros seguem política de confidencialidade`
  },
  {
    title: "9. Alterações nesta política",
    content: "Esta política pode ser atualizada a qualquer momento. Em caso de mudanças relevantes, notificaremos por e-mail ou pela plataforma."
  }
];
</script>

<template>
  <div class="min-h-screen bg-[#041518] text-[#E0DEDE]">
    <Head title="Política de Privacidade - Kivvo" />

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
            Política de Privacidade
          </span>
        </h1>
        <p class="text-xl text-branco/70">
          A Kivvo valoriza a sua privacidade. Este documento descreve como tratamos os seus dados pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD – Lei nº 13.709/2018).
        </p>
      </div>

      <!-- Privacy Sections -->
      <div class="max-w-3xl mx-auto space-y-8">
        <div 
          v-for="(section, index) in sections" 
          :key="index"
          class="bg-[#363941] rounded-xl p-6 border border-branco/10 hover:border-verde-primario/50 transition-all duration-300"
        >
          <h2 class="text-xl font-semibold mb-4 text-verde-primario">
            {{ section.title }}
          </h2>
          <p class="text-branco/70 whitespace-pre-line leading-relaxed">
            {{ section.content }}
          </p>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="max-w-3xl mx-auto mt-12">
        <div class="bg-verde-primario/10 rounded-xl p-6 border border-verde-primario/30">
          <h2 class="text-xl font-semibold mb-4 text-verde-primario">
            📩 Dúvidas? Fale com nosso Encarregado de Dados (DPO):
          </h2>
          <p class="text-branco/70">
            Entre em contato: 
            <a 
              href="mailto:privacidade@kivvo.app" 
              class="text-verde-primario hover:underline"
            >
              privacidade@kivvo.app
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