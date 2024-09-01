<template>
  <v-container>
    <v-row>
      <v-col>
        <video
          src="http://localhost:9000/tfgbucket/assets/BannerVideo.mp4"
          class="banner-video"
          autoplay
          muted
          playsinline
        ></video>
      </v-col>
    </v-row>


    <!-- WASM Intro Text Section -->
    <v-row class="wasm-intro">
      <v-col cols="12">
        <v-card-text>
          <h3>WASM Files</h3>
          <p>Explore optimized WebAssembly (WASM) files that enable high-performance web applications and extend your development capabilities across platforms.</p>
        </v-card-text>
      </v-col>
    </v-row>

    <!-- Popular Categories Title -->
    <v-row class="my-4">
      <v-col cols="12" class="text-center">
        <h4>Popular Categories</h4>
      </v-col>
    </v-row>

    <!-- Botons de categories -->
    <v-row class="my-4">
      <v-col
        v-for="category in categories"
        :key="category.name"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        class="d-flex justify-center"
      >
        <v-btn
          class="mx-4 my-2"
          @click="selectCategory(category)"
          color="terciary"
          elevation="2"
          rounded
        >
          <v-icon left>{{ category.icon }}</v-icon>
          {{ category.name }}
        </v-btn>
      </v-col>
    </v-row>

    <!-- Favorites Section -->
    <v-row v-if="currentUserId" class="my-4 justify-center align-center">
      <v-col cols="auto" class="d-flex align-center justify-center">
        <v-btn
          :disabled="!hasPreviousFavoriteItems"
          @click="loadPreviousFavoriteItems"
          icon
          class="slider-button"
        >
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>

        <h4 @click="goToFavorites" style="cursor: pointer; margin: 0 1rem;">Favorites</h4>

        <v-btn
          :disabled="!hasMoreFavoriteItems"
          @click="loadMoreFavoriteItems"
          icon
          class="slider-button"
        >
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>
      </v-col>
    </v-row>
    <!-- Favorites Slider -->
    <v-row class="slider-container">
      <v-col cols="12" class="slider-content">
        <v-row class="favorites-items-slider">
          <v-col
            v-for="item in visibleFavoriteItems"
            :key="item._id"
            cols="12"
            md="6"
            lg="4"
            class="d-flex justify-center"
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
      </v-col>
    </v-row>

    <!-- Latest Created Section -->
    <v-row class="my-4 justify-center align-center">
      <v-col cols="auto" class="d-flex align-center justify-center">
        <v-btn
          :disabled="!hasPreviousLatestItems"
          @click="loadPreviousLatestItems"
          icon
          class="slider-button"
        >
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>

        <h4 @click="goToLatestCreated" style="cursor: pointer; margin: 0 1rem;">Last Created</h4>

        <v-btn
          :disabled="!hasMoreLatestItems"
          @click="loadMoreLatestItems"
          icon
          class="slider-button"
        >
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>
      </v-col>
    </v-row>

    <!-- Latest Created Slider -->
    <v-row class="slider-container">
      
      <v-col cols="12" class="slider-content">
        <v-row class="latest-items-slider">
          <v-col
            v-for="item in visibleLatestItems"
            :key="item._id"
            cols="12"
            md="6"
            lg="4"
            class="d-flex justify-center"
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
      categories: [
        { name: 'Applications', icon: 'mdi-application' },
        { name: 'Games', icon: 'mdi-gamepad-variant' },
        { name: 'Web Frameworks', icon: 'mdi-web' },
        { name: 'Microservices', icon: 'mdi-cloud-outline' },
        { name: 'Libraries', icon: 'mdi-book-open' },
        { name: 'Encoders', icon: 'mdi-code-braces' },
        { name: 'Databases', icon: 'mdi-database' },
        { name: 'AI', icon: 'mdi-brain' },
        { name: 'Dev', icon: 'mdi-code-tags' },
        { name: 'Extensions', icon: 'mdi-puzzle' },
        { name: 'Blockchain', icon: 'mdi-currency-btc ' },
        { name: 'Security', icon: 'mdi-shield-check' },
      ],
      selectedCategory: null,
      hoveringItemId: null,
      currentUserId: null,
      favorites: [],

      latestPage: 0,
      favoritePage: 0,
      itemsPerPage: 3,
      categoryProjects: [],
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

      window.addEventListener('scroll', this.handleScroll);
    } catch (error) {
      console.error('Error fetching items:', error);
    }
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  computed: {
    visibleLatestItems() {
      return this.paginateAndSortItems(this.latestPage, this.itemsPerPage, true);
    },
    visibleFavoriteItems() {
      return this.paginateFavoriteItems(this.favoritePage, this.itemsPerPage);
    },
    hasMoreLatestItems() {
      return this.items.length > (this.latestPage + 1) * this.itemsPerPage;
    },
    hasPreviousLatestItems() {
      return this.latestPage > 0;
    },
    hasMoreFavoriteItems() {
      return this.items.filter(item => this.favorites.includes(item._id)).length > (this.favoritePage + 1) * this.itemsPerPage;
    },
    hasPreviousFavoriteItems() {
      return this.favoritePage > 0;
    },
  },
  methods: {
    paginateAndSortItems(page, itemsPerPage, isLatest) {
      const start = page * itemsPerPage;
      const end = start + itemsPerPage;
      // Ordena els items de més nou a més antic si és per la secció Latest Created
      const sortedItems = isLatest
        ? this.items.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        : this.items;

      return sortedItems.slice(start, end);
    },
    paginateFavoriteItems(page, itemsPerPage) {
      const start = page * itemsPerPage;
      const end = start + itemsPerPage;
      return this.items
        .filter(item => this.favorites.includes(item._id))
        .slice(start, end);
    },
    viewItem(itemId) {
      this.$router.push({ path: `/items/${itemId}` });
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
    loadMoreLatestItems() {
      if (this.hasMoreLatestItems) {
        this.latestPage += 1;
      }
    },
    loadPreviousLatestItems() {
      if (this.hasPreviousLatestItems) {
        this.latestPage -= 1;
      }
    },
    loadMoreFavoriteItems() {
      if (this.hasMoreFavoriteItems) {
        this.favoritePage += 1;
      }
    },
    loadPreviousFavoriteItems() {
      if (this.hasPreviousFavoriteItems) {
        this.favoritePage -= 1;
      }
    },
    selectCategory(category) {
      this.selectedCategory = category.name;
      this.goToCategory(category.name);
    },
    goToCategory(categoryName) {
      this.$router.push({ path: '/list', query: { category: categoryName } }).catch(err => {
        if (err.name !== 'NavigationDuplicated') {
          console.error(err);
        }
      });
    },
    goToFavorites() {
      this.$router.push({ path: '/list', query: { favorites: 'true' } }).catch(err => {
        if (err.name !== 'NavigationDuplicated') {
          console.error(err);
        }
      });
    },

    goToLatestCreated() {
      this.$router.push({ path: '/list', query: { filter: 'latest' } }).catch(err => {
        if (err.name !== 'NavigationDuplicated') {
          console.error(err);
        }
      });
    },
  }
};
</script>


<style scoped>

.item-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: start;
  justify-content: center;
}

.item-card:hover {
  transform: scale(1.05); 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.favorites-items-slider, 
.latest-items-slider {
  display: flex;
  overflow: hidden;
  width: 100%;
}

.favorites-items-slider > .v-row,
.latest-items-slider > .v-row {
  display: flex;
  flex-wrap: nowrap;
  transition: transform 0.3s ease;
}

.v-col {
  flex: 0 0 auto; 
}

.slider-container {
  display: flex;
  align-items: center;
  position: relative;
}

.slider-button {
  background-color: transparent;
  border: none;
  z-index: 1;
}

.text-center {
  text-align: center;
}

.my-4 {
  margin: 1.5rem 0; 
}

.d-flex {
  display: flex;
}
.align-center {
  align-items: center;
}

.banner-video {
  width: 100%;
  max-height: 400px; 
  object-fit: cover; 
}

</style>
