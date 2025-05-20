<template>
  <div>
    <h1>My Boards</h1>
    <div v-for="board in boards" :key="board.id">
      <h2>{{ board.name }}</h2>
      <p>{{ board.description }}</p>
      <!-- Render columns here -->
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      boards: [],
    };
  },
  mounted() {
    this.fetchBoards();
  },
  methods: {
    async fetchBoards() {
      const response = await axios.get('/api/boards');
      this.boards = response.data;
    },
  },
};
</script>