<?php
require_once "components_prelude.php";

function notification($notif) { ?>
<div class="notification-item p-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <h6 class="fw-bold mb-0"> <?= htmlspecialchars($notif['title']) ?> </h6>
        <button type="button" class="btn-close" aria-label="Close"></button>
    </div>
    <p class="notification-content text-muted mb-2">
    <?= !empty($notif['description'])
            ? htmlspecialchars($notif['description'])
            : ""
    ?>
    </p>
    <div class="d-flex align-items-center justify-content-between mt-2">
        <p class="small text-muted mb-0">
            <?= date('d/m/Y h:i', strtotime($notif['notification_datetime'])) ?>
        </p>
        <?php if (!empty($notif['action_link'])): ?>
            <a href="<?= htmlspecialchars($notif['action_link']) ?>" class="btn btn-sm btn-primary">
                Vai
            </a>
        <?php endif; ?>
    </div>
</div>
<?php } ?>