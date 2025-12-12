<body class="bg-light">

<header>
    <?php require (__DIR__ . "/partials/nav.php"); ?>
</header>

<section class="max-w-4xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold text-center mb-8">❓ Perguntas Frequentes sobre o Site</h1>
    <p class="text-center mb-8 text-gray-600">
        Aqui respondemos as dúvidas mais comuns sobre o funcionamento do nosso site e como aproveitar melhor nossos serviços online.
    </p>

    <div class="space-y-4">

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Como faço para me cadastrar no site?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Para se cadastrar, clique no botão "Cadastre-se" no topo da página, preencha os dados solicitados e confirme seu email. Após isso, você poderá acessar todos os recursos do site.
            </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Como agendar minhas aulas online?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Acesse a seção "Aulas", escolha a modalidade desejada e selecione a data e horário disponíveis. Confirme seu agendamento e ele será registrado no seu perfil.
            </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Esqueci minha senha, como recupero?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Clique em "Esqueci minha senha" na tela de login, informe seu email e siga as instruções enviadas para redefinir sua senha.
            </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Como posso entrar em contato com a academia?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Você pode usar o formulário de contato na seção "Contato" ou enviar um email diretamente para suporte@techfit.com. Também é possível ligar para nossa central de atendimento.
            </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Quais navegadores são compatíveis com o site?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Nosso site funciona perfeitamente nas versões mais recentes do Chrome, Firefox, Edge e Safari. Recomendamos sempre manter o navegador atualizado.
            </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
            <button class="w-full text-left px-6 py-4 bg-gray-100 hover:bg-gray-200 flex justify-between items-center faq-btn">
                <span>Como posso gerenciar meus dados pessoais?</span>
                <span class="transform transition-transform duration-300">&#x25BC;</span>
            </button>
            <div class="px-6 py-4 hidden faq-content">
                Acesse sua página de perfil, onde você pode atualizar informações pessoais, alterar senha, gerenciar seus planos e visualizar histórico de aulas.
            </div>
        </div>

    </div>
</section>

<script>
    const faqButtons = document.querySelectorAll('.faq-btn');

    faqButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const arrow = btn.querySelector('span:last-child');

            content.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        });
    });
</script>
