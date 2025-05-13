<?php
namespace Search;

function element(string $title, int|null $date, string $body) { ?>
<div class="search-item p-3">
    <div class="clearfix">
        <div class="float-start"><?= htmlspecialchars($title) ?> </div>
        <small class="text-muted float-end"><?= date('d/m/Y', $date) ?> </small>
    </div>
    <div class="ms-4 ps-1 mt-1">
        <small class="text-muted">
            <?= htmlspecialchars($body) ?>
        </small>
    </div>
</div>
<?php } 

function empty_element() {?>
<div class="search-item p-3">
    <p class="text-center italic"> Nessun risultato trovato </p>
</div>
<?php } ?>