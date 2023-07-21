import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [

  // GuestLayout login and register
  {
    path: '/guest',
    component: () => import('layouts/GuestLayout.vue'),
    children: [
      { path: 'login', component: () => import('pages/LoginPage.vue') },
      { path: 'register', component: () => import('pages/RegisterPage.vue') },
    ],
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ path: '', component: () => import('pages/FeedPage.vue') },
    {
      path:'/profile/:id', component:()=>import('pages/ProfilePage.vue')
      },
      {
      path:'/settings',component:()=>import('pages/SettingsPage.vue')
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
