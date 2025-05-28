<?php
namespace Search;
require_once "$root/functions/user.php";

function user_result(int $id, string $name, string $surn, int|null $date, string|null $body) { 
    $resp = \User\project_count($id);
    ?>
    <div class="card m-3">
        <div class="card-header clearfix py-3">
            <h5 class="float-start mb-0">
                <b> <?= htmlspecialchars("$name $surn") ?> </b>
            
                <?php if ($resp->is_ok()): ?>
                    <span> (<?= $resp->data ?> <?= $resp->data == 1 ? 'progetto' : 'progetti' ?>) </span>
                <?php endif; ?>
            </h5>
            <small class="text-muted float-end"><?= date('d/m/Y', $date) ?> </small>
        </div>
        <div class="card-body ms-4 ps-1 mt-1">
            <h6>Biografia</h6>
            <small class="text-muted pre-line"><?= $body === null ? "<i> L'utente non ha una biografia </i>" : htmlspecialchars($body) ?></small>
        </div>
        <div class="card-footer clearfix py-2">
            <a href="/app/user/profile.php?id=<?=htmlspecialchars($id)?>" class="btn btn-outline btn-outline-primary float-end m-0"> Vai </a>
        </div>
    </div>

<?php }

function project_result(int $id, string $title, string $auth_name, string $auth_surn, int|null $date, string|null $body) { ?>
<div class="card m-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div>
            <h5 class="mb-1 fw-bold"> <?= htmlspecialchars($title) ?> </h5>
            <span> Di <?= htmlspecialchars("$auth_name $auth_surn") ?> </span>
        </div>
        <small class="text-muted"><?= date('d/m/Y', $date) ?> </small>
    </div>
    <div class="card-body ms-4 ps-1 mt-1">
        <h6>Abstract</h6>
        <small class="text-muted pre-line mb-0"><?= $body === null ? "<i> Il progetto non ha un abstract </i>" : htmlspecialchars($body) ?></small>
    </div>
    <div class="card-footer clearfix">
        <a href="/app/project/details.php?id=<?=htmlspecialchars($id)?>" class="btn btn-outline btn-outline-primary float-end m-0">Vai</a>
        </a>
    </div>
</div>
<?php } 

function empty_result() {?>
<div class="search-item p-3">
    <p class="text-center italic mb-0"> Nessun risultato trovato </p>
</div>
<?php } ?>