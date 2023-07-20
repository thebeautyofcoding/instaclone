<template>
  <q-card-actions>
    <span>{{ props.post.likes_count }}</span>
    <q-btn
      :class="{ liked: props.post.is_liked_by_user }"
      flat
      round
      dense
      icon="favorite_border"
      @click="likePost(props.post)"
    />
    <span>{{ props.post.comments_count }}</span>
    <q-btn
      flat
      round
      dense
      icon="chat_bubble_outline"
      @click="toggleComments(props.post)"
    />
  </q-card-actions>
</template>

<script setup lang="ts">
  import { Post } from 'src/types';
  import { api } from 'src/boot/axios';
  import { useQuasar } from 'quasar';
  const $q = useQuasar();
  const props = defineProps({
    post: {
      type: Object as () => Post,
      required: true,
    },
  });

  const toggleComments = (post: Post) => {
    post.showComments = !post.showComments;
  };
  const likePost = (post: Post) => {
    api
      .post(`/posts/${post.id}/like`)
      .then((response) => {
        const isLikedByUser = response.data.is_liked_by_user;
        if (isLikedByUser) {
          $q.notify({
            message: 'Post liked',
            color: 'positive',
            icon: 'thumb_up',
          });
        } else {
          $q.notify({
            message: 'Post unliked',
            color: 'negative',
            icon: 'thumb_down',
          });
        }
      })
      .catch((error) => {
        const errorMessage = error.response.data.message;
        $q.notify({
          message: errorMessage,
          color: 'negative',
          icon: 'error',
        });
      });
  };
</script>

<style scoped>
.liked {
  color: red;
}
</style>
