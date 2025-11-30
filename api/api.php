<?php
//<!-- api\api.php -->
// true = 開発モード, false = 本番モード
define("DEBUG_MODE", true);

$db_path = '../data/todos.db';

// DB接続/作成
$db = new PDO('sqlite:' . $db_path);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_POST['action'] ?? '';
try {
  switch ($action) {
    case 'add':
      $sql = "INSERT INTO todos (id, text, isdone, tags) VALUES (?, ?, 0, ?)";
      $stmt = $db->prepare($sql);
      $id = (int)round(microtime(true) * 1000);
      $stmt->execute([$id, $_POST['text'], $_POST['tags']]);
      echo json_encode(['status' => 'ok', 'id' => $id]);
      break;

    case 'delete':
      $sql = "DELETE FROM todos WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$_POST['id']]);
      echo json_encode(['status' => 'ok']);
      break;

    case 'delete_done':
      $sql = "DELETE FROM todos WHERE isdone = 1";
      $stmt = $db->prepare($sql);
      $stmt->execute();
      echo json_encode(['status' => 'ok']);
      break;

    case 'toggle':
      $sql = "UPDATE todos SET isdone = NOT isdone WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$_POST['id']]);
      echo json_encode(['status' => 'ok']);
      break;

    case 'toggle_all':
      $sql = "UPDATE todos SET isdone = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$_POST['isdone']]);
      echo json_encode(['status' => 'ok']);
      break;

    case 'update':
      $id = $_POST['id'] ?? '';
      $text = $_POST['text'] ?? '';
      $tags = $_POST['tags'] ?? '';
      $sql = "UPDATE todos SET text = ?, tags = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
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

      // タグで絞り込み　複数タグ対応　OR条件を組み立て
      $conditions = [];
      foreach ($filterTags as $tag) {
        $conditions[] = "tags LIKE '%" . $tag . "%'";
      }
      if (count($conditions) > 0) {
        $sql .= " AND (" . implode(" OR ", $conditions) . ")";
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

      // $sql .= " bbbbb"; // Try・Catchテスト用

      $stmt = $db->prepare($sql);
      $stmt->execute($params);
      $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      echo json_encode($todos);
      break;
  }
} catch (Exception $e) {
  // ログにエラー出力（PHPの関数を使用）
  error_log($e->getMessage());
  http_response_code(500);
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

  // エラー時にSQLと値をログ出力
  // [注意]
  // $sqlに値を設定する
  logError($e->getMessage(),  ['text' => $text, 'id' => $id, 'tags' => $tags, 'params' => $params]);
}

function logError($error,  $sql = null, $values = null)
{
  $logFile = "C:/work/study/php/my-todo-app/log/error.log";

  if (DEBUG_MODE) {
    // 開発モード: SQLと値を詳細に出力
    // <日付>
    error_log(str_repeat("－", 50) . PHP_EOL, 3, $logFile);
    error_log(date('Y/m/d H:i:s') . PHP_EOL, 3, $logFile);
    error_log('エラー内容: ' . $error . PHP_EOL, 3, $logFile);
    error_log('$sql: ' . $sql . PHP_EOL, 3, $logFile);
    error_log('$_POST: ' . json_encode($_POST) . PHP_EOL, 3, $logFile);
    error_log('Values: ' . json_encode($values) . PHP_EOL, 3, $logFile);
  } else {
    // 本番モード: 最小限の情報のみ
    // 未出力
  }
}
