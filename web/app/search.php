<?php require_once 'prelude.php'?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php
    include "$root/components/head.php";
    include "$root/components/navbar.php";
    include "$root/components/search.php";
    include "$root/components/search/result.php";
    include "$root/components/footer.php";
    $query = $_GET['q'] ?? '';
    ?>
    <title> Ricerca </title>
</head>
<body>
    <?php navbar(1) ?>

    <h1 class="text-center mt-2">Risultati della ricerca</h1>

    <?php 
        search($query);
    ?>

    <main class="container my-4">
        <div class="mt-3">
            <h3 class="text-center"> Progetti </h3>
            <?php
                $prjs = Suggestions\projects($query, 5, 200, false)->data;

                if ($prjs['count'] == 0) {
                    Search\empty_result();
                } else {
                    foreach ($prjs['content'] as $p) {
                        Search\project_result(
                            $p['project_id'],
                            $p['title'],
                            $p['name'],
                            $p['surname'],
                            strtotime($p['project_datetime']),
                            $p['abstract']
                        );
                    }
                }
            ?>
        </div>

        <hr>

        <div class="mt-3">
            <h3 class="text-center"> Utenti </h3>
            <?php
                $users = Suggestions\users($query, 5, 200, false)->data;
                if ($users['count'] == 0) {
                    Search\empty_result();
                } else {
                    foreach($users['content'] as $user) {
                        $title = $user['name'] . ' ' . $user['surname'];
                        Search\user_result(
                            $user['user_id'],
                            $user['name'],
                            $user['surname'],
                            strtotime($user['user_datetime']),
                            $user['bio']
                        );
                    }
                }
            ?>
        </div>
    </main>

    <?php footer() ?>
</body>
</html>