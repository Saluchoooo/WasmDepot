<template>
  <v-container fluid class="d-flex align-center justify-center fill-height">
    <v-card class="fixed-card">
      <!-- Background Image Section -->
      <v-img 
        :src="item.image_path || 'http://localhost:9000/tfgbucket/assets/logo2.png'" 
        height="300px" 
        class="background-image">
      </v-img>

      <!-- Overlapping Image Section -->
      <div class="image-container">
        <v-img 
          :src="item.image_path || 'http://localhost:9000/tfgbucket/assets/logo2.png'" 
          class="square-image">
        </v-img>
      </div>

      <div class="content">
        <v-card-title class="card-title">
          {{ item.title }}
          <v-spacer></v-spacer>
          <v-btn 
            :href="item.web" 
            target="_blank" 
            color="primary" 
            class="small-btn">
            <v-icon>mdi-web</v-icon>
            WEB
          </v-btn>
          <v-btn 
            :href="item.github" 
            target="_blank" 
            color="secondary"
            class="small-btn">
            <v-icon>mdi-github</v-icon>
            GITHUB
          </v-btn>
          <v-btn
            v-if="item.creatorId === currentUserId || isAdmin"
            @click="deleteItem"
            color="red"
            class="small-btn"
          >
            <v-icon>mdi-delete</v-icon>
            DELETE
          </v-btn>
        </v-card-title>

        <!-- Synopsis Section -->
        <v-card-subtitle class="mt-4">
          <p style="font-size: 16px;">{{ item.sinopsis }}</p>
        </v-card-subtitle>
      </div>

      <!-- Tabs Section -->
      <v-tabs v-model="activeTab" background-color="transparent">
        <v-tab>Overview</v-tab>
        <v-tab>Usage</v-tab>
        <v-tab>Screenshots</v-tab>
        <v-tab>Benchmark</v-tab>
      </v-tabs>

      <v-tabs-items v-model="activeTab">
        <!-- Overview Tab -->
        <v-tab-item>
          <v-card-text class="overview-content">
            <v-row>
              <v-col cols="12" md="7">
                <div class="overview-item">
                  <strong>Description:</strong>
                  <div v-html="formatText(item.description)"></div> <!-- Utilitzar v-html per interpretar el HTML -->
                </div>
              </v-col>
              <v-col cols="12" md="5" class="text-right">
                <div class="overview-item">
                  <strong>Language:</strong>
                  <p>{{ item.language || 'Not specified' }}</p>
                </div>
                <div class="overview-item">
                  <strong>Supported Platform:</strong>
                  <p>{{ item.supportedPlatform || 'Not specified' }}</p>
                </div>
                <div class="overview-item">
                  <strong>Compiler Toolchain:</strong>
                  <p>{{ item.compilerToolchain || 'Not specified' }}</p>
                </div>
                <div class="overview-item">
                  <strong>Category:</strong>
                  <p>{{ item.categories }}</p>
                </div>
                <div class="overview-item">
                  <strong>Type:</strong>
                  <p>{{ item.type }}</p>
                </div>
              </v-col>
            </v-row>
            <div class="text-right">
                  <strong>Last Updated:</strong>
                  <p>{{ formatDate(item.updated_at) }}</p>
                </div>
          </v-card-text>
        </v-tab-item>


        <!-- Usage Tab -->
        <v-tab-item>
          <v-card-text class="usage-content">
            <div class="usage-content-inner">
              <!-- Mostrar o modificar el text d'instal·lació -->
              <div v-if="!showForm">
                <div v-html="formatText(item.installation)"></div> <!-- Utilitzar v-html per interpretar el HTML -->
                <div class="centered-btn">
                  <v-btn v-if="item.creatorId === currentUserId || isAdmin" @click="showForm = true" color="primary">
                    Edit Installation Info
                  </v-btn>
                </div>
              </div>
              <v-form 
                v-if="showForm" 
                @submit.prevent="saveInstallation"
                class="installation-form mt-4">
                <v-textarea
                  v-model="installationText" 
                  label="Installation Instructions" 
                  required
                  rows="5"
                  class="full-width"
                ></v-textarea>
                <v-file-input 
                  v-model="file" 
                  @change="handleFileChange('file', $event)" 
                  label="Upload Installation File" 
                  accept="*/*"
                  class="full-width"
                ></v-file-input>
                <div class="button-group">
                  <v-btn type="submit" color="primary" class="short-btn">Save</v-btn>
                  <v-btn @click="showForm = false" color="secondary" class="short-btn">Cancel</v-btn>
                </div>
              </v-form>
            </div>
            <div class="button-container">
              <v-btn
                :href="githubReadmeUrl"
                target="_blank"
                color="secondary"
                class="small-btn"
              >
                <v-icon>mdi-book-open-variant</v-icon>
                View README
              </v-btn>
              <v-btn 
                v-if="item.file_path" 
                :href="item.file_path" 
                target="_blank" 
                color="success"
                class="download-btn">
                <v-icon>mdi-download</v-icon>
                Download File
              </v-btn>
            </div>
          </v-card-text>
        </v-tab-item>

        <!-- Screenshots Tab -->
        <v-tab-item>
          <v-card-text>
            <v-row class="d-flex flex-column align-center justify-center">
              <!-- Mostrar les imatges de captures de pantalla -->
              <v-col
                v-for="(screenshot, index) in item.screenshots"
                :key="index"
                cols="12"
                class="d-flex justify-center mb-4"
              >
                <v-img
                  :src="screenshot"
                  height="500px"
                  max-width="100%"
                  contain
                  class="screenshot-image"
                ></v-img>
              </v-col>

              <!-- Botó per afegir noves imatges -->
              <v-col v-if="item.creatorId === currentUserId || isAdmin" cols="12" class="d-flex justify-center">
                <v-file-input
                  v-model="newScreenshot"
                  accept="image/*"
                  label="Add Screenshot"
                  prepend-icon="mdi-camera"
                  @change="uploadScreenshot"
                  class="add-screenshot-input"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-card-text>
        </v-tab-item>

        <!-- Benchmark Tab -->
        <v-tab-item>
          <v-card-text>
            <div class="usage-content-inner">
              <!-- Mostrar o modificar el text del benchmark -->
              <div v-if="item.benchmark && !showBenchmarkForm">
                <div style="font-size: 16px; text-align: justify;" v-html="formatText(item.benchmark)"></div> <!-- Utilitzar v-html per interpretar el HTML -->
                <div v-if="item.creatorId === currentUserId || isAdmin" class="centered-btn">
                  <v-btn @click="showBenchmarkForm = true" color="primary">
                    Edit Benchmark Text
                  </v-btn>
                </div>
              </div>

              <!-- Formulari per afegir/modificar el text del benchmark -->
              <v-form 
                v-else-if="item.creatorId === currentUserId || isAdmin"
                @submit.prevent="saveBenchmark"
                class="benchmark-form mt-4">
                <v-textarea
                  v-model="benchmarkText" 
                  label="Benchmark Information" 
                  required
                  rows="5"
                  class="full-width"
                ></v-textarea>
                <div class="button-group">
                  <v-btn type="submit" color="primary" class="short-btn">Save</v-btn>
                  <v-btn @click="showBenchmarkForm = false" color="secondary" class="short-btn">Cancel</v-btn>
                </div>
              </v-form>

              <!-- Mostrar les imatges del benchmark -->
              <v-row class="d-flex flex-column align-center justify-center">
                <v-col
                  v-for="(benchmark, index) in item.benchmarks"
                  :key="index"
                  cols="12"
                  class="d-flex justify-center mb-4"
                >
                  <v-img
                    :src="benchmark"
                    height="500px"
                    max-width="100%"
                    contain
                    class="benchmark-image"
                  ></v-img>
                </v-col>

                <!-- Botó per afegir noves imatges -->
                <v-col v-if="item.creatorId === currentUserId || isAdmin" cols="12" class="d-flex justify-center">
                  <v-file-input
                    v-model="newBenchmark"
                    accept="image/*"
                    label="Add Benchmark Image"
                    prepend-icon="mdi-camera"
                    @change="uploadBenchmark"
                    class="add-benchmark-input"
                  ></v-file-input>
                </v-col>
              </v-row>
            </div>
          </v-card-text>
        </v-tab-item>

      </v-tabs-items>
    </v-card>
  </v-container>
