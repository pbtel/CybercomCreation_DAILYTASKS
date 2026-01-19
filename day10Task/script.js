// DOM Selection
const itemInput = document.getElementById("itemInput");
const addBtn = document.getElementById("addBtn");
const itemList = document.getElementById("itemList");

// Add Item
addBtn.addEventListener("click", () => {
    const text = itemInput.value.trim();

    if (text === "") {
        alert("Please enter an item");
        return;
    }

    // Create elements
    const li = document.createElement("li");
    const span = document.createElement("span");
    const editBtn = document.createElement("button");
    const deleteBtn = document.createElement("button");

    span.innerText = text;
    editBtn.innerText = "Edit";
    deleteBtn.innerText = "Delete";

    // Edit Item
    editBtn.addEventListener("click", () => {
        const newText = prompt("Edit item:", span.innerText);
        if (newText !== null && newText.trim() !== "") {
            span.innerText = newText;
        }
    });

    // Delete Item
    deleteBtn.addEventListener("click", () => {
        li.remove();
    });

    li.append(span, editBtn, deleteBtn);
    itemList.appendChild(li);

    itemInput.value = "";
});
