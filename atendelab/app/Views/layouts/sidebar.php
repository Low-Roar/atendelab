<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span class="brand-mark"><i class="bi bi-chat-square-text"></i></span>
        <div> 
            <strong> AtendeLab </strong>
            <small> Academic Desk </small>
        </div>
    </div> 
    <nav class="nav flex-column gap-1">
        <a class="nav-link <?= $controllerAtual === 'dashboard' ? 'active' : '' ?>"
            href="<?= url('routes.php?controller=dashboard&action=index') ?>">
            <i class="b bi-grid"> </i> Dashboard
        </a>
        <a class="nav-link <?= $controllerAtual === 'pessoas' ? 'active' : '' ?>"
            href="<?= url('routes.php?controller=pessoas&action=index') ?>">
            <i class="bi bi-journal-check"></i> Pessoas Atendidas
        </a>
        <a class="nav-link <?= $controllerAtual === 'tipos-atendimentos' ? 'active' : '' ?>"
            href="<?= url('routes.php?controller=tipos-atendimentos&action=index') ?>">
            <i class="bi bi-journal-check"></i> Tipos de Atendimento
        </a>
        <a class="nav-link <?= $controllerAtual === 'atendimentos' ? 'active' : '' ?>"
            href="<?= url('routes.php?controller=atendimentos&action=index') ?>">
            <i class="bi bi-journal-check"></i> Atendimentos
        </a>
        <?php if (($usuario['perfil'] ?? '') === 'administrador'): ?>
            <a class="nav-link <?= $controllerAtual === 'usuarios' ? 'active' : '' ?>"
                href="<?= url('routes.php?controller=usuarios&action=index') ?>">
                <i class="bi bi-journal-check"></i> Usuários
            </a>
        <?php endif; ?>
    </nav>
    <a class="nav-link logout-link" href="<? url('routes.php?controller=auth&action=logout') ?>">
        <i class="bi bi-box-arrow-left"></i> Sair
    </a>
</aside>