</template>


<script>
import apiClient from '@/services/apiClient';

export default {
  data() {
    return {
      item: null,
      activeTab: 0,
      showForm: false,
      showBenchmarkForm: false,
      installationText: '',
      benchmarkText: '', 
      file: null,
      newScreenshot: null,
      newBenchmark: null,
      currentUserId: null,
      isAdmin: false,

    };
  },
  async created() {
    const itemId = this.$route.params.id;
    try {

      if (localStorage.getItem('user')) {
        const user = JSON.parse(localStorage.getItem('user'));
        this.currentUserId = user._id;
        this.isAdmin = user.role === 'admin';
      }
      const response = await apiClient.get(`/items/${itemId}`);
      this.item = response.data;

      if (this.item.benchmark) {
        this.benchmarkText = this.item.benchmark;
      }
    } catch (error) {
      console.error('Error fetching item details:', error);
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'Not available';
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    saveInstallation() {
      const formData = new FormData();
      let hasChanges = false;

      if (this.installationText.trim()) {
        formData.append('installation', this.installationText);
        hasChanges = true;
      }
      
      if (this.file) {
        formData.append('file', this.file);
        hasChanges = true;
      }

      if (!hasChanges) {
        console.log('No changes to save.');
        return;
      }

      apiClient.post(`/items/${this.item._id}/installation`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
        .then(response => {
          if (this.installationText.trim()) {
            this.item.installation = this.installationText;
          }
          if (this.file) {
            this.item.file_path = response.data.file_path;
          }
          this.showForm = false;
          this.installationText = '';
          this.file = null;
        })
        .catch(error => {
          console.error('Error saving installation info:', error);
        });
    },
    saveBenchmark() {
      const formData = new FormData();
      let hasChanges = false;

      if (this.benchmarkText.trim()) {
        formData.append('benchmark', this.benchmarkText);
        hasChanges = true;
      }

      if (this.newBenchmark) {
        formData.append('benchmark', this.newBenchmark);
        hasChanges = true;
      }

      if (!hasChanges) {
        console.log('No changes to save.');
        return; 
      }

      apiClient.post(`/items/${this.item._id}/benchmarkText`, formData)
        .then(response => {
          if (this.benchmarkText.trim()) {
            this.item.benchmark = this.benchmarkText;
          }
          if (this.newBenchmark) {
            this.item.benchmarks.push(response.data.benchmark_path);
          }
          this.showBenchmarkForm = false;
          this.benchmarkText = '';
          this.newBenchmark = null;
        })
        .catch(error => {
          console.error('Error saving benchmark text:', error);
        });
    },

    uploadScreenshot() {
      if (this.newScreenshot) {
        const formData = new FormData();
        formData.append('screenshot', this.newScreenshot);

        apiClient.post(`/items/${this.item._id}/screenshots`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
          .then(response => {
            this.item.screenshots.push(response.data.screenshot_path);
            this.newScreenshot = null;
          })
          .catch(error => {
            console.error('Error uploading screenshot:', error);
          });
      }
    },
    uploadBenchmark() {
      if (this.newBenchmark) {
        const formData = new FormData();
        formData.append('benchmark', this.newBenchmark);

        apiClient.post(`/items/${this.item._id}/benchmarkImage`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
          .then(response => {
            this.item.benchmarks.push(response.data.benchmark_path);
            this.newBenchmark = null;
          })
          .catch(error => {
            console.error('Error uploading benchmark image:', error);
          });
      }
    },
    handleFileChange(field, event) {
      if (event.target.files.length > 0) {
        this[field] = event.target.files[0];
      }
    },

    async deleteItem() {
      try {
        await apiClient.delete(`/items/${this.item._id}`);
        this.$router.push({ path: `/list` });
      } catch (error) {
        console.error('Error deleting item:', error);
      }
    },
    formatText(text) {
      return text ? text.replace(/\n/g, '<br>') : '';
    },

  },
  computed: {
    githubReadmeUrl() {
      return `${this.item.github}/blob/master/README.md`;
    },
  },
};
</script>

<style scoped>
.fill-height {
  height: 100vh;
}

.fixed-card {
  width: 100%;
  max-width: 900px;
  position: relative;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.9);
}

.background-image {
  object-fit: cover;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}

.image-container {
  position: absolute;
  top: 180px; /* Ajusta aquest valor segons sigui necessari */
  left: 20px;
  z-index: 0;
}

.square-image {
  width: 150px;
  height: 150px;
  border-radius: 8px;
  border: 5px solid white;
}

.content {
  padding: 16px;
}

.card-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 20px;
}

.card-title .v-btn {
  margin-left: 8px;
}

.small-btn {
  font-size: 14px;
  padding: 6px 12px;
}

.v-icon {
  margin-right: 8px;
}

.v-card-subtitle p {
  font-size: 16px;
  text-align: justify;
}

.overview-content {
  font-size: 16px;
  text-align: justify;
}

.usage-content {
  font-size: 16px;
  text-align: justify;
}


.benchmark-form {
  font-size: 16px;
  text-align: justify;
}

.overview-item,
.benchmark-form p {
  margin-bottom: 16px;
}

.overview-item strong,
.benchmark-form strong {
  display: block;
  margin-bottom: 4px;
}

.download-btn {
  margin-top: 16px;
}

.v-tabs .v-tab {
  font-weight: bold;
}

.v-tabs .v-tab.v-tab--active {
  color: #1976d2;
}

.centered-btn {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
}

.button-group {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.short-btn {
  padding: 6px 16px;
  min-width: 100px;
}

.installation-form,
.benchmark-form {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  width: 100%;
}

.add-screenshot-input,
.add-benchmark-input {
  width: 100%;
}

.button-container {
  display: flex;
  justify-content: center;
  margin-top: 16px;
}

.button-container .v-btn {
  margin: 0 8px;
}

</style>
