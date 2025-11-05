<!-- TodoList.php -->
<?php
// phpinfo();  // XDebugの動作確認用 →有効でした

session_start(); // セッション開始

// 初期化（初回のみ）
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [
        ["id" => time(), "text" => "牛乳を買う"],
        ["id" => time(), "text" => "メール返信"],
        ["id" => time(), "text" => "Reactの復習"]
    ];
}

// Todoの追加
if (isset($_POST['add']) && !empty($_POST['text'])) {
    $todo = [
        'id' => time(),
        'text' => $_POST['text'],
    ];
    array_push($_SESSION['todos'], $todo);
}

// Todoの削除
if (isset($_POST['delete'])) {
    // var_dump($_SESSION['todos']);   //27行目
    // var_dump($_POST['delete_id']);  //28行目

    $deleteId = (int)$_POST['delete_id'];
    $_SESSION['todos'] = array_filter($_SESSION['todos'], fn($todo) => $todo['id'] !== $deleteId);
    // ↓ デバッグ用（動作確認済みのためコメントアウト）
    // $_SESSION['todos'] = array_filter($_SESSION['todos'], function ($todo) use ($deleteId) {
    //     var_dump($todo); // 32行目
    //     return $todo['id'] !== $deleteId;
    // });
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
                <!-- ＜TODO内容＞ -->
                <?php echo htmlspecialchars($todo['text']); ?>
                <!-- ＜登録日＞ -->
                <?php echo htmlspecialchars($todo['id']); ?>
                <!-- ＜削除＞ボタン -->
                <form method="POST">
                    <input type="hidden" name="delete_id" value="<?php echo $todo['id']; ?>">
                    <button type="submit" name="delete">削除</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>