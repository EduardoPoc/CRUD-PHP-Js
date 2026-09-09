import axios from "axios";

// PUT — substituição completa
export async function updateUser(apiUrl, id, { name, age, email }) {
  try {
    const response = await axios.put(`${apiUrl}?id=${id}`, {
      name,
      age: Number(age),
      email,
    });
    return response.data;
  } catch (error) {
    const message = error.response?.data?.error || "Failed to update user";
    throw new Error(message);
  }
}

// PATCH — atualização parcial
export async function patchUser(apiUrl, id, fields) {
  if (fields.age !== undefined) {
    fields.age = Number(fields.age);
  }

  try {
    const response = await axios.patch(`${apiUrl}?id=${id}`, fields);
    return response.data;
  } catch (error) {
    const message = error.response?.data?.error || "Failed to patch user";
    throw new Error(message);
  }
}
