<!-- Create_sqlite_todos_db.php -->
<!-- 
 todos.jsonよりtodos.dbを作成するツールです。 
 一回のみ実行してください
 -->

<?php
$db_path = 'todos.db';
$file_path = 'todos.json';

// 初回のみ、DB作成・テーブル作成
if (file_exists($db_path)) {
    // ファイルが存在すれば削除する
    if (unlink($db_path)) {
        echo "ファイルが正常に削除されました。";
    } else {
        echo "ファイルの削除に失敗しました。";
    }
}

// DB接続/作成
$db = new PDO('sqlite:todos.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// テーブル作成
$db->exec("CREATE TABLE IF NOT EXISTS todos (
id INTEGER PRIMARY KEY,
text TEXT NOT NULL,
isdone INTEGER NOT NULL DEFAULT 0
)");

// JSONファイルからデータを読み込み、DBに挿入
$json = file_get_contents($file_path);
$todos = json_decode($json, true);

$stmt = $db->prepare("INSERT INTO todos (id, text, isdone) VALUES (?, ?, ?)");

foreach ($todos as $todo) {
    $stmt->execute([
        (int)$todo['id'],
        $todo['text'],
        $todo['isdone'] ? 1 : 0
    ]);
}

echo "SQLiteデータベースの初期化が完了しました。";
?>