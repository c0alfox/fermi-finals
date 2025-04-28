<?php 
require_once 'prelude.php';
require_once "$root/auth/authentication.php";

function navbar() {?>
<nav id="navbar_id" class="navbar navbar-expand-lg fixed-top bg-primary bg-gradient">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav me-auto">
                <a class="nav-link active" aria-current="page" href="/index.php">Home</a>
                <a class="nav-link" href="">Features</a>
            </div>
            <div>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        if (!Auth\has_valid_jwt()) {
                            echo "Accedi o Registrati";
                        } else {
                            echo $_SESSION['Username'];
                        }
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <?php if (empty($_SESSION['Username'])): ?>
                            <li><a class="dropdown-item" href="login.php">Accedi</a></li>
                            <li><a class="dropdown-item" href="signup.php">Registrati</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="friends.php">Amici</a></li>
                            <li><a class="dropdown-item" href="preferences.php">Impostazioni</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login.php">Cambia account</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<?php } ?>