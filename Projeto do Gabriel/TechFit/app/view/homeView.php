
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
                <a href="/aulas" class="btn cta btn-dark btn-lg px-4 me-md-2">
                    Agende Sua Aula
</a>
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
        <?php foreach (($planos ?? []) as $p):
            $id = (int)$p['id_plano'];
        ?>
        <div class="col-lg-4 p-2">
            <div class="card shadow-sm h-100 d-flex flex-column p-5">
                <h4 class="card-title"><?php echo htmlspecialchars($p['nome_plano']); ?></h4>
                <h5 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($p['descricao_plano'] ?? ''); ?></h5>
                <div class="card-body text-center">
                    <h2>R$<?php echo number_format((float)$p['preco'], 2, ',', '.'); ?></h2>
                    <span>/<?php echo intval($p['duracao']) === 30 ? 'mês' : intval($p['duracao']) . ' dias'; ?></span>
                </div>
                <ul class="py-4">
                    <li>Acesso às modalidades inclusas</li>
                </ul>
                <a href="/pagamentos?id_plano=<?php echo $id; ?>" class="btn btn-outline-dark fw-bold mt-auto">
                    Assinar
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
