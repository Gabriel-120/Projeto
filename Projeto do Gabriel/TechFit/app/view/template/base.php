<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="TechFit: Transforme sua saúde com treinos personalizados, aulas online e planos acessíveis. Junte-se à nossa academia hoje!">
    <meta name="keywords" content="academia, treinos personalizados, fitness, saúde, TechFit">
    <title>TechFit -                                                             <?php echo $titulo ?></title>
    <link rel="shortcut icon" href="/assets/icons/favicon.ico" type="image/x-icon">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    />
    <link rel="stylesheet" href="/assets/css/utility.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <?php echo $headExtras ?? '' ?>
</head>
    <body>
        <?php if (has_flash('success')): ?>
        <div id="flash-success"
         class="mb-3 p-3 rounded-lg bg-green-100 text-green-800 shadow transition-opacity duration-700">

            <?php foreach (get_flash('success') as $msg): ?>
                <p><?php echo htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (has_flash('error')): ?>
        <div id="flash-error"
        class="mb-3 p-3 rounded-lg bg-red-100 text-red-800 shadow transition-opacity duration-700">

            <?php foreach (get_flash('error') as $msg): ?>
                <p><?php echo htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <main>
        <?php echo $conteudo ?>
    </main>
    <?php require_once __DIR__ . "/../partials/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", () => {
    const flashes = document.querySelectorAll("[id^='flash-']");

    flashes.forEach(flash => {
        setTimeout(() => {
            flash.classList.add("opacity-0"); 

            setTimeout(() => flash.remove(), 700);
        }, 2000);
    });
});
</script>

</body>

</html>