import { defineStore } from 'pinia';
import { api } from 'src/boot/axios';
import { Post } from 'src/types';
import { useUserStore } from './user-store';

export interface PostState{
  posts: Post[]
}

export const usePostStore = defineStore('post', {
  state: (): PostState => ({
    posts: []
  }),

  actions: {
    async fetchPosts() {
      const response = await api.get('/posts');
      // add showComments property to each post and set it to false
   this.posts = response.data.posts.map((post: Post) => {
        return { ...post, showComments: false };
      }
      );
    },

    async fetchUsersPosts(userId: number) {
      const response = await api.get(`/users/${userId}/posts`);
      this.posts = response.data.posts.map((post: Post) => {
        return { ...post, showComments: false };
      });
    },
    postCreated(post: Post) {
      if(this.router.currentRoute.value.path.startsWith('/profile') && this.router.currentRoute.value.params.id !== post.user.id.toString()) {
        return;
      }

      if (this.router.currentRoute.value.path === '/') {
        this.posts.unshift(post);
        return;
      }

      this.posts.unshift(post);
    },
    postLiked(post:Post) {
      const likedPost = this.posts.find((post) => post.id === post.id);
      if (likedPost) {
        likedPost.likes_count = post.likes_count
        likedPost.is_liked_by_user=post.is_liked_by_user&&post.user_id_who_liked===useUserStore().user.id
      }
    },
    updateCommentsCount(postId: number, commentsCount: number) {
      const postIndex = this.posts.findIndex((post) => post.id === postId);
      if (postIndex !== -1) {
        this.posts[postIndex].comments_count = commentsCount;
      }
    },
    setPostsToEmptyArray() {
      this.posts = [];
    }
  },
});
