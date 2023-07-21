import { AxiosResponse } from 'axios';
import { defineStore } from 'pinia';
import { api } from 'src/boot/axios';
import { User, UserLoginData, UserRegisterData } from 'src/types';

export interface UserState {
  user: User;
}

export const useUserStore = defineStore('user', {
  state: (): UserState => {
    const userInLocalStorage = localStorage.getItem('user');

    if (userInLocalStorage) {
      return {
        user: JSON.parse(userInLocalStorage)
      };
    }

    return {
      user: {
        name: '',
        avatar_url: '',
        bio: '',
        email: '',
        id: 0,
      }
    };
  },

  actions: {
    async login(user: UserLoginData) {
      return api.post('/login', user).then((response: AxiosResponse<{ user: User, token: string }>) => {
        const userApi = response.data.user;
        const token = response.data.token;
        // locastorage
        localStorage.setItem('user', JSON.stringify(userApi));
        localStorage.setItem('token', token);
        //state
        this.user = userApi;
      })
    },
    async register(user: UserRegisterData) {
      return api.post('/register', user).then((response: AxiosResponse<{ user: User, token: string }>) => {
        const userApi = response.data.user;
        const token = response.data.token;
        // locastorage
        localStorage.setItem('user', JSON.stringify(userApi));
        localStorage.setItem('token', token);
        //state
        this.user = userApi;
      }).catch((error) => {
       throw(error)
      }
      )
    },
    async logout() {
     return  api.post('/logout').then(() => {
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        this.user = {
          name: '',
          avatar_url: '',
          bio: '',
          email: '',
          id: 0,
        }
      } ).catch((error) => {
        throw(error)
       })
    }
  }
});
