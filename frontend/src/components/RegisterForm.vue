<template>
  <v-container fluid class="fullscreen-container">
    <v-row no-gutters class="fullscreen-row">
      <!-- Video Section -->
      <v-col cols="12" md="8" class="d-none d-md-block">
        <video
          src="http://localhost:9000/tfgbucket/assets/RegisterVideo.mp4"
          autoplay
          muted
          playsinline
          class="fullscreen-video"
        ></video>
      </v-col>

      <!-- Registration Form Section -->
      <v-col
        cols="12"
        md="4"
        class="d-flex align-center justify-center"
        style="padding: 20px;"
      >
        <div style="width: 100%; max-width: 400px;">
          <h2 class="text-center"> Register </h2>
          <v-form @submit.prevent="submitForm">
            <v-text-field
              label="Name"
              v-model="name"
              :rules="[rules.required, rules.firstLetterUppercase]"
              required
            ></v-text-field>
            <v-text-field
              label="Email"
              v-model="email"
              type="email"
              :rules="[rules.required, rules.email]"
              required
            ></v-text-field>
            <v-text-field
              label="Password"
              v-model="password"
              type="password"
              :rules="[rules.required, rules.minLength]"
              required
            ></v-text-field>
            <v-text-field
              label="Confirm Password"
              v-model="password_confirmation"
              type="password"
              :rules="[rules.required, validatePasswordConfirmation]"
              required
            ></v-text-field>
            <v-btn type="submit" color="primary" block>Register</v-btn>
          </v-form>
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
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: 'user',
      rules: {
        required: value => !!value || 'Aquest camp és obligatori',
        email: value => /.+@.+\..+/.test(value) || 'Introduïu un correu vàlid',
        minLength: value => (value && value.length >= 6) || 'La contrasenya ha de tenir almenys 6 caràcters',
        firstLetterUppercase: value => {
          if (!value) return true;
          return /^[A-Z]/.test(value) || 'La primera lletra ha de ser majúscula';
        },
      },
    };
  },

  created() {
    EventBus.$on('authStatusChanged', this.updateAuthStatus);
  },
  beforeDestroy() {
    EventBus.$off('authStatusChanged', this.updateAuthStatus);
  },
  methods: {
    updateAuthStatus(status) {
      this.isAuthenticated = status.isAuthenticated;
      this.user = status.user;
    },

    validatePasswordConfirmation(value) {
      return value === this.password || 'Les contrasenyes no coincideixen';
    },
    //metode per enviar les dades al backend 
    async submitForm() {
      try {
        const newUser = {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
          role: this.role
        };

        const response = await apiClient.post('/register', newUser);

        const { user, token } = response.data;

        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));

        EventBus.$emit('authStatusChanged', { isAuthenticated: true, user: user });

        this.$router.push({ path: '/menu' });
      } catch (error) {
        console.error('Error en enviar les dades:', error);
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
