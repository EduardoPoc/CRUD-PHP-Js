import axios from "axios";

export async function createUser(apiUrl, { name, age, email }) {
  try {
    const response = await axios.post(apiUrl, {
      name,
      age: Number(age),
      email,
    });
    return response.data;
  } catch (error) {
    const message = error.response?.data?.error || "Failed to create user";
    throw new Error(message);
  }
}
