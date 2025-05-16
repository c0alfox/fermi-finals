<?php
namespace Search;

function user_element(int $id, string $name, string $surn, int|null $date, string|null $body) { ?>
<a href="/app/user/profile.php?id=<?=htmlspecialchars($id)?>" class="search-link">
    <div class="search-item px-3 py-2 border-bottom">
        <div class="clearfix">
            <h5 class="float-start mb-0"><?= htmlspecialchars("$name $surn") ?> </h5>
            <small class="text-muted float-end"><?= date('d/m/Y', $date) ?> </small>
        </div>
        <div class="ms-4 ps-1 mt-1">
            <small class="text-muted">
                <?= $body === null ? "<i> L'utente non ha una biografia </i>" : htmlspecialchars($body) ?>
            </small>
        </div>
    </div>
</a>

<?php }

function project_element(int $id, string $title, string $auth_name, string $auth_surn, int|null $date, string|null $body) { ?>
<a href="/app/project/details.php?id=<?=htmlspecialchars($id)?>" class="search-link">
    <div class="search-item px-3 py-2 border-bottom">
        <div class="clearfix">
            <div class="float-start">
                <h5 class="mb-0"> <?= htmlspecialchars($title) ?> </h5>
                <span class="small text-muted"> Di <?= htmlspecialchars("$auth_name $auth_surn") ?> </span>
            </div>
            <small class="text-muted float-end"><?= date('d/m/Y', $date) ?> </small>
        </div>
        <div class="ms-4 ps-1 mt-1">
            <small class="text-muted">
                <?= $body === null ? "<i> Il progetto non ha un abstract </i>" : htmlspecialchars($body) ?>
            </small>
        </div>
    </div>
</a>
<?php } 

function empty_element() {?>
<div class="search-item p-3">
    <p class="text-center italic mb-0"> Nessun risultato trovato </p>
</div>
<?php } ?>