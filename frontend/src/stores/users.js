import { defineStore } from "pinia";
import axios from "axios";
export const useUserStore = defineStore('user',{
    state(){
        return{
        user: null,
        token : null,
    }
    
},actions:{
    async logout() {
        try {
          // Envoyer la requête de déconnexion avec les en-têtes appropriés
          await axios.post(import.meta.env.VITE_API_URL + '/api/logout', {}, {
            headers: {
              Authorization: 'Bearer ' + this.token
            }
          });
      
          // Réinitialiser l'état local
          this.user = null;
          this.token = null;
      
          // Supprimer les informations de l'utilisateur et le token du localStorage
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          
          // Vous pouvez rediriger l'utilisateur ou afficher un message de succès
        } catch (error) {
          console.error('Error during logout:', error.response ? error.response.data : error.message);
        }
      },
    login(user,token){
        this.user = user
        this.token = token.replace(/"/g, '');
        if(!localStorage.getItem('token')||!localStorage.getItem('user')){
            localStorage.setItem('token',JSON.stringify(this.token));
            localStorage.setItem('user',JSON.stringify(this.user));
        }
    }
},
getters:{
        isLoggedIn() {
            if(this.token != null){
                return true
            }
            return false
        }
    }

})