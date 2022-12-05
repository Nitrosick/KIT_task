<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/global/global.php';

if (!$is_admin) {
    header('Location: /');
    die();
}

$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['action'])) {
    $db = DB::get_connect();

    if (!$db) {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }

    switch ($_POST['action']) {
        case 'change':
            change($db, $_POST);
            break;
        case 'remove':
            remove($db, $_POST['id']);
            break;
        case 'add':
            add($db, $_POST);
            break;
        case 'get_list':
            get_list($db);
            break;
        case 'get_parents':
            get_parents($db);
            break;
    }
}

function change($db, $post) {
    if ($post['parent_id']) {
        $select = $db->query("
            SELECT parent_id
            FROM objects
            WHERE id = {$post['parent_id']}
            LIMIT 1;
        ");
        $check = $select->fetch();

        if ($check['parent_id'] === $post['id']) {
            echo json_encode([ 'status' => 'failed' ]);
            die();
        }
    }

    $update = "
        UPDATE objects SET
            name = :name,
            description = :description,
            parent_id =
            CASE
                WHEN :parent_id = 0 THEN parent_id
                ELSE :parent_id
            END
        WHERE id = :id
        AND id != 1
        AND :parent_id != :id;
    ";

    $result = $db->prepare($update);
    $result->bindParam(':id', $post['id'], PDO::PARAM_INT);
    $result->bindParam(':name', $post['name'], PDO::PARAM_STR);
    $result->bindParam(':description', $post['description'], PDO::PARAM_STR);
    $result->bindParam(':parent_id', $post['parent_id'], PDO::PARAM_INT);
    $result->execute();

    if ($result->rowCount()) {
        echo json_encode([ 'status' => 'ok' ]);
        die();
    } else {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }
}

function remove($db, $id) {
    $delete = "
        DELETE FROM objects
        WHERE id = :id
        AND id != 1;
    ";

    $result = $db->prepare($delete);
    $result->bindParam(':id', $id, PDO::PARAM_INT);
    $result->execute();

    if ($result->rowCount()) {
        echo json_encode([ 'status' => 'ok' ]);
        die();
    } else {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }
}

function add($db, $post) {
    if (!$post['name'] || !$post['description']) {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }

    $insert = "
        INSERT INTO objects(name, description, parent_id)
        VALUES (:name, :description, :parent_id);
    ";

    $result = $db->prepare($insert);
    $result->bindParam(':name', $post['name'], PDO::PARAM_STR);
    $result->bindParam(':description', $post['description'], PDO::PARAM_STR);
    $result->bindParam(':parent_id', $post['parent_id'], PDO::PARAM_INT);
    $result->execute();

    if ($result->rowCount()) {
        echo json_encode([ 'status' => 'ok', 'id' => $db->lastInsertId() ]);
        die();
    } else {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }
}

function get_list($db) {
    try {
        $array = [];
        $result = $db->query("
            SELECT
                id,
                name,
                description,
                parent_id
            FROM objects
            WHERE parent_id IS NOT NULL;
        ");

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { $array[] = $row; }

        echo json_encode([
            'status' => 'ok',
            'list' => $array
        ]);
        die();

    } catch (\Throwable $th) {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }
}

function get_parents($db) {
    try {
        $array = [];
        $result = $db->query("
            SELECT
                id,
                name
            FROM objects;
        ");

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { $array[] = $row; }

        echo json_encode([
            'status' => 'ok',
            'list' => $array
        ]);
        die();

    } catch (\Throwable $th) {
        echo json_encode([ 'status' => 'failed' ]);
        die();
    }
}
