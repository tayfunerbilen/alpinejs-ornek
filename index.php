<?php

header("Access-Control-Allow-Origin: *");

try {
    $db = new PDO('mysql:host=localhost;dbname=todo', 'root', 'root');
} catch(PDOException $e) {
    die($e->getMessage());
}

$action = $_POST['action'];
$response = [];

switch ($action) {

    // todoları listele
    case 'todos':

        $response = $db->query('select * from todos order by id desc')->fetchAll(PDO::FETCH_ASSOC);

        break;

    // yeni todo ekleme
    case 'add-todo':

        $todo = $_POST['todo'];
        $data = [
            'todo' => $todo,
            'done' => 0
        ];
        
        $query = $db->prepare('INSERT INTO todos SET todo = :todo, done = :done');
        $insert = $query->execute($data);

        if ($insert) {
            $data['id'] = $db->lastInsertId();
            $response = $data;
        } else {
            $response['error'] = 'Bir sorun oluştu ve todo eklenemedi!';
        }

        break;

    case 'delete-todo':

        $id = $_POST['id'];
        if (!$id) {
           $response['error'] = 'ID eksik olamaz!';
        } else {

            $delete = $db->exec('delete from todos where id = "' . $id . '"');
            if ($delete) {
                $response['deleted'] = true;
            } else {
                $response['error'] = 'Todo silinemedi!';
            }

        }

        break;

    case 'done-todo':

        $id = $_POST['id'];
        $done = $_POST['done'];
        if (!$id) {
            $response['error'] = 'ID eksik olamaz!';
        } else {

            $query = $db->prepare('select id from todos where id = :id');
            $query->execute([
                'id' => $id
            ]);
            $todo = $query->fetch(PDO::FETCH_ASSOC);
            if (!$todo) {
                $response['error'] = 'Gönderdiğiniz idye ait todo bulunamadı!';
            } else {

                // todo güncelle
                $query = $db->prepare('update todos set done = :done where id = :id');
                $update = $query->execute([
                    'id' => $id,
                    'done' => $done
                ]);

                if ($update) {
                    $response['done'] = true;
                } else {
                    $response['error'] = 'Todo güncellenirken bir sorun oluştu';
                }

            }

        }

        break;

}

echo json_encode($response);