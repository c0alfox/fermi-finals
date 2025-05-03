<?php 
require_once 'prelude.php';
require_once "$root/auth/authentication.php";
require_once "$root/functions/user.php";

function navbar() {
    $userstring = User\get_userstring(Auth\get_user_id());
    ?>
<nav id="navbar_id" class="navbar navbar-expand-lg fixed-top bg-primary bg-gradient">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav me-auto">
                <a class="nav-link active" aria-current="page" href="/">Home</a>
                <a class="nav-link" href="">Features</a>
            </div>
            <div>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if ($userstring === null): ?>
                        Accedi o Registrati
                    <?php else: ?>
                        Benvenuto <?= $userstring ?>
                    <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php if ($userstring === null): ?>
                        <li><a class="dropdown-item" href="/app/auth/login.php">Accedi</a></li>
                        <li><a class="dropdown-item" href="/app/auth/signup.php">Registrati</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="/app/user/profile.php">Profilo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/app/auth/login.php">Cambia account</a></li>
                        <li><a class="dropdown-item" href="/app/auth/logout.php">Logout</a></li>
                    <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<div style="height: 60px;"></div>
<?php } ?>