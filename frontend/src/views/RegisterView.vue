<template>
  <div class="register-form bg-white shadow-lg rounded-lg p-8 max-w-md mx-auto mt-10">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Register</h2>
    <form @submit.prevent="registerUser">
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          type="text"
          id="name"
          v-model="name"
          required
          class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          v-model="email"
          required
          class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input
          type="password"
          id="password"
          v-model="password"
          required
          class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <button
        type="submit"
        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      >
        Register
      </button>
    </form>
    <div v-if="errorMessage" class="mt-4 text-red-600 text-sm text-center">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/users';
import { useRouter } from 'vue-router';

const router = useRouter();
const userStore = useUserStore();
const email = ref('');
const name = ref('');
const password = ref('');
const errorMessage = ref(null);
const url = import.meta.env.VITE_API_URL + '/api/register';

const registerUser = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    const response = await axios.post(url, {
      name: name.value,
      email: email.value,
      password: password.value,
    });

    userStore.login(response.data.user, response.data.token);
    if (userStore.isLoggedIn) {
      router.push('/');
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
  }
};
</script>

<style scoped>
/* Tailwind CSS styles are used instead of custom CSS */
</style>
