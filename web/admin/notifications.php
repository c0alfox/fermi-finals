<?php
require_once "prelude.php";
require_once "$root/functions/user.php";
require_once "$root/functions/request.php";
require_once "$root/functions/notifications.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);  # Method not allowed
    die();
}

$pdo = connect();
if ($pdo === null) {
    http_response_code(500);
    die();
}

try {
    $s = $pdo->prepare("SELECT user_id, name, surname FROM PrgUsers");
    $s->execute();
    $users = $s->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    http_response_code(500);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (has_required_parameters($_POST, ['title', 'user_id'])) {
        $notif = Notifications\create(
            $_POST['title'],
            empty($_POST['description']) ? null : $_POST['description'],
            empty($_POST['action_link']) ? null : $_POST['action_link'],
            $_POST['user_id']
        )->die_if_error()->data;
        $notif['notification_datetime'] = datetime_to_rust($notif['notification_datetime']);
        $notif = json_encode($notif);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "http://wss:8888/api/notifs/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $notif);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curl, CURLOPT_TIMEOUT,30);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Accept:application/json'
        ]);
        curl_setopt($curl, CURLOPT_STDERR, fopen('php://stderr', 'w'));

        $result = curl_exec($curl);

        curl_close($curl);
    }
}

?>
<!DOCTYPE html>
<html lang="it">

<head>
    <?php include "$root/components/head.php" ?>
    <style>
        body, html {
            min-height: 100%;
        }
    </style>

    <title> Aggiungi notifica </title>
</head>

<body class="bg-light">
    <div class="card shadow-md w-50 mx-auto mt-5">
        <div class="card-header text-bg-primary bg-gradient">
            <h2 class="text-center mb-0"> Aggiungi notifica </h2>
        </div>
        <div class="card-body">
            <form class="p-4" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Titolo</label>
                    <input type="title" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Descrizione</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="action_link" class="form-label">Action Link</label>
                    <input type="action_link" class="form-control" id="action_link" name="action_link">
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-control form-select" required>
                    <?php foreach($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>">
                            <?= htmlspecialchars(
                                $user['name'] . ' ' . $user['surname'] . ' (' . $user['user_id'] . ')'
                            ) ?>
                        </option>
                    <?php endforeach ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary bg-gradient shadow-sm d-block mx-auto">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>