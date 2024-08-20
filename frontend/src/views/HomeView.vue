<template>
  <div class="image-list container mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Les Memes</h2>

    <!-- Affichage de la liste des images -->
    <div v-if="images.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="image in images" :key="image.id" class=" bg-white shadow-md rounded-lg overflow-hidden">
        <RouterLink :to="'/image/'+image.url" class="text-blue-500 underline">
          <img :src="getImageUrl(image.path)" :alt="`Image ${image.id}`" class="w-full h-48 object-cover" />
        </Routerlink>
        <div>
          <span>upload le {{formatDate(image.created_at)}} </span>
          <span> par {{image.user.name }} </span>
        </div>
      </div>
    </div>

    <!-- Message si aucune image n'est trouvée -->
    <div v-else class="text-center text-gray-500">
      <p>Pas d'images</p>
    </div>

    <!-- Affichage d'un message d'erreur -->
    <div v-if="errorMessage" class="mt-4 text-red-600 text-center">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/users';

const userStore = useUserStore();
const url = import.meta.env.VITE_API_URL + '/api/images';
const images = ref([]); // Stocker les images
const errorMessage = ref(null); // Stocker le message d'erreur

const fetchImages = async () => {
  try {
    // Obtenir le cookie CSRF pour Sanctum avec cookies
    await axios.get(import.meta.env.VITE_API_URL + '/sanctum/csrf-cookie', {
      withCredentials: true, // Nécessaire pour Sanctum avec cookies
    });

    // Faire la requête pour obtenir les images
    const response = await axios.get(url);
    images.value = response.data;
  } catch (error) {
    // Gérer les erreurs et fournir plus de détails
    if (error.response) {
      console.error('Error fetching images:', error.response.status, error.response.data);
    } else if (error.request) {
      console.error('No response received:', error.request);
    } else {
      console.error('Error setting up request:', error.message);
    }
    errorMessage.value = 'Failed to fetch images. Please try again later.';
  }
};


const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
  });
};
const getImageUrl = (path) => {
  return import.meta.env.VITE_API_URL + `/storage/${path}`; // Assurez-vous que votre API retourne les bons chemins
};

// Appeler la fonction `fetchImages` au montage du composant pour récupérer les images
fetchImages();
</script>

<style scoped>

</style>
