<?php 
namespace Search;

function header($title) { ?>
<div class="p-2 bg-light border-bottom">
    <small class="text-muted fw-bold"> <?= htmlspecialchars($title) ?> </small>
</div>
<?php } ?>