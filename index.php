<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/global/global.php';

    $db = DB::get_connect();
    $objects = [];
    $info = null;

    if (!$db) {
        $info = 'Connection failed';
    } else {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'login':
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    $select = "
                        SELECT
                            id,
                            password_hash,
                            is_admin
                        FROM users
                        WHERE username = :username
                    ";

                    $result = $db->prepare($select);
                    $result->bindParam(':username', $username, PDO::PARAM_STR);
                    $result->execute();
                    $user = $result->fetch();

                    if ($user) {
                        if (password_verify($password, $user['password_hash'])) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user'] = $username;
                            $_SESSION['is_admin'] = $user['is_admin'];

                            header('Location: /');
                        } else {
                            $info = 'Incorrect password';
                        }
                    } else {
                        $info = 'User not found';
                    }

                    break;
                case 'logout':
                    unset($_SESSION['user_id']);
                    unset($_SESSION['user']);
                    unset($_SESSION['is_admin']);

                    header('Location: /');
                    break;
            }
        }

        try {
            $result = $db->query("
                SELECT
                    id,
                    name,
                    description,
                    parent_id
                FROM objects
                WHERE parent_id IS NOT NULL;
            ");

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $objects[] = $row;
            }
        } catch (\Throwable $th) {
            $info = 'Data loading error';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/global/normalize.css">
    <link rel="stylesheet" href="/global/global.css">
    <link rel="stylesheet" href="/main/main.css">
    <title>KIT</title>
</head>
<body>
    <header class="header">
        <a href="#" class="header_logo">kit</a>
        <nav class="header_links">
            <?php if ($is_admin): ?>
                <a href="/admin.php" class="header_links_item">admin</a>
            <?php endif; ?>

            <?php if ($logged): ?>
                <form method="post" action="#">
                    <input type="hidden" name="action" value="logout">
                    <button class="header_links_item">logout</button>
                </form>
            <?php else: ?>
                <button class="header_links_item" id="login_open">login</button>
            <?php endif; ?>
        </nav>
    </header>
    <main class="content">
        <?php if (!$logged): ?>
            <form method="post" action="#" class="auth closed">
                <input class="auth_input" type="text" name="username" placeholder="username">
                <input class="auth_input" type="password" name="password" placeholder="password">
                <input type="hidden" name="action" value="login">

                <button class="auth_login" type="submit" id="login">login</button>
                <button class="auth_close" type="button" id="login_close">X</button>
            </form>
        <?php endif; ?>

        <div class="object closed root" id="1">
            <div class="object_header">
                <span class="object_name">Root</span>
                <button
                    class="object_info"
                    data-description="Root description text"
                >i</button>
                <button class="object_open">+</button>
            </div>
            <!-- Content -->
        </div>

        <div class="description closed">
            <span class="description_content"></span>
            <button class="description_close" id="description_close">X</button>
        </div>

        <div class="info">
            <?php if ($info) echo $info; ?>
        </div>
    </main>
</body>
<script>const objects = <?= json_encode($objects) ?></script>
<script src="/main/main.js"></script>
</html>
