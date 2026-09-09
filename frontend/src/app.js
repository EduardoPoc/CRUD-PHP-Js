import { renderUsers, findUserById } from "./scripts/dom/render.js";
import { createUser } from "./scripts/api/create.js";
import { deleteUser } from "./scripts/api/delete.js";
import { updateUser, patchUser } from "./scripts/api/update.js";

const apiUrl = "http://localhost:8000/api/users";

// Referências do DOM
const form = document.getElementById("create-user-form");
const formError = document.getElementById("form-error");
const formTitle = document.getElementById("form-title");
const submitBtn = form.querySelector('button[type="submit"]');
const cancelBtn = document.getElementById("cancel-edit");
const usersSection = document.getElementById("users");

// Estado de edição
let editingId = null;
let originalUser = null;

// --- Helpers de erro ---
function showError(message) {
  formError.textContent = message;
  formError.classList.remove("d-none");
}

function hideError() {
  formError.classList.add("d-none");
  formError.textContent = "";
}

// --- Helpers de edição ---
function getUserFromCard(button) {
  const card = button.closest(".user-card");
  return findUserById(Number(card.id));
}

function enterEditMode(user) {
  editingId = user.id;
  originalUser = { ...user };
  document.getElementById("name").value = user.name;
  document.getElementById("age").value = user.age;
  document.getElementById("email").value = user.email;
  formTitle.textContent = "Edit User";
  submitBtn.textContent = "Update";
  cancelBtn.style.display = "";
  document.getElementById("name").focus();
}

function exitEditMode() {
  editingId = null;
  originalUser = null;
  formTitle.textContent = "Create User";
  submitBtn.textContent = "Create";
  cancelBtn.style.display = "none";
  form.reset();
}

cancelBtn.addEventListener("click", exitEditMode);

// --- Delegação de eventos nos cards ---
usersSection.addEventListener("click", async (event) => {
  const { target } = event;

  if (target.dataset.action === "edit") {
    enterEditMode(getUserFromCard(target));
  }

  if (target.dataset.action === "delete") {
    const user = getUserFromCard(target);
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
      await deleteUser(apiUrl, user.id);
      if (editingId === user.id) exitEditMode();
      renderUsers(apiUrl);
    } catch (error) {
      showError(error.message);
    }
  }
});

// --- Submit: cria ou edita ---
form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const name = document.getElementById("name").value;
  const age = document.getElementById("age").value;
  const email = document.getElementById("email").value;

  hideError();

  try {
    if (editingId !== null) {
      // MODO EDIÇÃO — descobre o que mudou
      const changed = {};
      if (name !== originalUser.name) changed.name = name;
      if (Number(age) !== originalUser.age) changed.age = age;
      if (email !== originalUser.email) changed.email = email;

      if (Object.keys(changed).length === 0) {
        exitEditMode();
        return;
      }

      const allChanged = Object.keys(changed).length === 3;
      if (allChanged) {
        await updateUser(apiUrl, editingId, { name, age, email });
      } else {
        await patchUser(apiUrl, editingId, changed);
      }
    } else {
      // MODO CRIAÇÃO
      await createUser(apiUrl, { name, age, email });
    }

    exitEditMode();
    renderUsers(apiUrl);
  } catch (error) {
    showError(error.message);
  }
});

// --- Primeira renderização ---
document.addEventListener("DOMContentLoaded", () => renderUsers(apiUrl));
