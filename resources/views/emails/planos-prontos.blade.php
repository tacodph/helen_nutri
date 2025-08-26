@component('mail::message')
<div class="text-center mb-6">
    <img src="{{ asset('logotipo_fundo_claro.svg') }}" alt="Kivvo" style="width: 32px; height: 32px; margin: 0 auto 1rem auto;">
</div>

# Olá, {{ $user->name }}! 👋

Seus planos personalizados foram gerados com sucesso! 🎉

## 📋 O que você recebeu:

- **Plano Alimentar Personalizado**: Desenvolvido de acordo com seus objetivos e necessidades nutricionais
- **Plano de Treino**: Treinos específicos para maximizar seus resultados

Os PDFs estão anexados a este e-mail para você baixar e consultar quando precisar.

## 💡 Dicas importantes:

- Mantenha os planos sempre à mão para consulta
- Siga as orientações de forma consistente
- Em caso de dúvidas, entre em contato com seu coach

Bons treinos! 💪  
Equipe **Kivvo**

<div class="text-center mt-8 text-sm text-gray-500">
    <p>Este é um e-mail automático, por favor não responda.</p>
    <p>Para suporte, acesse <a href="https://kivvo.app" class="text-primaria-500">kivvo.app</a></p>
</div>
@endcomponent