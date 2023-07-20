<template>
  <q-page>
    <q-card>
      <q-card-section>
        <div class="row items-center q-gutter-md">
          <q-avatar size="150px">
            <q-skeleton
              v-if="loadingAvatar"
              type="circle"
              size="150px"
              animated
            />
            <q-img
              v-else
              :src="'http://localhost:8000' + user.avatar_url"
              spinner-color="white"
              spinner-size="50"
              @load="loadingAvatar = false"
            />
          </q-avatar>
          <div class="q-gutter-xs">
            <div class="">
              <div class="text-dark text-h6">Username</div>
              <q-skeleton v-if="loadingProfileData" type="text" animated />
              <div v-else>{{ user.name }}</div>
            </div>
            <div>
              <div class="text-dark text-h6">Bio</div>
              <q-skeleton v-if="loadingProfileData" type="text" animated />
              <div v-else>{{ user.bio }}</div>
            </div>
          </div>
          <div class="q-ml-lg">
            <q-skeleton v-if="loadingProfileData" type="text" animated />
            <div v-else class="text-dark text-h6">
              <div>{{ posts.length }}</div>
            </div>
            {{ posts.length === 1 ? 'post' : 'posts' }}
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!--posts-->
    <div class="row q-gutter-md q-mt-md justify-start">
      <div
        class="col-12 col-md-3 col-sm-5"
        v-for="post in posts"
        :key="post.id"
      >
        <q-card @click="showPostDetails(post)">
          <q-img
            class="cursor-pointer fit"
            :src="'http://localhost:8000/storage/' + post.image_path"
            spinner-color="white"
            spinner-size="50"
          />
        </q-card>
      </div>
    </div>
    <!--Dialog-->
    <q-dialog
      @update:model-value="handleDialogClose"
      v-model="showDialog"
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card v-if="selectedPost">
        <q-card-section>
          <div class="text-h6">{{ selectedPost.body }}</div>
        </q-card-section>
        <q-img
          fit="cover"
          style="width: 500px; height: 300px"
          :src="'http://localhost:8000/storage/' + selectedPost.image_path"
        />
        <q-card-section>
          <PostCardActions :post="selectedPost" />
        </q-card-section>
        <q-card-section>
          <CommentSection :post="selectedPost" :loading="loadingComments" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>



<script setup lang="ts">
  import { storeToRefs } from 'pinia';
  import { api } from 'src/boot/axios';
  import { useCommentStore } from 'src/stores/comment-store';
  import { usePostStore } from 'src/stores/post-store';
  import { Post } from 'src/types';
  import { onMounted, onUnmounted, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { echo } from 'src/boot/echo';
  import CommentSection from 'src/components/CommentSection.vue';
  import PostCardActions from 'src/components/PostCardActions.vue';

  const user = ref({ name: '', bio: '', avatar_url: '' });
  const { posts } = storeToRefs(usePostStore());
  const showDialog = ref(false);
  const selectedPost = ref<Post | undefined>(undefined);
  const selectedPostComments = ref<Comment[]>([]);
  const loadingProfileData = ref(true);
  const loadingComments = ref(true);
  const loadingAvatar = ref(true);
  const router = useRouter();

  const showPostDetails = (post: Post) => {
    echo
      .channel('posts')
      .listen('PostCreated', (data: { post: Post }) => {
        usePostStore().postCreated(data.post);
      })
      .listen('PostLiked', (data: { post: Post }) => {
        usePostStore().postLiked(data.post);
      });
    selectedPost.value = post;
    showDialog.value = true;
    loadingComments.value = true;
    useCommentStore()
      .fetchComments(post.id)
      .then((comments) => {
        selectedPostComments.value = comments as Comment[] | [];
        loadingComments.value = false;
      });
  };

  const fetchUserData = async () => {
    try {
      const response = await api.get(
        '/users/' + router.currentRoute.value.params.id
      );
      console.log(response.data.user);
      user.value = response.data.user;
      loadingProfileData.value = false;
      loadingAvatar.value = false;
    } catch (_) {
      loadingProfileData.value = false;
      loadingAvatar.value = false;
    }
  };
  const handleDialogClose = (newValue: boolean) => {
    if (!newValue) {
      selectedPost.value = undefined;
      selectedPostComments.value = [];
      echo
        .channel('posts')
        .stopListening('PostCreated')
        .stopListening('PostLiked');
    }
  };
  onMounted(async () => {
    await fetchUserData();
    loadingProfileData.value = false;
    await usePostStore().fetchUsersPosts(
      Number(router.currentRoute.value.params.id)
    );
    echo
      .channel('posts')
      .listen('PostCreated', (data: { post: Post }) => {
        usePostStore().postCreated(data.post);
      })
      .listen('PostLiked', (data: { post: Post }) => {
        usePostStore().postLiked(data.post);
      });
  });
</script>


