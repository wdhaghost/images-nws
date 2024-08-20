<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { useUserStore } from '../stores/users';

const imageUrl = ref(null);
const image = ref(null);
const errorMessage = ref(null);
const isLoading = ref(true);
const userStore = useUserStore();

// Récupérer l'URL unique depuis les paramètres de la route
const route = useRoute();
const uniqueUrl = route.params.url;
const token = userStore.token;
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
  });
};
const fetchImage = async () => {
  try {
    const response = await axios.get(import.meta.env.VITE_API_URL + '/api/image/' + uniqueUrl, {
      headers: {
        Authorization: 'Bearer ' + token,
      }

    });
   
    image.value = response.data;
    imageUrl.value = import.meta.env.VITE_API_URL +'/storage/'+response.data.path;
    isLoading.value = false;
   
  } catch (error) {
    if (error.response && error.response.status === 403) {
    errorMessage.value = "Image Privée.";
  } else if (error.response && error.response.status === 404) {
    errorMessage.value = "Image non trouvée.";
  } else {
    errorMessage.value = "Failed to load image.";
  }

  isLoading.value = false;
  }
};

// Charger l'image quand le composant est monté
onMounted(() => {
  fetchImage();
});
</script>

<template>
  <div class="image-view-container max-w-3xl mx-auto py-8 text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Image Viewer</h2>

    <!-- Affichage du chargement -->
    <div v-if="isLoading" class="text-gray-500">
      <p>Loading image...</p>
    </div>

    <!-- Affichage de l'image -->
    <div v-if="imageUrl && !isLoading">
      <img :src="imageUrl" alt="Unique Image" class="w-full h-auto rounded-lg shadow-lg object-cover" />
      <div>
          <span>upload le {{formatDate(image.created_at)}} </span>
          <span> par {{image.user.name }} </span>
        </div>
    </div>
    <div>

    </div>

    <!-- Affichage d'une erreur -->
    <div v-if="errorMessage" class="text-red-500">
      <p>{{ errorMessage }}</p>
    </div>
  </div>
</template>

<style scoped>
/* Utilisation de Tailwind CSS pour le style */
</style>
s