<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/users';

const userStore = useUserStore();
const url = import.meta.env.VITE_API_URL + '/api/myimages';
const images = ref([]); // Stocker les images
const errorMessage = ref(null); // Stocker le message d'erreur
const token = userStore.token;


const fetchImages = async () => {
  try {
    const response = await axios.get(url, {
      withCredentials: true,
      headers: {
        Authorization: 'Bearer ' + token,
      },
    });

    images.value = response.data; // Stocker les images reçues
  } catch (error) {

    errorMessage.value = 'Echec lors de la récupération des images. Essayez plus tard.';
  }
};

// Fonction pour supprimer une image
const deleteImage = async (id) => {
  try {
    await axios.delete(import.meta.env.VITE_API_URL + `/api/images/${id}`, {
      headers: {
        Authorization: 'Bearer ' + token,
      },
    });
    images.value = images.value.filter((image) => image.id !== id); // Mettre à jour localement
  } catch (error) {
    console.error('Error deleting image:', error);
    errorMessage.value = 'Echec lors de la suppression.';
  }
};
const togglePrivacy = async (id, currentPrivacy) => {
  const auth = 'Bearer ' + token;
  try {
    // Inverser la valeur de la confidentialité
    const newPrivacy = !currentPrivacy;
    const image = images.value.find(img => img.id === id);

    // Envoyer la requête PUT avec les données mises à jour
    const response = await axios.put(
      import.meta.env.VITE_API_URL + `/api/images/${id}`,
      { is_public: newPrivacy }, // Envoyer la nouvelle valeur de confidentialité
      {
        headers: {
          Authorization: auth.replace('"', ''), // Retirer les guillemets superflus
          'Content-Type': 'application/json', // Indiquer que les données sont au format JSON
        },
      }
    );

    image.is_public = response.data.is_public; // Mettre à jour localement
    
  } catch (error) {
    console.error('Erreur impossible de la rendre privé:', error.response ? error.response.data : error.message);
  }
};

// Générer l'URL de l'image à partir de son chemin stocké
const getImageUrl = (path) => {
  return import.meta.env.VITE_API_URL + `/storage/${path}`; // Assurez-vous que votre API retourne les bons chemins
};

// Appeler la fonction `fetchImages` au montage du composant pour récupérer les images
fetchImages();
</script>

<template>
  <div class="image-list container mx-auto py-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Mes images</h2>

    <!-- Affichage de la liste des images -->
    <div v-if="images.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="image in images" :key="image.id" class="image-item bg-white shadow-md rounded-lg overflow-hidden">
        <img :src="getImageUrl(image.url)" :alt="`Image ${image.id}`" class="w-full h-48 object-cover" />
        <div class="p-4 flex justify-between items-center">
          <span class="text-sm font-bold text-gray-700">{{ image.name }}</span>
          <div>

            <button @click="deleteImage(image.id)" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
              Delete
            </button>
            <button @click="togglePrivacy(image.id, image.is_public)"
              :class="image.is_public ? 'bg-green-500' : 'bg-gray-500'"
              class="text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                <span v-if="image.is_public">Rendre Privée</span>
                <span v-else>Rendre Publique</span>
              </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Message si aucune image n'est trouvée -->
    <div v-else>
      <p class="text-gray-500">Pas d'image.</p>
    </div>

    <!-- Affichage d'un message d'erreur -->
    <div v-if="errorMessage" class="mt-4 text-red-600">{{ errorMessage }}</div>
  </div>
</template>

<style scoped>
/* Aucun style supplémentaire nécessaire grâce à Tailwind */
</style>
