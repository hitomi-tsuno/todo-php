<!-- TodoList.php -->
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Todoリスト</title>
</head>

<body>
    <h1>Todoリスト</h1>
    <form id="addForm">
        <input type="search" name="text" placeholder="新しいTODOを入力" required>
        <button type="submit">追加</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>完了</th>
                <th>内容</th>
                <th>登録日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="todoBody"></tbody>
    </table>
    <script src="TodoList.js"></script>
</body>

</html>