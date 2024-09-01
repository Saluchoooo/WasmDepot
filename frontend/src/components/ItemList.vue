<template>
  <v-container>
    <v-row>
      <!-- Barra de cerca -->
      <v-col cols="12" md="4">
        <v-text-field
          v-model="searchQuery"
          label="Search"
          clearable
          @input="filterItems"
        ></v-text-field>
      </v-col>

      <!-- Seleccionador de filtres -->
      <v-col cols="12" md="4">
        <v-select
          v-model="selectedCategory"
          :items="categories"
          label="Select Category"
          clearable
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <v-select
          v-model="selectedType"
          :items="types"
          label="Select Type"
          clearable
        ></v-select>
      </v-col>
    </v-row>

    <v-row>
      <v-col
        v-for="item in filteredItems"
        :key="item._id"
        cols="12"
        md="6"
        lg="4"
      >
        <v-card
          :class="['item-card', { 'hovered-card': hoveringItemId === item._id }]"
          @click="viewItem(item._id)"
          @mouseover="hoveringItemId = item._id"
          @mouseleave="hoveringItemId = null"
        >
          <v-card-actions class="justify-end">
            <v-btn icon @click.stop="toggleFavorite(item._id)">
              <v-icon :color="isFavorite(item._id) ? 'red' : 'grey'">
                {{ isFavorite(item._id) ? 'mdi-heart' : 'mdi-heart-outline' }}
              </v-icon>
            </v-btn>
          </v-card-actions>
          <v-img :src="item.image_path || 'http://localhost:9000/tfgbucket/assets/logo.png'" height="200px"></v-img>
          <v-card-title>{{ item.title }}</v-card-title>
          <v-card-subtitle>
            <p>{{ item.sinopsis }}</p>
            <v-chip class="ma-2" small>{{ item.categories }}</v-chip>
            <v-chip class="ma-2" small>{{ item.type }}</v-chip>
            <v-chip v-if="item.creatorId === currentUserId || isAdmin" class="ma-2" color="green" text-color="white">
              by {{ item.creatorName }}
            </v-chip>
            <v-chip v-else>by {{ item.creatorName }}</v-chip>
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import apiClient from '@/services/apiClient';

export default {
  data() {
    return {
      items: [],
      categories: ['Games', 'Dev', 'Extensions','Applications','Web Frameworks','Microservices','Libraries','Encoders','Databases','AI','Blockchain','Security'],
      types: ['WASM', 'WASI', 'Projecte'],
      selectedCategory: null,
      selectedType: null,
      searchQuery: '', // Nou model per a la cerca
      hoveringItemId: null,
      currentUserId: null,
      favorites: [], // Per mantenir els favorits
      isAdmin: false,
    };
  },
  async created() {
    try {
      if (localStorage.getItem('user')) {
        const user = JSON.parse(localStorage.getItem('user'));
        this.currentUserId = user._id;
        this.isAdmin = user.role === 'admin';
        this.favorites = user.favorites || [];
      }

      const response = await apiClient.get('/list');
      this.items = response.data;

      this.updateFilters();

    } catch (error) {
      console.error('Error fetching items:', error);
    }
  },

  watch: {
    '$route.query': {
      handler() {
        this.updateFilters();
      },
      immediate: true,
    }
  },
  computed: {
    filteredItems() {
      const showMyItemsOnly = this.$route.query.myItems === 'true';
      const showFavoritesOnly = this.$route.query.favorites === 'true';

      let filtered = this.items.filter(item => {
        const belongsToUser = !showMyItemsOnly || item.creatorId === this.currentUserId;
        const matchesCategory = !this.selectedCategory || item.categories === this.selectedCategory;
        const matchesType = !this.selectedType || item.type === this.selectedType;
        const matchesSearch = item.title.toLowerCase().includes(this.searchQuery.toLowerCase()); 

        return belongsToUser && matchesCategory && matchesType && matchesSearch;
      });

      if (showFavoritesOnly) {
        filtered = filtered.filter(item => this.favorites.includes(item._id));
      }

      if (this.$route.query.filter === 'latest') {
        filtered = filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      }

      return filtered;
    },
  },
  methods: {
    viewItem(itemId) {
      this.$router.push({ path: `/items/${itemId}` });
    },
    updateFilters() {
      this.selectedCategory = this.$route.query.category || null;
      this.selectedType = this.$route.query.type || null;
    },
    toggleFavorite(itemId) {
      const index = this.favorites.indexOf(itemId);
      if (index === -1) {
        this.favorites.push(itemId);
      } else {
        this.favorites.splice(index, 1);
      }
      this.saveFavorites();
    },
    isFavorite(itemId) {
      return this.favorites.includes(itemId);
    },
    saveFavorites() {
      const user = JSON.parse(localStorage.getItem('user'));
      user.favorites = this.favorites;
      apiClient.put(`/items/${user._id}/favorites`, { favorites: user.favorites });
      localStorage.setItem('user', JSON.stringify(user));
    },
    filterItems() {
      this.filteredItems; 
    },
  },
};
</script>

<style scoped>
.item-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}
.hovered-card {
  transform: scale(1.10);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
