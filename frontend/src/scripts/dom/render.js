import { getUsers } from "../api/read.js";

let usersCache = [];

export function findUserById(id) {
  return usersCache.find((user) => user.id === id);
}

// Nunca interpole dado do usuário direto no innerHTML: um nome como
// <img src=x onerror=alert(1)> viraria código executável na página.
function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

export async function renderUsers(apiUrl) {
  const users = await getUsers(apiUrl);
  usersCache = users;
  const usersSection = document.getElementById("users");

  if (users.length === 0) {
    usersSection.innerHTML = '<p class="text-muted">No users found.</p>';
    return;
  }

  usersSection.innerHTML = "";

  users.forEach((user) => {
    const userDiv = document.createElement("div");
    userDiv.classList.add("col-md-3");

    userDiv.innerHTML = `
            <div class="card user-card h-100" id="${escapeHtml(user.id)}">
                <div class="card-body">
                    <h5 class="card-title">${escapeHtml(user.name)}</h5>
                    <p class="card-text mb-1"><strong>Age:</strong> ${escapeHtml(user.age)}</p>
                    <p class="card-text"><strong>Email:</strong> ${escapeHtml(user.email)}</p>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-sm btn-outline-dark flex-fill" data-action="edit">Edit</button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" data-action="delete">Delete</button>
                </div>
            </div>
        `;

    usersSection.appendChild(userDiv);
  });
}
