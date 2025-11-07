<!-- TodoList.php -->
<?php
$db_path = 'todos.db';

// DB接続/作成
$db = new PDO('sqlite:' . $db_path);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Todoの追加
if (isset($_POST['add']) && !empty($_POST['text'])) {
    // ＜　INSERT文の実行　＞
    $stmt = $db->prepare("INSERT INTO todos (id, text, isdone) VALUES (?, ?, 0)");
    $id = (int)round(microtime(true) * 1000);
    $stmt->execute([$id, $_POST['text']]);
}

// Todoの削除
if (isset($_POST['delete'])) {
    // ＜　DELETE文の実行　＞
    $deleteId = (int)$_POST['delete_id'];
    $stmt = $db->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->execute([$deleteId]);
}

// Todoの完了・未完了切り替え
if (isset($_POST['isdone_id'])) {
    // ＜　UPDATE文の実行　＞
    $isdoneId = (int)$_POST['isdone_id'];
    $stmt = $db->prepare("UPDATE todos SET isdone = NOT isdone WHERE id = ?");
    $stmt->execute([$isdoneId]);
}

// ＜　SELECT文の実行　＞
$todos = $db->query("SELECT * FROM todos ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Todoリスト</title>
    <!-- CSSで .done クラスに打ち消し線を追加 -->
    <style>
        .done {
            text-decoration: line-through;
        }
    </style>

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
        <?php foreach ($todos as $todo): ?>
            <li>
                <!-- ✅ ＜完了＞チェックボックス -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="isdone_id" value="<?php echo $todo['id']; ?>">
                    <input type="checkbox" name="isdone"
                        onchange="this.form.submit()"
                        <?php if ($todo['isdone']) echo 'checked'; ?>>
                </form>
                <!-- ＜TODO内容＞ -->
                <span class="<?= $todo['isdone'] ? 'done' : '' ?>">
                    <?= htmlspecialchars($todo['text']) ?>
                </span>
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