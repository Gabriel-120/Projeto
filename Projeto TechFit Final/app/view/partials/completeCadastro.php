<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Concluir Cadastro</h2>

    <?php if (!empty($message)): ?>
        <div class="mb-4 text-green-700"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="/profile?page=concluir">
        <input type="hidden" name="action" value="concluir_cadastro">
        <h3 class="font-semibold">Dados básicos</h3>
        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_email ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>CPF</label>
            <input type="text" name="cpf" class="form-control" value="<?php echo htmlspecialchars($user_cpf ?? ''); ?>">
        </div>

        <h3 class="font-semibold mt-4">Informações adicionais</h3>
        <div class="form-group">
            <label>Gênero</label>
            <input type="text" name="genero" class="form-control" value="<?php echo htmlspecialchars($aluno['genero'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Endereço</label>
            <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars($aluno['endereco'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($aluno['telefone'] ?? ''); ?>">
        </div>

        <div class="mt-4">
            <button class="btn-primary" type="submit">Confirmar e Concluir Cadastro</button>
        </div>
    </form>
</div>
<script>
    // CPF mask for partial
    const cpfField = document.querySelector('input[name="cpf"]');
    if (cpfField) {
        cpfField.addEventListener('input', function(){
            let v = this.value.replace(/\D/g,'');
            if (v.length > 11) v = v.slice(0,11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
        });
    }

    function validateCPF(cpf){
        cpf = cpf.replace(/\D/g,'');
        if (cpf.length !== 11) return false;
        if (/^(\d)\1+$/.test(cpf)) return false;
        let sum = 0, rest;
        for (let i=1;i<=9;i++) sum = sum + parseInt(cpf.substring(i-1,i)) * (11 - i);
        rest = (sum * 10) % 11;
        if ((rest === 10) || (rest === 11)) rest = 0;
        if (rest !== parseInt(cpf.substring(9,10))) return false;
        sum = 0;
        for (let i=1;i<=10;i++) sum = sum + parseInt(cpf.substring(i-1,i)) * (12 - i);
        rest = (sum * 10) % 11;
        if ((rest === 10) || (rest === 11)) rest = 0;
        if (rest !== parseInt(cpf.substring(10,11))) return false;
        return true;
    }

    const form = document.querySelector('form[action="/profile?page=concluir"]');
    if (form) {
        form.addEventListener('submit', function(e){
            const cpf = cpfField ? cpfField.value : '';
            if (cpf && !validateCPF(cpf)) { e.preventDefault(); alert('CPF inválido'); return false; }
        });
    }
</script>
