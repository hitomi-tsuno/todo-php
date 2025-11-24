// js/TodoList.js

// 編集中のTODOのIDを保持
let isEditingId = 0;
let filterIsDone = null; // null: 全件, 0: 完了済み, 1: 未完了
let filterText = ""; // テキストフィルター用
let sortKey = "id"; // ソートキー（id: 登録日時）
let sortOrder = "asc"; // ソート順（asc: 昇順, desc: 降順）

// TODOリストの取得と表示
async function fetchTodos() {
  try {
    const selectedTags = getSelectedTags();
    const params = new URLSearchParams({
      action: "list",
      filterIsDone,
      filterText,
      sortKey,
      sortOrder,
    });
    selectedTags.forEach((tag) => params.append("filterTags[]", tag));
    const res = await fetch("api/api.php", {
      method: "POST",
      body: params,
    });

    const todos = await res.json();
    if (todos.status === "error") {
      console.error("APIエラー:", todos.message); // ← Consoleに出る
      // alert("取得エラー: " + todos.message);      // UIにも出すなら
      // return;
    }

    updateHeaderCheckbox(todos); // ヘッダーチェックボックスの状態更新
    updateDoneCount(todos); // 一括削除ボタンの完了済み件数の更新
    updateSortIcons(); // ソートアイコンの更新
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
      textTd.classList.add("StyledText");
      textTd.className.add = todo.isdone ? "done" : "";
      textTd.textContent = todo.text;
      textTd.addEventListener("click", () => {
        isEditingId = todo.id;
        fetchTodos(); // TODOリストの取得と表示
      });
    } else {
      const textbox = document.createElement("input");
      const tagsbox = document.createElement("input");
      // ***** 📝 TODO *****
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
        updateTodo(todo.id, textbox.value, tagsbox.value)
      );
      textTd.appendChild(textbox);

      // ***** 🏷️ タグ *****
      tagsbox.type = "search";
      tagsbox.value = todo.tags;
      const originaltags = todo.tags; // 元の値を保持
      // // ×ボタンクリック時、元の値に戻す
      // tagsbox.addEventListener("input", () => {
      //   if (tagsbox.value === "") {
      //     tagsbox.value = originaltags; // 空になったら元に戻す
      //   }
      // });
      tagsbox.addEventListener("change", () =>
        updateTodo(todo.id, textbox.value, tagsbox.value)
      );
      textTd.appendChild(tagsbox);

      // 描画完了後にフォーカスを当てる
      setTimeout(() => {
        textbox.focus(); // ここでフォーカスを当てる
        textbox.select(); // 全選択してすぐ編集できる
      }, 0);
    }
    row.appendChild(textTd);

    // 🏷️ タグ
    const tagTd = document.createElement("td");
    tagTd.textContent = todo.tags;
    tagTd.addEventListener("click", () => {
      isEditingId = 0;
      fetchTodos(); // TODOリストの取得と表示
    });
    row.appendChild(tagTd);
    // タグの色分け表示
    const tagList = (todo.tags || "").split(",").map((tag) => tag.trim());
    tagList.forEach((tag) => {
      const tagSpan = document.createElement("span");
      tagSpan.textContent = tag;
      tagSpan.className = "tag";
      tagSpan.dataset.tag = tag; // 色分け用
      tagTd.appendChild(tagSpan);
    });
    row.appendChild(tagTd);

    // 📅 登録日時
    const dateTd = document.createElement("td");
    dateTd.textContent = new Date(todo.id).toLocaleString();
    dateTd.addEventListener("click", () => {
      isEditingId = 0;
      fetchTodos(); // TODOリストの取得と表示
    });
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

async function addTodo(text, tags) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "add", text, tags }),
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

async function updateTodo(id, text, tags) {
  await fetch("api/api.php", {
    method: "POST",
    body: new URLSearchParams({ action: "update", id, text, tags }),
  });
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}

document.getElementById("addForm").addEventListener("submit", (e) => {
  e.preventDefault();
  const text = e.target.text.value;
  const tags = e.target.tags.value;
  addTodo(text, tags);
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

// フィルター タグ変更時の処理
document.getElementById("tagsCheckboxList").addEventListener("change", (e) => {
  FilterTodos_tags(e.target.value);
});
// フィルター適用
function FilterTodos_tags(tags) {
  // filterTag = tags;
  isEditingId = 0; // 編集状態をリセット
  fetchTodos(); // TODOリストの取得と表示
}
function getSelectedTags() {
  return Array.from(
    document.querySelectorAll("#tagsCheckboxList input:checked")
  ).map((cb) => cb.value);
}

// ソートヘッダークリック時の処理
document
  .getElementById("sort-isdone")
  .addEventListener("click", () => toggleSort("isdone"));
document
  .getElementById("sort-text")
  .addEventListener("click", () => toggleSort("text"));
document
  .getElementById("sort-tags")
  .addEventListener("click", () => toggleSort("tags"));
document
  .getElementById("sort-id")
  .addEventListener("click", () => toggleSort("id"));
// ソート切替
function toggleSort(key) {
  if (sortKey === key) {
    sortOrder = sortOrder === "asc" ? "desc" : "asc";
  } else {
    sortKey = key;
    sortOrder = "asc";
  }
  updateSortIcons(); // ソートアイコンの更新
  fetchTodos();
}

// ソートアイコンの更新
function updateSortIcons() {
  const headers = document.querySelectorAll("th.sortable");
  headers.forEach((th) => {
    th.classList.remove("asc", "desc");
  });

  const activeTh = document.getElementById(`sort-${sortKey}`);
  if (activeTh) {
    activeTh.classList.add(sortOrder);
  }
}

fetchTodos(); // TODOリストの取得と表示
