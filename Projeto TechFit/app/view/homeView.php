
<header>
    <?php require (__dir__. "/partials/nav.php");?>
</header>
<section id="hero" class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">
                Transforme sua saúde com a TechFit
            </h1>
            <p class="lead">
                Agende suas aulas online, tenha acesso rápido a sua frequência,
                receba treinos personalizados e muito mais.
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button class="btn cta btn-dark btn-lg px-4 me-md-2">
                    Agende Sua Aula
                </button>
                <button class="btn btn-outline-secondary text-dark btn-lg px-4">
                    Cadastre-se Agora
                </button>
            </div>
        </div>

        <div class="col-10 col-sm-8 col-lg-6">
            <img
                src="./assets/images/hero.jpg"
                class="d-block max-lg-auto img-fluid"
                width="700"
                height="500"
                loading="lazy"
                alt="Imagem de uma pessoa se exercitando" />
        </div>
    </div>
</section>

<section class="container marketing py-5">
    <h1 class="text-center pb-4">Planos</h1>
    <div class="row">
        <!-- Plano Básico -->
        <div class="col-lg-4 p-2">
            <div class="card shadow-sm h-100 d-flex flex-column p-5">
                <h4 class="card-title">Plano Básico</h4>
                <h5 class="card-subtitle mb-2 text-muted">Para indivíduos</h5>
                <div class="card-body text-center">
                    <h2>R$99,99</h2>
                    <span>/mês</span>
                </div>
                <ul class="py-4">
                    <li>Acesso a todas as modalidades básicas</li>
                    <li>Treino funcional</li>
                    <li>Controle de frequência</li>
                </ul>
                <button class="btn btn-outline-dark fw-bold mt-auto">
                    Assinar
                </button>
            </div>
        </div>

        <!-- Plano Intermediário -->
        <div class="col-lg-4 p-2">
            <div class="card intermediate shadow-sm h-100 d-flex flex-column p-5">
                <h4 class="card-title">Plano Intermediário</h4>
                <h5 class="card-subtitle mb-2 text-muted">Para quem quer mais</h5>
                <div class="card-body text-center">
                    <h2>R$149,99</h2>
                    <span>/mês</span>
                </div>
                <ul class="py-4">
                    <li>Acesso a todas as modalidades da academia</li>
                    <li>Treino funcional e aulas coletivas</li>
                    <li>Benefícios adicionais</li>
                </ul>
                <button class="btn btn-dark fw-bold mt-auto">Assinar</button>
            </div>
        </div>

        <!-- Plano Premium -->
        <div class="col-lg-4 p-2">
            <div class="card premium shadow-sm h-100 d-flex flex-column p-5">
                <div class="spark"></div>
                <h4 class="card-title">Plano Premium</h4>
                <h5 class="card-subtitle mb-2 text-muted">
                    Para atletas e profissionais
                </h5>
                <div class="card-body text-center">
                    <h2>R$199,99</h2>
                    <span>/mês</span>
                </div>
                <ul class="py-4">
                    <li>Acesso ilimitado a todas as modalidades</li>
                    <li>Treino personalizado</li>
                    <li>Consultoria e suporte especializado</li>
                </ul>
                <button class="btn btn-outline-dark fw-bold mt-auto">
                    Assinar
                </button>
            </div>
        </div>
    </div>
</section>