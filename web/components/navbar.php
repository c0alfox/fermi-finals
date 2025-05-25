<?php
require_once 'components_prelude.php';
require_once "navbar/notification.php";

require_once "$root/auth/authentication.php";
require_once "$root/functions/user.php";
require_once "$root/functions/notifications.php";

$user_id = Auth\get_user_id();
$userstring = User\get_userstring($user_id);

if ($user_id !== null): ?>
<script type="module" src="/static/js/components/navbar.js" defer></script>
<?php endif;

function navbar() { 
    global $user_id, $userstring;
    ?>
    <nav id="navbar_id" class="navbar navbar-expand-lg fixed-top bg-primary bg-gradient">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                    <!--<a class="nav-link" href="">Features</a>-->
                </div>

                <?php if ($userstring !== null):
                    $count = Notifications\count($user_id)->data;
                    $notifs = Notifications\fetch($user_id)->data; ?>
                <div>
                    <div class="nav-item dropdown position-relative d-inline">
                        <a class="nav-link px-3 position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i class="icon notification-bell"></i>
                            <span id="notification-badge" class="notification-badge badge rounded-pill bg-danger 
                                <?= $count == 0 ? 'd-none' : '' ?>"> <?= $count ?> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow notification-list p-0" aria-labelledby="notificationDropdown">
                            <div class="d-flex flex-column">
                                <div class="dropdown-header p-3">
                                    <h6 class="mb-0 text-center">
                                        <?= $count == 0 
                                                ? 'Nessuna notifica'
                                                : 'Notifiche'
                                        ?>
                                    </h6>
                                </div>
                                <?php foreach ($notifs as $n) {
                                    notification($n);
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
