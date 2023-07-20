<template>
  <q-page padding>
    <div class="container">
      <h6 class="text-grey-9">Profile Settings</h6>

      <q-avatar size="200px">
        <q-skeleton v-if="loadingData" type="circle" animated size="200px" />
        <q-img
          v-if="user.avatar_url"
          :src="'http://localhost:8000' + user.avatar_url"
        />
        <q-img v-else src="http://localhost:8000/storage/avatars/default.png" />
      </q-avatar>
      <q-file
        class="q-mt-md"
        v-model="avatarFile"
        accept="image/*"
        @update:model-value="uploadImage"
        outlined
        dense
      >
        <template v-slot:prepend>
          <q-icon name="image" />
        </template>
      </q-file>

      <q-skeleton
        v-if="loadingData"
        animated
        type="QInput"
        height="50"
        label="Name"
        class="q-mt-md"
        outlined
      />
      <q-input
        v-else
        v-model="user.name"
        label="Name"
        outlined
        class="q-mt-md"
        dense
      />
      <q-skeleton
        v-if="loadingData"
        animated
        type="QInput"
        height="50"
        label="Bio"
        class="q-mt-md"
        outlined
      />
      <q-input
        v-else
        v-model="user.bio"
        label="Bio"
        outlined
        class="q-mt-md"
        dense
      />
      <q-btn
        color="primary"
        label="Update Profile"
        class="q-mt-md"
        :disable="loadingData"
        @click="updateUserData(user)"
      >
        <template v-slot:loading>
          <q-spinner></q-spinner>
        </template>
      </q-btn>
    </div>
  </q-page>
</template>

<script setup lang="ts">
  import { useQuasar } from 'quasar';
  import { api } from 'src/boot/axios';
  import { onMounted, ref } from 'vue';

  const user = ref({
    name: '',
    avatar_url: '',
    bio: '',
  });

  const $q = useQuasar();
  const avatarFile = ref<File | null>(null);

  const loadingData = ref(true);

  const uploadImage = async () => {
    if (!avatarFile.value) {
      return;
    }
    const formData = new FormData();
    formData.append('avatar', avatarFile.value);
    try {
      const response = await api.post('/settings/avatar', formData);
      const avatarUrl = response.data.user.avatar_url;
      user.value.avatar_url = avatarUrl;
      $q.notify({
        message: 'Avatar updated',
        color: 'positive',
        icon: 'thumb_up',
      });
    } catch (_) {
      $q.notify({
        message: 'Avatar update failed',
        color: 'negative',
        icon: 'error',
      });
    }
  };

  const updateUserData = async (userData: {
    name: string;
    bio: string;
    avatar_url: string;
  }) => {
    try {
      await api.post('/settings', userData);
      $q.notify({
        message: 'User data updated',
        color: 'positive',
        icon: 'thumb_up',
      });
    } catch (_) {
      $q.notify({
        message: 'User data update failed',
        color: 'negative',
        icon: 'error',
      });
    }
  };

  onMounted(async () => {
    try {
      const response = await api.get('/settings');
      user.value = response.data.user;
      loadingData.value = false;
    } catch (_) {
      $q.notify({
        message: 'Failed to load user data',
        color: 'negative',
        icon: 'error',
      });
    }
  });
</script>
<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
}
</style>
