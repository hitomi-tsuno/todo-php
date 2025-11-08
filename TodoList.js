// TodoList.js
async function fetchTodos() {
  try {
    const res = await fetch('api.php', {
      method: 'POST',
      body: new URLSearchParams({ action: 'list' })
    });
    const todos = await res.json();
    renderTodos(todos);
  } catch (err) {
    console.error('取得エラー:', err);
    alert('Todoの取得に失敗しました');
  }
}

function renderTodos(todos) {
  const tbody = document.getElementById('todoBody');
  tbody.innerHTML = '';

  todos.forEach(todo => {
    const row = document.createElement('tr');

    // ✅ 完了チェックボックス
    const checkboxTd = document.createElement('td');
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = !!todo.isdone;
    checkbox.addEventListener('change', () => toggleTodo(todo.id));
    checkboxTd.appendChild(checkbox);
    row.appendChild(checkboxTd);

    // 📝 TODOテキスト
    const textTd = document.createElement('td');
    textTd.className = todo.isdone ? 'done' : '';
    textTd.textContent = todo.text;
    row.appendChild(textTd);

    // 📅 登録日時
    const dateTd = document.createElement('td');
    dateTd.textContent = new Date(todo.id).toLocaleString();
    row.appendChild(dateTd);

    // ❌ 削除ボタン
    const deleteTd = document.createElement('td');
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = '削除';
    deleteBtn.addEventListener('click', () => deleteTodo(todo.id));
    deleteTd.appendChild(deleteBtn);
    row.appendChild(deleteTd);

    tbody.appendChild(row);
  });
}

async function addTodo(text) {
  await fetch('api.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'add', text })
  });
  fetchTodos();
}

async function deleteTodo(id) {
  await fetch('api.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'delete', id })
  });
  fetchTodos();
}

async function toggleTodo(id) {
  await fetch('api.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'toggle', id })
  });
  fetchTodos();
}

document.getElementById('addForm').addEventListener('submit', e => {
  e.preventDefault();
  const text = e.target.text.value;
  addTodo(text);
  e.target.reset();
});

fetchTodos();
