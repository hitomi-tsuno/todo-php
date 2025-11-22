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
  const select = document.getElementById("tagsSelect");
  select.innerHTML = "";

  const tagOption = document.createElement("option");
  tagOption.value = "null";
  tagOption.textContent = "全件";
  select.appendChild(tagOption);
  tags.forEach((tag) => {
    const tagOption = document.createElement("option");
    tagOption.value = tag.tags;
    tagOption.textContent = tag.tags;
    select.appendChild(tagOption);
  });
}
fetchTags(); // Tagsの取得と表示
