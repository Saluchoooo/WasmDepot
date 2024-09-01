<template>
  <v-container fluid class="fullscreen-container">
    <v-row no-gutters class="fullscreen-row">
      <!-- Video Section -->
      <v-col cols="12" md="8" class="d-none d-md-block">
        <video
          src="http://localhost:9000/tfgbucket/assets/LoginVideo.mp4"
          autoplay
          muted
          playsinline
          class="fullscreen-video"
        ></video>
      </v-col>

      <!-- Login Form Section -->
      <v-col
        cols="12"
        md="4"
        class="d-flex align-center justify-center"
        style="padding: 20px;"
      >
        <div style="width: 100%; max-width: 400px;">
          <h2 class="text-center">Login</h2>
          <v-form @submit.prevent="submitForm">
            <v-text-field
              label="Correu electrònic"
              v-model="email"
              type="email"
              :rules="[rules.required, rules.email]"
              required
            ></v-text-field>
            <v-text-field
              label="Contrasenya"
              v-model="password"
              type="password"
              :rules="[rules.required]"
              required
            ></v-text-field>
            <v-btn type="submit" color="primary" block>Entrar</v-btn>
          </v-form>
          <div class="text-center" style="margin-top: 20px;">
            <span>No tens un compte? </span>
            <v-btn text @click="$router.push('/register')">Registra't</v-btn>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import apiClient from '@/services/apiClient';
import { EventBus } from '@/utils/event-bus';

export default {
  data() {
    return {
      email: '',
      password: '',
      rules: {
        required: value => !!value || 'Aquest camp és obligatori',
        email: value => /.+@.+\..+/.test(value) || 'Introduïu un correu vàlid',
      },
    };
  },

  methods: {
    //metode per enviar l'informacio a API
    async submitForm() {
      try {
        const actUser = {
          email: this.email,
          password: this.password,
        };

        const response = await apiClient.post('/login', actUser);

        const token = response.data.token;
        localStorage.setItem('token', token);

        const user = await apiClient.post('/getUser');
        localStorage.setItem('user', JSON.stringify(user.data.user));

        // Notifica mitjançant event bus que s'ha canviat status 
        EventBus.$emit('authStatusChanged', { isAuthenticated: true, user: user.data.user });

        this.$router.push({ path: '/menu' });
      } catch (error) {
        if (error.response) {
          console.error('Login error:', error.response.data.error || 'Unknown error');
          alert(`Error: ${error.response.data.error || 'Unknown error'}`);
        } else {
          console.error('Connection error:', error.message);
          alert('Connection error. Please try again later.');
        }
      }
    },
  },
};
</script>

<style scoped>
.fullscreen-container {
  height: 100vh;
  overflow: hidden;
  padding: 0;
  margin: 0;
}

.fullscreen-row {
  height: 100%;
  overflow: hidden;
}

.fullscreen-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.text-center {
  text-align: center;
}
</style>
