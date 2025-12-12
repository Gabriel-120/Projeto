
<header>
        <?php require __DIR__ . "/partials/nav.php"; ?>
</header>

<main class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white shadow-md rounded-lg p-8">
            <?php if (!empty($message)): ?>
                <div class="mb-6 p-4 rounded text-sm bg-green-100 text-green-800">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors) && is_array($errors)): ?>
                <div class="mb-6 p-4 rounded text-sm bg-red-50 text-red-800">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h1 class="text-2xl font-semibold mb-4">Fale conosco</h1>
            <p class="text-gray-600 mb-6">Tem uma dúvida, sugestão ou problema? Envie uma mensagem e responderemos em breve.</p>

            <form method="post" action="/contato/send" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input id="nome" name="nome" type="text" required
                                     value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
                                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required
                                     value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2" />
                    </div>
                </div>

                <div>
                    <label for="assunto" class="block text-sm font-medium text-gray-700">Assunto</label>
                    <input id="assunto" name="assunto" type="text"
                                 value="<?= htmlspecialchars($old['assunto'] ?? '') ?>"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2" />
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone (opcional)</label>
                    <input id="telefone" name="telefone" type="tel"
                                 value="<?= htmlspecialchars($old['telefone'] ?? '') ?>"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2" />
                </div>

                <div>
                    <label for="mensagem" class="block text-sm font-medium text-gray-700">Mensagem</label>
                    <textarea id="mensagem" name="mensagem" rows="6" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 px-3 py-2"><?= htmlspecialchars($old['mensagem'] ?? '') ?></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        Enviar mensagem
                    </button>
                    <a href="/" class="text-sm text-gray-600 hover:underline">Voltar ao início</a>
                </div>
            </form>
        </div>
    </div>
</main>
