<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="/" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar ao Início
        </a>
    </div>
    <div class="max-w-md mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-10 sm:px-12">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Login - TechFit</h2>

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <?php
                        $type   = $_SESSION['flash_type'] ?? 'info';
                        $colors = [
                            'success' => 'green',
                            'error'   => 'red',
                            'warning' => 'yellow',
                            'info'    => 'blue',
                        ];
                        $color = $colors[$type] ?? 'blue';
                    ?>
                    <div class="mb-6 bg-<?php echo $color; ?>-100 border border-<?php echo $color; ?>-400 text-<?php echo $color; ?>-800 px-4 py-3 rounded-lg flex justify-between items-start" role="alert">
                        <span class="block sm:inline"><?php echo $_SESSION['flash_message']; ?></span>
                        <button type="button" onclick="this.parentElement.remove()" class="ml-4 text-<?php echo $color; ?>-800 hover:text-<?php echo $color; ?>-900">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <form method="POST" action="/login" class="space-y-6" novalidate>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               required
                               placeholder="seu@email.com"
                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition duration-200">
                        <p class="mt-1 text-xs text-gray-500">Digite seu email cadastrado</p>
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">
                            Senha
                        </label>
                        <input type="password"
                               id="senha"
                               name="senha"
                               required
                               placeholder="Sua senha"
                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition duration-200">
                        <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform transition duration-200 hover:scale-[1.02]">
                            Entrar
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Não tem conta?
                        <a href="/cadastro" class="font-medium text-emerald-600 hover:text-emerald-500 underline-offset-4 hover:underline">
                            Criar Conta
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>