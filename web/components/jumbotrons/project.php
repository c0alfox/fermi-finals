<?php 
namespace Jumbotron;

function project($project_suggestion) { ?>
<a href="/app/project/details.php?id=<?=$project_suggestion['project_id']?>" class="search-link">
    <div class="m-2 p-4 position-absolute top-0 start-0 end-0 bottom-0 border border-primary bg-light shadow-sm rounded project-jumbotron">
        <div>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="text-primary bold fw-bold mb-0"> <?= htmlspecialchars($project_suggestion['title']) ?> </h2>
                <p class="small mb-0"> <?= date("d/m/Y", strtotime($project_suggestion['project_datetime'])) ?> </p>
            </div>

            <p class="pt-1">
                Di <?= htmlspecialchars($project_suggestion['name'] . ' ' . $project_suggestion['surname']) ?>
            </p>
        </div>
        <div class="w-75 mx-auto d-flex align-items-center justify-content-center h-100">
            <?php if ($project_suggestion['abstract'] === null): ?>
                <p class="italic"> Il progetto non ha un abstract </p>
            <?php else: ?>
                <p> <?= htmlspecialchars($project_suggestion['abstract']) ?> </p>
            <?php endif; ?>
        </div>
    </div>
</a>
<? } ?>