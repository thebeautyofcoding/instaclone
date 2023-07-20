import { AxiosResponse } from 'axios';
import { defineStore } from 'pinia';
import { api } from 'src/boot/axios';
import { usePostStore } from './post-store';
import { Comment } from 'src/types';

interface CommentState {
  comments: Comment[];
}

export const useCommentStore = defineStore('comment', {
  state: ():CommentState => ({
    comments: [],
  }),
  actions: {
    async fetchComments(postId: number) {
      const response: AxiosResponse<{ comments: Comment[] }> = await api.get(`/posts/${postId}/comments`)
        .catch((error) => {
          throw new Error(error);
         });
      this.comments = response.data.comments;
      return this.comments;
    },

    commentPosted(postId:number,comment: Comment) {
      this.comments = [...this.comments, comment]
      usePostStore().updateCommentsCount(postId, this.comments.length)
    },
    replyPosted(reply: Comment) {
      const commentIndex = this.comments.findIndex((comment) => comment.id === reply.parent_id);
      if (commentIndex !== -1) {
        //check if replies property exists on comment
        if (!this.comments[commentIndex].replies) {
          this.comments[commentIndex].replies = [];
        }

        this.comments[commentIndex].replies = [...this.comments[commentIndex].replies, reply];
      }
    }
  },
})
