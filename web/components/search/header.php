<?php 
namespace Search;

function header($title, $round = false) { ?>
<div class="p-2 bg-primary-subtle border-bottom <?= $round ? 'rounded-top' : '' ?>">
    <small class="fw-bold"> <?= htmlspecialchars($title) ?> </small>
</div>
<?php } ?>