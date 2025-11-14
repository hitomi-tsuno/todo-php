// js/TodoList.js

// 編集中のTODOのIDを保持
let isEditingId = 0;
let filterIsDone = null; // null: 全件, 0: 完了済み, 1: 未完了
let filterText = ""; // テキストフィルター用

// TODOリストの取得と表示
async function fetchTodos() {
  try {
    const res = await fetch("api/api.php", {
      method: "POST",
      body: new URLSearchParams({ action: "list", filterIsDone, filterText }),
    });
    const todos = await res.json();
    updateHeaderCheckbox(todos); // ヘッダーチェックボックスの状態更新
    updateDoneCount(todos); // 一括削除ボタンの完了済み件数の更新
    renderTodos(todos); // TODOリストの描画
  } catch (err) {
    console.error("取得エラー:", err);
    alert("Todoの取得に失敗しました");
  }
}

// TODOリストの描画
function renderTodos(todos) {
  const tbody = document.getElementById("todoBody");
  tbody.innerHTML = "";

  todos.forEach((todo) => {
    const row = document.createElement("tr");
    row.className = "todo" + (todo.isdone ? " done" : "");
    row.dataset.id = todo.id;

    // ✅ 完了チェックボックス
    const checkboxTd = document.createElement("td");
    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.checked = !!todo.isdone;
    checkbox.addEventListener("change", () => toggleTodo(todo.id));
    checkboxTd.appendChild(checkbox);
    row.appendChild(checkboxTd);

    // 📝 TODOテキスト
    const textTd = document.createElement("td");
    if (isEditingId !== todo.id) {
      textTd.className = todo.isdone ? "done" : "";
      textTd.textContent = todo.text;
      textTd.addEventListener("click", () => {
        isEditingId = todo.id;
        fetchTodos(); // TODOリストの取得と表示
      });
    } else {
      const textbox = document.createElement("input");
      textbox.type = "search";
      textbox.value = todo.text;
      const originalText = todo.text; // 元の値を保持
      // ×ボタンクリック時、元の値に戻す
      textbox.addEventListener("input", () => {
        if (textbox.value === "") {
          textbox.value = originalText; // 空になったら元に戻す
        }
      });
      textbox.addEventListener("change", () =>
        updateTodo(todo.id, textbox.value)
      );
      textTd.appendChild(textbox);
      // 描画完了後にフォーカスを当てる
      setTimeout(() => {
        textbox.focus(); // ここでフォーカスを当てる
        textbox.select(); // 全選択してすぐ編集できる
      }, 0);
    }
    row.appendChild(textTd);

    // 📅 登録日時
    const dateTd = document.createElement("td");
    dateTd.textContent = new Date(todo.id).toLocaleString();
    row.appendChild(dateTd);

    // ❌ 削除ボタン
    const deleteTd = document.createElement("td");
    const deleteBtn = document.createElement("button");
    deleteBtn.textContent = "削除";
    deleteBtn.addEventListener("click", () => deleteTodo(todo.id));
    deleteTd.appendChild(deleteBtn);
    row.appendChild(deleteTd);

    tbody.appendChild(row);
  });
}

async function addTodo(text) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "add", text }),
  });
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

async function deleteTodo(id) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "delete", id }),
  });
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

async function toggleTodo(id) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "toggle", id }),
  });
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

async function updateTodo(id, text) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "update", id, text }),
  });
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

document.getElementById("addForm").addEventListener("submit", (e) => {
  e.preventDefault();
  const text = e.target.text.value;
  addTodo(text);
  e.target.reset();
});

// ヘッダーチェックボックスで全件完了/未完了切替
document
  .getElementById("headerCheckbox")
  .addEventListener("change", async (e) => {
    const checked = e.target.checked;
    await fetch("api/api.php", {
      method: "POST",
      body: new URLSearchParams({
        action: "toggle_all",
        isdone: checked ? 1 : 0,
      }),
    });
    fetchTodos(); // TODOリストの取得と表示
  });
// ヘッダーチェックボックスの状態更新
function updateHeaderCheckbox(todos) {
  const headerCheckbox = document.getElementById("headerCheckbox");
  headerCheckbox.checked = areAllTodosDone(todos);
}
// 全件完了しているか判定
function areAllTodosDone(todos) {
  return todos.every((todo) => todo.isdone === 1);
}

// 一括削除ボタンの完了済み件数の更新
function updateDoneCount(todos) {
  const count = todos.filter((todo) => todo.isdone === 1).length;
  document.getElementById("done-count").textContent = count;
  document.getElementById("bulk-delete-btn").style.display =
    count > 0 ? "inline-block" : "none";
}

// 一括削除ボタンクリック時の処理
document
  .getElementById("bulk-delete-btn")
  .addEventListener("click", async () => {
    const confirmed = confirm("完了済みのタスクをすべて削除しますか？");
    if (!confirmed) return;

    // 完了済みのIDを収集
    const doneIds = Array.from(document.querySelectorAll(".todo.isdone")).map(
      (el) => el.dataset.id
    );

    // サーバーに送信
    await fetch("api/api.php", {
      method: "POST",
      body: new URLSearchParams({ action: "delete_done" }),
    });
    isEditingId = 0; // 編集状態をリセット
    fetchTodos(); // TODOリストの取得と表示
  });

// フィルターセレクト変更時の処理
document.getElementById("filterSelect").addEventListener("change", (e) => {
  FilterTodos_isdone(e.target.value);
});
// フィルター適用
function FilterTodos_isdone(isdone) {
  filterIsDone = isdone;
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

// テキストフィルター入力時の処理
document.getElementById("textFilter").addEventListener("input", (e) => {
  filterText = e.target.value;
  isEditingId = 0;
  fetchTodos();
});

fetchTodos(); // TODOリストの取得と表示
