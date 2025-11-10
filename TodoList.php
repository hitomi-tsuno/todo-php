<!-- TodoList.php -->
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Todoリスト</title>
</head>

<body>
    <h1>Todoリスト</h1>
    <form id="addForm">
        <input type="search" name="text" placeholder="新しいTODOを入力" required>
        <button type="submit">追加</button>
        <button id="bulk-delete-btn" style="display: none;">
            一括削除 対象：<span id="done-count">0</span>件
        </button>

    </form>

    <table>
        <thead>
            <tr>
                <th>完了<input type="checkbox" id="headerCheckbox"></th>
                <th>内容</th>
                <th>登録日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="todoBody"></tbody>
    </table>

    <script src="js/TodoList.js"></script>
</body>

</html>