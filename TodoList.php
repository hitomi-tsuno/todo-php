<!-- TodoList.php -->
<?php
// phpinfo();  // XDebugの動作確認用 →有効でした

session_start(); // セッション開始

// // セッションを破棄する　（動作確認用）
// $_SESSION = array();
// session_destroy();

// 初期化（初回のみ）
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [
        ["id" => 1, "text" => "牛乳を買う", "isdone" => false],
        ["id" => 2, "text" => "メール返信", "isdone" => false],
        ["id" => 3, "text" => "Reactの復習", "isdone" => false]
    ];
}

// Todoの追加
if (isset($_POST['add']) && !empty($_POST['text'])) {
    $todo = [
        'id' => time(),
        'text' => $_POST['text'],
        "isdone" => false,
    ];
    array_push($_SESSION['todos'], $todo);
}

// Todoの削除
if (isset($_POST['delete'])) {
    $deleteId = (int)$_POST['delete_id'];
    $_SESSION['todos'] = array_filter($_SESSION['todos'], function ($todo) use ($deleteId) {
        return $todo['id'] !== $deleteId;
    });
}

// Todoの完了・未完了切り替え
elseif (isset($_POST['isdone_id'])) {
    $isdoneId = (int)$_POST['isdone_id'];
    foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $isdoneId) {
            $todo['isdone'] = !$todo['isdone'];
            break;
        }
    }
    unset($todo); // 参照の解放
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Todoリスト</title>
</head>

<body>
    <h1>Todoリスト</h1>

    <form method="POST">
        <!-- ＜追加＞テキストボックス -->
        <input type="text" name="text" placeholder="新しいTODOを入力" required>
        <!-- ＜追加＞ボタン -->
        <button type="submit" name="add">追加</button>
    </form>

    <ul>
        <?php foreach ($_SESSION['todos'] as $todo): ?>
            <li>
                <!-- ✅ ＜完了＞チェックボックス -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="isdone_id" value="<?php echo $todo['id']; ?>">
                    <input type="checkbox" name="isdone"
                        onchange="this.form.submit()"
                        <?php if ($todo['isdone']) echo 'checked'; ?>>
                </form>
                <!-- ＜TODO内容＞ -->
                <?php echo htmlspecialchars($todo['text']); ?>
                <!-- ＜登録日＞ -->
                <?php echo htmlspecialchars($todo['id']); ?>
                <!-- ＜削除＞ボタン -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $todo['id']; ?>">
                    <button type="submit" name="delete">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>