<template>
  <v-app>
    <v-app-bar app>
      <v-img @click="goToMenu" style="cursor: pointer"
        src="http://localhost:9000/tfgbucket/assets/logo.png"
        alt="Logo"
        max-width="80"
        class="mr-3"
      ></v-img>

      <v-spacer></v-spacer>
      <!-- Botons centrats -->
      <v-row class="justify-center"> 
        <v-col cols="auto">
          <v-btn @click="goToMenu" color="secondary" text class="mx-2" style="text-transform: none;">
            HOME
          </v-btn>
        </v-col>
        <v-col cols="auto">
          <v-btn @click="goToItemList" color="secondary" text class="mx-2" style="text-transform: none;">
            ALL
          </v-btn>
        </v-col>
        <v-col v-if="isAuthenticated" cols="auto">
          <v-btn v-if="isAuthenticated" @click="goToMyList" color="secondary" text class="mx-2" style="text-transform: none;">
            ByME
          </v-btn>
        </v-col>
      </v-row>
      <v-spacer></v-spacer>

      <!-- Botó per afegir un nou element -->
      <v-tooltip v-if="isAuthenticated" bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="isAuthenticated" icon v-bind="attrs" v-on="on" @click="goToAddItem">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>New Project</span>
      </v-tooltip>

      <!-- Botó d'usuari que mostra informació de l'usuari en una targeta -->
      <v-menu v-if="isAuthenticated" offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-bind="attrs" v-on="on" text>
            <v-icon left>mdi-account</v-icon>
            {{ userName }}
          </v-btn>
        </template>
        <v-card>
          <v-card-title>
            <span class="headline">{{ user.name }}</span>
          </v-card-title>
          <v-card-text>
            <p><strong>Email:</strong> {{ user.email }}</p>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn color="primary" @click="logout">Log Out</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>

      <v-btn v-if="!isAuthenticated && !isLoginPage && !isRegisterPage" @click="goToLogin" color="secondary" text class="mx-2" style="text-transform: none;">
        LOGIN
      </v-btn>
      <v-btn v-if="!isAuthenticated && !isLoginPage && !isRegisterPage" @click="goToRegister" color="secondary" text class="mx-2" style="text-transform: none;">
        SIGN UP
      </v-btn>

    </v-app-bar>

    <v-main class="main-content">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
import { EventBus } from './utils/event-bus';
import apiClient from '@/services/apiClient';
export default {
  data() {
    return {
      isAuthenticated: false,
      userName: '',
      user: {
        name: '',
        email: '',
        role: ''
      },
    };
  },
  computed: {
    isLoginPage() {
      return this.$route.path === '/login';
    },
    isRegisterPage() {
      return this.$route.path === '/register';
    },
  },
  async created() {
    this.updateAuthStatusFromLocalStorage();
    EventBus.$on('authStatusChanged', this.updateAuthStatus);
  },
  beforeDestroy() {
    EventBus.$off('authStatusChanged', this.updateAuthStatus);
  },
  methods: {
    //mètode per actualitzar variables 
    updateAuthStatusFromLocalStorage() {
      const token = localStorage.getItem('token');
      if (token) {
        this.user = JSON.parse(localStorage.getItem('user')) || {};
        this.userName = this.user.name || '';
        this.isAuthenticated = true;
      } else {
        this.isAuthenticated = false;
      }
    },
    //mètode per tancar sessió
    async logout() {
      try {
        await apiClient.post('/logout');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.updateAuthStatus({ isAuthenticated: false });
        this.goToMenu()
      } catch (error) {
        console.error('Error en tancar sessió:', error.response?.data);
      }
    },
    //modifica l'estat quan hi ha canvis en l'usuari actiu
    updateAuthStatus(status) {
      this.isAuthenticated = status.isAuthenticated;
      this.user = status.user || {};
      this.userName = this.user.name || '';
    },
    
    goToLogin() {
      if (!this.isLoginPage) {
        this.$router.push({ path: '/login' });
      }
    },
    goToRegister() {
      if (!this.isRegisterPage) {
        this.$router.push({ path: '/register' });
      }
    },
    goToAddItem() {
      if (this.$route.path !== '/newItem') {
        this.$router.push({ path: '/newItem' }).catch(err => {
          if (err.name !== 'NavigationDuplicated') {
            console.error(err);
          }
        });
      }
    },
    goToMenu() {
      if (this.$route.path !== '/menu') {
        this.$router.push({ path: '/menu' }).catch(err => {
          if (err.name !== 'NavigationDuplicated') {
            console.error(err);
          }
        });
      }
    },
    goToItemList() {
      this.$router.push({ path: '/list', query: { category: null } }).catch(err => {
        if (err.name !== 'NavigationDuplicated') {
          console.error(err);
        }
      });
    },
    goToMyList() {
      this.$router.push({ path: '/list', query: { myItems: true, category: null } }).catch(err => {
        if (err.name !== 'NavigationDuplicated') {
          console.error(err);
        }
      });
    },

  },
};
</script>

<style>
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
}
.v-app-bar {
  z-index: 10; 
}

.main-content {
  padding-top: 64px; 
  padding-bottom: 64px; 
}
</style>
