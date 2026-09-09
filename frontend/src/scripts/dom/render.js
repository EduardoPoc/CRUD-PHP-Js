import { getUsers } from "../api/read.js";

let usersCache = [];

export function findUserById(id) {
  return usersCache.find((user) => user.id === id);
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
            <div class="card user-card h-100" id="${user.id}">
                <div class="card-body">
                    <h5 class="card-title">${user.name}</h5>
                    <p class="card-text mb-1"><strong>Age:</strong> ${user.age}</p>
                    <p class="card-text"><strong>Email:</strong> ${user.email}</p>
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
