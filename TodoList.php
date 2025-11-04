<?php
$todos = ["牛乳を買う", "メール返信", "Reactの復習"];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Todoリスト</title>
</head>

<body>
    <h1>Todoリスト</h1>
    <ul>
        <?php foreach ($todos as $todo): ?>
            <li><?php echo htmlspecialchars($todo); ?></li>
        <?php endforeach; ?>
    </ul>
</body>

</html>