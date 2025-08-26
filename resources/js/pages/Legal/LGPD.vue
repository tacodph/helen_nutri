<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';  


const currentDate = new Date().toLocaleDateString('pt-BR', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
});

const controllerInfo = {
  title: "Controlador:",
  content: "Kivvo – [razão social, CNPJ, endereço, se aplicável]"
};

const dpoInfo = {
  title: "Encarregado (DPO):",
  content: "privacidade@kivvo.app"
};

const sections = [
  {
    title: "1. Fundamentos Legais para o Tratamento de Dados",
    content: `Conforme a LGPD, a Kivvo se apoia nas seguintes bases legais:

• Art. 7º, I: Consentimento do titular

• Art. 7º, V: Execução de contrato

• Art. 7º, IX: Legítimo interesse (melhoria da experiência e segurança)

• Art. 11, II, a: Tratamento de dados sensíveis com consentimento (ex: saúde)`
  },
  {
    title: "2. Dados Pessoais Tratados",
    content: `• Dados cadastrais: nome, e-mail, sexo, idade

• Dados de saúde e estilo de vida: peso, altura, restrições alimentares, nível de atividade, histórico de treino

• Dados técnicos: endereço IP, navegador, localização aproximada`
  },
  {
    title: "3. Medidas de Segurança",
    content: `• Armazenamento criptografado

• Controle de acesso baseado em função

• Anonimização de dados para análise estatística

• Monitoramento contínuo de acesso e anomalias`
  },
  {
    title: "4. Direitos dos Titulares",
    content: "Conforme o Art. 18 da LGPD, o usuário pode exercer seus direitos mediante solicitação formal por e-mail. A Kivvo compromete-se a atender às solicitações no prazo legal."
  },
  {
    title: "5. Transferência internacional",
    content: "Caso dados sejam processados fora do Brasil, garantimos que os prestadores de serviço cumpram requisitos de segurança equivalentes aos exigidos pela LGPD."
  },
  {
    title: "6. Prazo de retenção",
    content: `Os dados são armazenados:

• Enquanto houver vínculo contratual ou uso ativo da plataforma

• Até 6 meses após encerramento do uso, exceto nos casos previstos em lei

• Dados podem ser anonimizados para fins estatísticos após esse período`
  }
];
</script>

<template>
  <div class="min-h-screen bg-[#041518] text-[#E0DEDE]">
    <Head title="LGPD - Kivvo" />

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
            LGPD
          </span>
        </h1>
        <p class="text-xl text-branco/70">
          Tratamento de Dados Pessoais na Kivvo
        </p>
      </div>

      <!-- Controller and DPO Info -->
      <div class="max-w-3xl mx-auto space-y-4 mb-8">
        <div class="bg-[#363941] rounded-xl p-6 border border-branco/10">
          <h2 class="text-xl font-semibold mb-2 text-verde-primario">
            {{ controllerInfo.title }}
          </h2>
          <p class="text-branco/70">
            {{ controllerInfo.content }}
          </p>
        </div>

        <div class="bg-[#363941] rounded-xl p-6 border border-branco/10">
          <h2 class="text-xl font-semibold mb-2 text-verde-primario">
            {{ dpoInfo.title }}
          </h2>
          <p class="text-branco/70">
            <a 
              :href="'mailto:' + dpoInfo.content"
              class="text-verde-primario hover:underline"
            >
              {{ dpoInfo.content }}
            </a>
          </p>
        </div>
      </div>

      <!-- LGPD Sections -->
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
            📩 Dúvidas sobre seus dados pessoais?
          </h2>
          <p class="text-branco/70">
            Entre em contato com nosso DPO: 
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