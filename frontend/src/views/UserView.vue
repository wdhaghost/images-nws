<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/users';
import { useRouter } from 'vue-router';

// Récupération de l'état utilisateur depuis le store
const userStore = useUserStore();
const router = useRouter();
const userInfo = ref(null);
const errorMessage = ref(null);
const successMessage = ref(null);

// Fonction pour récupérer les informations de l'utilisateur

    userInfo.value = userStore.user;
// Fonction pour supprimer le compte de l'utilisateur
const deleteAccount = async () => {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    try {
      await axios.delete(import.meta.env.VITE_API_URL + '/api/user/delete', {
        headers: {
          Authorization: `Bearer ${userStore.token}`,
        },
      });

      // Supprimer les informations de l'utilisateur et du token
      userStore.user = null;
      userStore.token = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');

      successMessage.value = 'Your account has been deleted successfully.';
      
      // Rediriger vers la page de login après suppression du compte
      router.push('/login');
      
    } catch (error) {
      errorMessage.value = 'Failed to delete account. Please try again later.';
      console.error('Error deleting account:', error.response ? error.response.data : error.message);
    }
  }
};

// Appeler `fetchUserInfo` lorsque le composant est monté

</script>

<template>
  <div class="max-w-3xl mx-auto py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">User Profile</h2>

    <!-- Affichage des informations de l'utilisateur -->
    <div v-if="userInfo" class="bg-white p-6 rounded-lg shadow-lg mb-6">
      <p class="text-xl font-semibold mb-2">Name: {{ userInfo.name }}</p>
      <p class="text-lg text-gray-600 mb-2">Email: {{ userInfo.email }}</p>
      <p class="text-lg text-gray-600 mb-2">Joined: {{ new Date(userInfo.created_at).toLocaleDateString() }}</p>
    </div>

    <!-- Message d'erreur ou de succès -->
    <div v-if="errorMessage" class="text-red-600 mb-4">{{ errorMessage }}</div>
    <div v-if="successMessage" class="text-green-600 mb-4">{{ successMessage }}</div>

    <!-- Bouton de suppression du compte -->
    <button @click="deleteAccount"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
      Delete Account
    </button>
  </div>
</template>

<style scoped>
/* Utilisation de Tailwind CSS pour styliser l'interface */
</style>
