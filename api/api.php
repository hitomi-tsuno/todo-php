<?php
$db_path = '../data/todos.db';

// DB接続/作成
$db = new PDO('sqlite:' . $db_path);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_POST['action'] ?? '';
switch ($action) {
  case 'add':
    $stmt = $db->prepare("INSERT INTO todos (id, text, isdone, tags) VALUES (?, ?, 0, ?)");
    $id = (int)round(microtime(true) * 1000);
    $stmt->execute([$id, $_POST['text'], $_POST['tags']]);
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
    $id = $_POST['id'] ?? '';
    $text = $_POST['text'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $stmt = $db->prepare("UPDATE todos SET text = ?, tags = ? WHERE id = ?");
    $stmt->execute([$text, $tags, $id]);
    break;

  case 'list_tags':
    $sql = "SELECT DISTINCT tags FROM todos WHERE tags <> '' ORDER BY tags ASC";
    $params = [];
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($todos);
    break;

  case 'list':
    $filterIsDone = $_POST['filterIsDone'] ?? null;
    $filterText = $_POST['filterText'] ?? '';
    $filterTags = $_POST['filterTags'] ?? [];

    $sql = "SELECT * FROM todos WHERE 1";
    $params = [];

    // フィルター処理
    if ($filterIsDone === "0") {
      $sql .= " AND isdone = 1";
    } elseif ($filterIsDone === "1") {
      $sql .= " AND isdone = 0";
    }

    if ($filterText !== '') {
      $sql .= " AND text LIKE ?";
      $params[] = '%' . $filterText . '%';
    }

    foreach ($filterTags as $tag) {
      $sql .= " AND tags LIKE ?";
      $params[] = '%' . $tag . '%';
    }

    // ソート処理
    $sortKey = $_POST['sortKey'] ?? 'id';
    $sortOrder = $_POST['sortOrder'] ?? 'desc';

    // 安全なカラム名と順序だけ許可
    $allowedKeys = ['id', 'text', 'isdone', 'tags'];
    $allowedOrders = ['asc', 'desc'];

    if (!in_array($sortKey, $allowedKeys)) $sortKey = 'id';
    if (!in_array($sortOrder, $allowedOrders)) $sortOrder = 'desc';

    $sql .= " ORDER BY $sortKey $sortOrder";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($todos);
    break;
}
