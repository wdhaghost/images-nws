<script setup>
import { RouterLink, RouterView } from 'vue-router';
import { useUserStore } from "./stores/users";
import { ref } from 'vue';

// Gérer l'état du menu pour les petits écrans
const isMenuOpen = ref(false);

const userStore = useUserStore();
if (localStorage.getItem('token')) {
  userStore.login(JSON.parse(localStorage.getItem('user')), localStorage.getItem('token'));
}

// Fonction pour basculer le menu sur mobile
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
  <header class="bg-gray-800 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-4">
      <!-- Logo et bouton de menu pour mobile -->
      <div class="flex justify-between items-center w-full lg:w-auto">
        <h1 class="text-xl font-bold">Meme App</h1>
        <button @click="toggleMenu" class="lg:hidden text-white focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <!-- Navigation pour les grands écrans -->
      <nav class="hidden lg:flex space-x-4">
        <RouterLink to="/" class="text-white hover:text-gray-300 font-semibold">Accueil</RouterLink>
        <RouterLink to="/images" class="text-white hover:text-gray-300 font-semibold" v-if="userStore.isLoggedIn">Mes images</RouterLink>
        <RouterLink to="/upload" class="text-white hover:text-gray-300 font-semibold" v-if="userStore.isLoggedIn">Upload</RouterLink>
        <RouterLink to="/login" class="text-white hover:text-gray-300 font-semibold" v-if="!userStore.isLoggedIn">Connexion</RouterLink>
        <RouterLink to="/register" class="text-white hover:text-gray-300 font-semibold" v-if="!userStore.isLoggedIn">Inscription</RouterLink>
      </nav>

      <!-- Informations utilisateur et déconnexion -->
      <div v-if="userStore.isLoggedIn" class="hidden lg:flex items-center space-x-4">
        <RouterLink to="/user">{{ userStore.user.name }}</RouterLink>
        <button @click="userStore.logout" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">
          Logout
        </button>
      </div>
    </div>
    <!-- Menu pour les petits écrans -->
    <div v-if="isMenuOpen" class="lg:hidden bg-gray-700">
      <nav class="space-y-2 py-4">
        <RouterLink to="/" @click="isMenuOpen = false" class="block text-white hover:text-gray-300 font-semibold px-4">Accueil</RouterLink>
        <RouterLink to="/images" @click="isMenuOpen = false" class="block text-white hover:text-gray-300 font-semibold px-4" v-if="userStore.isLoggedIn">Images</RouterLink>
        <RouterLink to="/upload" @click="isMenuOpen = false" class="block text-white hover:text-gray-300 font-semibold px-4" v-if="userStore.isLoggedIn">Upload</RouterLink>
        <RouterLink to="/login" @click="isMenuOpen = false" class="block text-white hover:text-gray-300 font-semibold px-4" v-if="!userStore.isLoggedIn">Connexion</RouterLink>
        <RouterLink to="/register" @click="isMenuOpen = false" class="block text-white hover:text-gray-300 font-semibold px-4" v-if="!userStore.isLoggedIn">Inscription</RouterLink>
        
        <!-- Informations utilisateur et déconnexion sur mobile -->
        <div v-if="userStore.isLoggedIn" class="block text-white font-medium px-4">
          <RouterLink to="/user" @click="isMenuOpen = false">{{ userStore.user.name }}</RouterLink>
          {{ userStore.user.name }}
        </div>
        <button v-if="userStore.isLoggedIn" @click="userStore.logout" class="block bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg mx-4">
          Logout
        </button>
      </nav>
    </div>
  </header>

  <main class="container mx-auto py-8">
    <RouterView />
  </main>
</template>

<style scoped>
/* Aucun style personnalisé supplémentaire, tout est géré avec Tailwind CSS */
</style>
