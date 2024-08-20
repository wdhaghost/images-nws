import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/users'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import UploadView from '../views/UploadView.vue'
import ImagesView from '../views/ImagesView.vue'
import RegisterView from '../views/RegisterView.vue'
import ImageView from '../views/ImageView.vue'
import UserView from '../views/UserView.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/images',
      name: 'images',
      component: ImagesView,
      beforeEnter(to,from){
        const userStore= useUserStore()

        if (!userStore.isLoggedIn)return { name: 'home' }
      }
    },
    {
      path: '/image/:url', // Route avec un paramètre dynamique `url`
      name: 'image-viewer',
      component: ImageView,
    },
    {
      path: '/login',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: LoginView,
      beforeEnter(to,from){
        const userStore= useUserStore()

        if (userStore.isLoggedIn)return { name: 'home' }
      }
    },
    {
      path: '/register',
      name: 'register',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: RegisterView,
      beforeEnter(to,from){
        const userStore= useUserStore()

        if (userStore.isLoggedIn)return { name: 'home' }
      }
    },
    {
      path: '/upload',
      name: 'upload',
      component: UploadView,
      beforeEnter(to,from){
        const userStore= useUserStore()

        if (!userStore.isLoggedIn)return { name: 'login' }
      }
    },
    {
      path: '/user',
      name: 'user',
      component: UserView,
      beforeEnter(to,from){
        const userStore= useUserStore()

        if (!userStore.isLoggedIn)return { name: 'login' }
      }
    }

  ]
})

export default router
