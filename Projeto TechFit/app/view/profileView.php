<div class="flex">
    <aside class="flex flex-col justify-between min-h-screen bg-[#efefef] w-64 p-4 shadow-lg">
        <div>
            <a class="align-center hover:bg-[#cfcfcf] text-decoration-none text-black p-2 rounded-md flex items-center gap-2" href="/">❌ Voltar ao inicio</a>
            <div class="flex mt-5 flex-col items-center">
                <img src="<?= htmlspecialchars($user_pfp) ?>" alt="Foto de Perfil"
                     class="size-40 ring-2 ring-white rounded-full mb-2 border-4 border-gray-300">
                <p class="font-bold text-black-900 text-lg"><?= htmlspecialchars($user_name) ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($user_tipo) ?></p>
            </div>

            <nav class="mt-6 flex flex-col space-y-3">
                <?php if (strtolower($user_tipo) === 'aluno'): ?>
                    <a href="/profile?page=agenda" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2
                       <?= ($currPage === 'agenda') ? 'bg-blue-600 text-white' : 'hover:bg-[#cfcfcf]' ?>">
                       📅 Minha Agenda
                    </a>
                    <a href="/profile?page=avaliacao" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2
                       <?= ($currPage === 'avaliacao') ? 'bg-blue-600 text-white' : 'hover:bg-[#cfcfcf]' ?>">
                       📊 Avaliação Física
                    </a>
                    <a href="/profile?page=frequencia" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2
                       <?= ($currPage === 'frequencia') ? 'bg-blue-600 text-white' : 'hover:bg-[#cfcfcf]' ?>">
                       📈 Frequência
                    </a>
                    <a href="/comunicados" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2 hover:bg-[#cfcfcf]">
                       📢 Comunicados
                    </a>
                <?php elseif (strtolower($user_tipo) === 'funcionario'): ?>
                    <a href="/admin/painel" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2 hover:bg-[#cfcfcf]">📋 Painel Administrativo</a>
                    <a href="/admin/relatorios" class="text-decoration-none text-black p-2 rounded-md flex items-center gap-2 hover:bg-[#cfcfcf]">📑 Relatórios</a>
                <?php endif; ?>
            </nav>
        </div>

        <div class=" border-t border-dotted border-[#2f2f2f] mt-4 pt-4 space-y-2">
            <a href="/profile?page=configuracao" class="text-decoration-none text-black text-black p-2 rounded-md flex items-center gap-2
               <?= ($currPage === 'configuracao') ? 'bg-blue-600 text-white' : 'hover:bg-[#cfcfcf]' ?>">
                <span>⚙️</span><span>Configurações</span>
            </a>
            <a href="/logout" class="text-decoration-none text-black text-black p-2 rounded-md bg-blue flex items-center gap-2 text-red-600 hover:bg-red-100">
                <span>🚪</span><span>Sair</span>
            </a>
        </div>
    </aside>


    <main class="flex-1 p-8 bg-gray-50">
        <?php
        if (isset($subView)) {
            $subViewPath = __DIR__ . '/' . $subView;
            
            if ($subView === 'partials/placeholderView.php' && isset($message)) {
                echo "<div class='max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md'>";
                echo "<h2 class='text-2xl font-bold mb-4'>" . htmlspecialchars(ucfirst($currPage ?: 'Início')) . "</h2>";
                echo "<p>" . htmlspecialchars($message) . "</p>";
                echo "</div>";
            } 
            elseif (file_exists($subViewPath)) {
                require $subViewPath;
            } 
            else {
                echo "<p>Erro: A visão '$subView' não foi encontrada.</p>";
            }
        } else {
            echo "<p>Bem-vindo!</p>";
        }
        ?>
    </main>
</div>