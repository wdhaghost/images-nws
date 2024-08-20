<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import axios from 'axios';
import { useUserStore } from '../stores/users';

const userStore = useUserStore();
const imageFile = ref(null);
const name = ref(null);
const message = ref(null);
const errorMessage = ref(null);
const token = userStore.token;
const uploadedImageUrl = ref(null); // Stocke l'URL de l'image après upload

// Référence à l'élément input pour le fichier
const fileInput = ref(null);

// Router instance
const router = useRouter();

const handleDrop = (event) => {
  event.preventDefault();
  if (event.dataTransfer.files && event.dataTransfer.files[0]) {
    imageFile.value = event.dataTransfer.files[0];
    message.value = `Image selected: ${imageFile.value.name}`;
    errorMessage.value = null;
  }
};

const handleDragOver = (event) => {
  event.preventDefault();
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    imageFile.value = file;
    message.value = `Image selected: ${file.name}`;
    errorMessage.value = null;
    console.log("File selected:", file);
  }
};

// Fonction pour ouvrir le sélecteur de fichier lorsque la zone de drag est cliquée
const handleClick = () => {
  fileInput.value.click(); // Simuler un clic sur l'input de fichier caché
};

const getCsrfToken = async () => {
  try {
    await axios.get(import.meta.env.VITE_API_URL + '/sanctum/csrf-cookie', {
      withCredentials: true,
    });
  } catch (error) {
    errorMessage.value = "Failed to get CSRF token. Please try again.";
  }
};

const uploadImage = async () => {
  if (!name.value) {
    errorMessage.value = "Entrez un nom.";
    return;
  }
  if (!imageFile.value) {
    errorMessage.value = "Please select an image first.";
    return;
  }

  await getCsrfToken();

  const formData = new FormData();
  formData.append('image', imageFile.value);
  formData.append('name', name.value);
  const url = import.meta.env.VITE_API_URL + '/api/upload';

  try {
    const response = await axios.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: 'Bearer ' + token,
      },
    });
    message.value = `Image uploaded successfully.`;
    uploadedImageUrl.value = response.data.url; // Stocker l'URL de l'image téléchargée
    errorMessage.value = null;
    imageFile.value = null;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = "Unauthenticated. Please login again.";
    } else {
      errorMessage.value = "Echec lors de l'upload.";
    }
  }
};
</script>

<template>
  <div class="upload-page bg-gray-50 py-8">
    <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Upload Image</h1>

    <!-- Zone de Drag & Drop -->
    <!-- Bouton d'upload -->
     <div class="w-full mx-auto">
      <input type="text" v-model="name" placeholder="Nom de l'image" class="mt-1 block mx-auto w-50 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
     </div>
    <div class="drop-area bg-white border-2 border-dashed border-gray-300 p-6 rounded-lg mb-6 mx-auto max-w-lg text-center cursor-pointer"
         @drop="handleDrop"
         @dragover="handleDragOver"
         @click="handleClick">
      <p class="text-gray-500">Déposez une image ici, ou cliquer pour en choisr une</p>
      <input type="file" id="image" accept="image/*" ref="fileInput" @change="handleFileChange" class="hidden" />
    </div>

    <div class="text-center">
      <button @click.prevent="uploadImage" class="bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Upload
      </button>
    </div>

    <!-- Affichage des messages -->
    <div v-if="message" class="mt-4 text-green-600">{{ message }}</div>
    <div v-if="errorMessage" class="mt-4 text-red-600">{{ errorMessage }}</div>

    <!-- Affichage du lien vers l'image après upload -->
    <div v-if="uploadedImageUrl" class="mt-6 text-center">
      <RouterLink :to="`/image/${uploadedImageUrl}`" class="text-blue-500 underline">
        Voir l'image téléchargée
      </RouterLink>
    </div>
  </div>
</template>

<style scoped>
/* Pas besoin de styles supplémentaires, tout est stylé avec Tailwind */
</style>
