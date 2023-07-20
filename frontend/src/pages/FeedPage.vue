    <template>
  <q-page>
    <div v-for="post in posts" :key="post.id">
      <q-card class="my-card q-gutter-md">
        <div class="q-px-sm row justify-start items-center">
          <router-link :to="`/profile/${post.user.id}`">
            <q-avatar class="q-mr-md">
              <q-img
                v-if="!post.user.avatar_url"
                src="http://localhost:8000/storage/avatars/default.png"
              />

              <q-avatar v-else :src="post.user.avatar_url" />
            </q-avatar>
          </router-link>
          <h6>{{ post.user.name }}</h6>
        </div>
        <q-img
          fit="cover"
          style="
            min-height: 500px;
            min-width: 500px;
            max-height: 500px;
            max-width: 500px;
            margin: auto;
          "
          :src="'http://localhost:8000/storage/' + post.image_path"
        />
        <q-card-section>
          {{ post.body }}
        </q-card-section>
        <q-card-section>
          <PostCardActions :post="post" />
        </q-card-section>
        <q-card-section v-if="post.showComments">
          <CommentSection :post="post" />
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>


    <script setup lang="ts">
  import { storeToRefs } from 'pinia';
  import { usePostStore } from 'src/stores/post-store';
  import { onMounted, onUnmounted } from 'vue';
  import { echo } from 'src/boot/echo';
  import { Post } from 'src/types';
  import PostCardActions from 'src/components/PostCardActions.vue';
  import CommentSection from 'src/components/CommentSection.vue';
  const { posts } = storeToRefs(usePostStore());

  onMounted(async () => {
    await usePostStore().fetchPosts();
    echo
      .channel('posts')
      .listen('PostCreated', (data: { post: Post }) => {
        console.log('post created', data.post);
        usePostStore().postCreated(data.post);
      })
      .listen('PostLiked', (data: { post: Post }) => {
        usePostStore().postLiked(data.post);
      });
  });
  onUnmounted(() => {
    // stop listening to the channel
    usePostStore().setPostsToEmptyArray();
    echo.leaveChannel('posts');
  });
</script>

<style scoped>
.my-card {
  max-width: 500px;
  max-height: auto;
  margin: auto;
}
</style>
