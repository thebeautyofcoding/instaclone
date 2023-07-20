<template>
  <div>
    <q-scroll-area style="height: 200px">
      <div v-if="loadingComments">
        <div v-for="n in 3" :key="n">
          <q-skeleton animated class="q-my-md">
            <q-skeleton type="circle" animated>
              <q-avatar>
                <div style="width: 100%; height: 100%; background: #ccc"></div>
              </q-avatar>
            </q-skeleton>
          </q-skeleton>
        </div>
      </div>

      <!-- Comments -->
      <div v-else v-for="comment in comments" :key="comment.id">
        <div class="comment">
          <router-link :to="`/profile/${comment.user.id}`">
            <q-avatar class="q-mr-md">
              <q-img
                v-if="!comment.user.avatar_url"
                src="http://localhost:8000/storage/avatars/default.png"
              />

              <q-avatar v-else :src="comment.user.avatar_url" />
            </q-avatar>
          </router-link>
          <div class="col">
            <strong>
              {{ comment.user.name }}
            </strong>
            <p>{{ comment.body }}</p>
          </div>

          <!-- Replies -->
          <div v-for="reply in comment.replies" :key="reply.id">
            <div class="reply">
              <router-link :to="`/profile/${reply.user.id}`">
                <q-avatar class="q-mr-md" size="md">
                  <q-img
                    v-if="!reply.user.avatar_url"
                    src="http://localhost:8000/storage/avatars/default.png"
                  />

                  <q-avatar v-else :src="reply.user.avatar_url" />
                </q-avatar>
              </router-link>
              <div>
                <strong>
                  {{ reply.user.name }}
                </strong>
                <p>{{ reply.body }}</p>
              </div>
            </div>
          </div>
          <!-- End of Replies -->
          <!--Reply form-->

          <div v-if="idOfCommentToShowReplyFormFor === comment.id">
            <q-input
              v-model="replyText"
              filled
              dense
              placeholder="Reply to comment"
              lazy-rules
              :rules="[(val) => !!val || 'Reply cannot be empty']"
            />
            <q-btn
              flat
              dense
              color="primary"
              label="Reply"
              @click="addReply(comment.id)"
            />
            <q-btn
              flat
              dense
              color="secondary"
              label="Cancel"
              @click="idOfCommentToShowReplyFormFor = undefined"
            />
          </div>
        </div>
        <q-btn
          flat
          dense
          color="primary"
          label="Reply"
          @click="idOfCommentToShowReplyFormFor = comment.id"
        />
      </div>
      <!-- comment form -->
      <div v-if="showCommentForm">
        <q-input
          v-model="commentText"
          filled
          dense
          placeholder="Add a comment"
          lazy-rules
          :rules="[(val) => !!val || 'Comment cannot be empty']"
        />
        <q-btn
          flat
          dense
          color="primary"
          label="Comment"
          @click="addComment(props.post.id)"
        />
        <q-btn
          flat
          dense
          color="secondary"
          label="Cancel"
          @click="showCommentForm = false"
        />
      </div>
      <q-btn
        v-else
        flat
        dense
        color="primary"
        label="Add a comment"
        @click="showCommentForm = true"
      />
    </q-scroll-area>
  </div>
</template>



<script setup lang="ts">
  import { storeToRefs } from 'pinia';
  import { Post } from 'src/types';
  import { useCommentStore } from 'src/stores/comment-store';
  import { onMounted, onUnmounted, ref } from 'vue';
  import { api } from 'src/boot/axios';
  import { echo } from 'src/boot/echo';
  const props = defineProps({
    post: {
      type: Object as () => Post,
      required: true,
    },
  });
  const { comments } = storeToRefs(useCommentStore());
  const commentText = ref('');
  const replyText = ref('');
  const showCommentForm = ref(false);
  const idOfCommentToShowReplyFormFor = ref<number>();
  const loadingComments = ref(true);
  import { Comment } from 'src/types';

  const addComment = async (postId: number) => {
    try {
      await api.post(`/posts/${postId}/comments`, {
        body: commentText.value,
      });
      commentText.value = '';
      showCommentForm.value = false;
    } catch (error) {
      throw error;
    }
  };

  const addReply = async (commentId: number) => {
    try {
      await api.post(`/comments/${commentId}/replies`, {
        body: replyText.value,
      });
      replyText.value = '';
      idOfCommentToShowReplyFormFor.value = undefined;
    } catch (error) {
      throw error;
    }
  };
  onMounted(async () => {
    await useCommentStore().fetchComments(props.post.id);
    loadingComments.value = false;
    echo
      .channel('posts')
      .listen('CommentPosted', (data: { comment: Comment }) => {
        useCommentStore().commentPosted(props.post.id, data.comment);
      })
      .listen('ReplyPosted', (data: { reply: Comment }) => {
        useCommentStore().replyPosted(data.reply);
      });
  });
  onUnmounted(() => {
    echo.leave('posts');
  });
</script>
<style  scoped>
.comment {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  flex-shrink: 0;
}
.reply {
  padding: 0.5rem;
  margin: 0.5rem 2rem;
  border-left: 1px solid #ddd;
}
</style>
