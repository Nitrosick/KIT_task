<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/global/global.php';

    if (!$is_admin) {
        header('Location: /');
        die();
    }

    $db = DB::get_connect();
    $info = null;

    if (!$db) {
        $info = 'Connection failed';
    } else {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'logout') {
                unset($_SESSION['user_id']);
                unset($_SESSION['user']);
                unset($_SESSION['is_admin']);

                header('Location: /');
            }
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
    <link rel="stylesheet" href="/admin/admin.css">
    <title>KIT | Admin</title>
</head>
<body>
    <header class="header">
        <a href="/" class="header_logo">kit</a>
        <nav class="header_links">
            <form method="post" action="#">
                <input type="hidden" name="action" value="logout">
                <button class="header_links_item">logout</button>
            </form>
        </nav>
    </header>
    <main class="content">
        <form action="#" class="add_form">
            <input type="text" class="add_form_name" placeholder="name">
            <input type="text" class="add_form_desc" placeholder="description">
            <select name="parent_id" id="parent_id" class="add_form_parent">
                <?= $parents ?>
            </select>
            <button type="submit" class="add_form_submit">add</button>
        </form>

        <div class="object root" id="1">
            <div class="object_header">
                <input
                    class="object_name"
                    type="text"
                    value="Root"
                    data-id="1"
                >
            </div>
            <input
                class="object_description"
                type="text"
                value="Root description text"
                data-id="1"
            >
            <!-- Content -->
        </div>

        <div class="info">
            <?php if ($info) echo $info; ?>
        </div>
    </main>
</body>
<script src="/admin/admin.js"></script>
</html>
