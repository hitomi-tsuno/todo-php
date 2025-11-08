<?php
$db_path = 'todos.db';

// DB接続/作成
$db = new PDO('sqlite:' . $db_path);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_POST['action'] ?? '';
switch ($action) {
  case 'add':
    $stmt = $db->prepare("INSERT INTO todos (id, text, isdone) VALUES (?, ?, 0)");
    $id = (int)round(microtime(true) * 1000);
    $stmt->execute([$id, $_POST['text']]);
    echo json_encode(['status' => 'ok', 'id' => $id]);
    break;

  case 'delete':
    $stmt = $db->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    echo json_encode(['status' => 'ok']);
    break;

  case 'toggle':
    $stmt = $db->prepare("UPDATE todos SET isdone = NOT isdone WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    echo json_encode(['status' => 'ok']);
    break;

  case 'list':
    $todos = $db->query("SELECT * FROM todos ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($todos);
    break;
}