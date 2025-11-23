// js\TagsList.js

// TODOリストの取得と表示
async function fetchTags() {
  try {
    const res = await fetch("api/api.php", {
      method: "POST",
      body: new URLSearchParams({
        action: "list_tags",
      }),
    });
    const tags = await res.json();
    renderTags(tags); // Tagsの描画
  } catch (err) {
    console.error("取得エラー:", err);
    alert("Tagsの取得に失敗しました");
  }
}

// 🏷️ タグの描画
function renderTags(tags) {
  const container = document.getElementById("tagsCheckboxList");
  container.innerHTML = "";

  tags.forEach(tag => {
    const label = document.createElement("label");
    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.value = tag.tags;
    label.appendChild(checkbox);
    label.appendChild(document.createTextNode(" " + tag.tags));
    container.appendChild(label);
  });
}
fetchTags(); // Tagsの取得と表示
