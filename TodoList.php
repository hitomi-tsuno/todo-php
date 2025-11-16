<!-- TodoList.php -->
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Todoリスト</title>
</head>

<body>

    <h1>Todoリスト</h1>

    <!-- フィルター -->
    <div class="button-row">
        <div class="filter-area">
            フィルター：
            <!-- フィルター　全件 / 完了 / 未完了 -->
            <select id="filterSelect">
                <option value="null">全件</option>
                <option value="0">完了のみ</option>
                <option value="1">未完了のみ</option>
            </select>
            <label>
                🔍
                <input type="search" id="textFilter" placeholder="キーワードで絞り込み">
            </label>
            <br>

        </div>

        <!-- 入力＋追加＋一括削除 -->
        <div class="input-area">
            <form id="addForm">
                <!-- 📝 追加テキスト -->
                <input type="search" name="text" placeholder="新しいTODOを入力" required>
                <!-- 🏷️ タグ -->
                <input type="search" name="tags" placeholder="タグ（例: 買い物, 家事）">
                <!-- ＋追加ボタン -->
                <button type="submit">追加</button>
            </form>

            <!-- ❌ 一括削除ボタン -->
            <button id="bulk-delete-btn">
                一括削除 対象：<span id="done-count">0</span>件
            </button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th id="sort-isdone" class="sortable">完了<input type="checkbox" id="headerCheckbox"></th>
                <th id="sort-text" class="sortable">内容</th>
                <th id="sort-tags" class="sortable">タグ</th>
                <th id="sort-id" class="sortable">登録日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="todoBody"></tbody>
    </table>
    <script src="js/TodoList.js"></script>

</body>

</html>