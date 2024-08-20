<template>
  <div class="login-form bg-white shadow-lg rounded-lg p-8 max-w-md mx-auto mt-10">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Login</h2>
    <form @submit.prevent="loginUser">
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
        Login
      </button>
    </form>
    <div v-if="errorMessage" class="mt-4 text-red-600 text-sm text-center">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import {useUserStore} from '../stores/users'
import {useRouter} from 'vue-router'

const router = useRouter()
const userStore = useUserStore()
const email = ref('');
const password = ref('');
const errorMessage = ref(null);
const url = import.meta.env.VITE_API_URL + '/api/login'

const loginUser = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    const response = await axios.post(url, {
      email: email.value,
      password: password.value,
    });
    userStore.login(response.data.user, response.data.token)
    if (userStore.isLoggedIn) {
      router.push('/');
    }
  } catch (error) {
    errorMessage.value = 'Invalid credentials, please try again';
  }
};
</script>

<style scoped>
/* Pas de style additionnel nécessaire avec Tailwind */
</style>
