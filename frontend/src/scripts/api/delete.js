import axios from "axios";

export async function deleteUser(apiUrl, id) {
  try {
    const response = await axios.delete(`${apiUrl}?id=${id}`);
    return response.data;
  } catch (error) {
    const message = error.response?.data?.error || "Failed to delete user";
    throw new Error(message);
  }
}
