<?php
$db_path = '../data/todos.db';

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

  case 'delete_done':
    $stmt = $db->prepare("DELETE FROM todos WHERE isdone = 1");
    $stmt->execute();
    echo json_encode(['status' => 'ok']);
    break;

  case 'toggle':
    $stmt = $db->prepare("UPDATE todos SET isdone = NOT isdone WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    echo json_encode(['status' => 'ok']);
    break;

  case 'toggle_all':
    $stmt = $db->prepare("UPDATE todos SET isdone = ?");
    $stmt->execute([$_POST['isdone']]);
    echo json_encode(['status' => 'ok']);
    break;

  case 'update':
    $stmt = $db->prepare("UPDATE todos SET text = ? WHERE id = ?");
    $stmt->execute([$_POST['text'], $_POST['id']]);
    echo json_encode(['status' => 'ok']);
    break;

  case 'list':
    $filterIsDone = $_POST['filterIsDone'] ?? null;
    $filterText = $_POST['filterText'] ?? '';

    $sql = "SELECT * FROM todos WHERE 1";
    $params = [];

    if ($filterIsDone === "0") {
      $sql .= " AND isdone = 1";
    } elseif ($filterIsDone === "1") {
      $sql .= " AND isdone = 0";
    }

    if ($filterText !== '') {
      $sql .= " AND text LIKE ?";
      $params[] = '%' . $filterText . '%';
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($todos);
    break;
}
