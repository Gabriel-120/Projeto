<nav class="navbar navbar-expand-lg bg-body-tertiary mb-10 rounded">
  <div class="container-fluid">
    <a href=  "/" class="navbar-brand col-lg-3 me-0">
      <img src="/assets/images/logo.png" width="62" alt="Logo" class="d-inline-block align-text-center">
      TechFit
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navLinks" aria-controls="navLinks" aria-expanded="false"
      aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class=" navbar-collapse d-lg-flex" id="navLinks">
      <ul class="navbar-nav col-lg-6 justify-content-lg-center gap-3">
        <li><a href="/" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">Início</a></li>
        <li><a href="/sobre" class="nav-link">Sobre</a></li>
        <li><a href="/planos" class="nav-link">Planos</a></li>
        <li><a href="/aulas" class="nav-link">Aulas</a></li>
        <li><a href="/comunicados" class="nav-link">Comunicados</a></li>
      </ul>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="d-lg-flex col-lg-3 gap-3 p-2 justify-content-lg-end">
          <a href="/login" class="btn btn-outline-dark">Entrar</a>
          <a href="/cadastro" class="btn btn-dark">Cadastrar-se</a>
        </div>
      <?php else: ?>
        <div class="dropdown text-end col-lg-3 d-flex justify-content-end align-items-center">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? __DIR__ .'images/upload/pfp/avatar.png') ?>"
                 alt="Foto" width="32" height="32" class="rounded-circle me-2">
            <span class="text-black">Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></span>
          </a>
          <ul class="dropdown-menu text-small shadow">
            <li><a href="/profile" class="dropdown-item">Perfil</a></li>
            <li><a href="/profile?page=configuracao" class="dropdown-item">Configurações</a></li>
            <li><a href="/logout" class="dropdown-item text-danger">Sair</a></li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>
