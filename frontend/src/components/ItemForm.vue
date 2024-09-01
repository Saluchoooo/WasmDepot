<template>
  <v-container fluid class="fullscreen-container">
    <v-row no-gutters class="fullscreen-row">
      <!-- Form Section -->
      <v-col
        cols="12"
        md="6"
        class="d-flex align-start justify-center"
        style="padding: 50px; padding-top: 100;" 
      >
        <div class="form-container">
          <h1 class="text-center mb-4">New Project</h1>
          <v-form @submit.prevent="submitForm">
            <!-- Title -->
            <v-text-field
              label="Title"
              v-model="form.title"
              required
            ></v-text-field>

            <v-row>
              <!-- Sinopsis -->
              <v-col cols="12">
                <v-text-field
                  label="Sinopsis"
                  v-model="form.sinopsis"
                  required
                ></v-text-field>
              </v-col>

              <!-- Description -->
              <v-col cols="12">
                <v-textarea
                  label="Description"
                  v-model="form.description"
                  required
                ></v-textarea>
              </v-col>
            </v-row>

            <v-row>
              <!-- Web -->
              <v-col cols="12" md="6">
                <v-text-field
                  label="Web"
                  v-model="form.web"
                  type="url"
                ></v-text-field>
              </v-col>

              <!-- GitHub -->
              <v-col cols="12" md="6">
                <v-text-field
                  label="GitHub"
                  v-model="form.github"
                  type="url"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <!-- Image -->
              <v-col cols="12">
                <v-file-input
                  label="Image"
                  v-model="form.image"
                  @change="handleFileChange('image', $event)"
                  accept="image/*"
                  required
                ></v-file-input>
              </v-col>
            </v-row>

            <v-row>
              <!-- Categories -->
              <v-col cols="12" md="6">
                <v-select
                  label="Categories"
                  v-model="form.categories"
                  :items="categories"
                  required
                ></v-select>
              </v-col>

              <!-- Type -->
              <v-col cols="12" md="6">
                <v-select
                  label="Type"
                  v-model="form.type"
                  :items="types"
                  required
                ></v-select>
              </v-col>
            </v-row>

            <v-row>
              <!-- Language -->
              <v-col cols="12" md="4">
                <v-text-field
                  label="Language"
                  v-model="form.language"
                ></v-text-field>
              </v-col>

              <!-- Supported Platform -->
              <v-col cols="12" md="4">
                <v-text-field
                  label="Supported Platform"
                  v-model="form.supportedPlatform"
                ></v-text-field>
              </v-col>

              <!-- Compiler Toolchain -->
              <v-col cols="12" md="4">
                <v-text-field
                  label="Compiler Toolchain"
                  v-model="form.compilerToolchain"
                ></v-text-field>
              </v-col>
            </v-row>

            <!-- Submit Button -->
            <v-btn type="submit" color="primary" class="mt-4" block>Create</v-btn>
          </v-form>
        </div>
      </v-col>
      
      <!-- Video Section -->
      <v-col cols="12" md="6" class="d-none d-md-block">
        <video
          src="http://localhost:9000/tfgbucket/assets/NewItemVideo.mp4"
          autoplay
          muted
          playsinline
          class="fullscreen-video"
        ></video>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import apiClient from '@/services/apiClient';

export default {
  data() {
    return {
      form: {
        title: '',
        description: '',
        sinopsis: '',
        image: null,
        web: '',
        github: '',
        categories: '',
        type: '',
        language: '', 
        supportedPlatform: '', 
        compilerToolchain: '', 
        creatorId: '',
        creatorName: ''
      },
      categories: [
        'Games', 'Dev', 'Extensions', 'Applications', 'Web Frameworks',
        'Microservices', 'Libraries', 'Encoders', 'Databases', 'AI',
        'Blockchain', 'Security'
      ],
      types: ['WASM', 'WASI', 'Project']
    };
  },

  created() {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user) {
      this.form.creatorId = user._id;
      this.form.creatorName = user.name;
      console.log('Creator ID:', this.form.creatorId);
      console.log('Creator Name:', this.form.creatorName);
    } else {
      console.error('User not found in localStorage.');
    }
  },

  methods: {
    async submitForm() {
      const formData = new FormData();
      formData.append('title', this.form.title);
      formData.append('sinopsis', this.form.sinopsis);
      formData.append('description', this.form.description);
      formData.append('image', this.form.image);
      formData.append('web', this.form.web);
      formData.append('github', this.form.github);
      formData.append('categories', this.form.categories);
      formData.append('type', this.form.type);
      formData.append('language', this.form.language); 
      formData.append('supportedPlatform', this.form.supportedPlatform); 
      formData.append('compilerToolchain', this.form.compilerToolchain); 
      formData.append('creatorId', this.form.creatorId);
      formData.append('creatorName', this.form.creatorName);

      try {
        const response = await apiClient.post('/newItem', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        console.log(response.data);
        this.$router.push({ path: `/items/${response.data.itemId}` });
      } catch (error) {
        console.error('Error submitting the form:', error);
      }
    },
    handleFileChange(type, event) {
      this.form[type] = event.target.files[0];
    },
  }
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

.form-container {
  width: 100%;
  max-width: 600px;
}

.v-card {
  box-shadow: none;
  border: none;
}
</style>
