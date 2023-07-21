<template>
  <q-dialog v-model="showUploadModal">
    <q-card>
      <q-card-section>
        <h6>Upload</h6>
        <q-file
          color="grey-3"
          label-color="blue"
          v-model="image"
          label="Select an image"
          @update:modelValue="handleImagePreview"
        >
          <template v-slot:append>
            <q-icon name="image" color="blue" />
          </template>
        </q-file>
        <q-img spinner-color="blue" :src="imageUrl" style="min-width: 400px" />
        <q-input
          v-model="body"
          label="Description"
          color="grey-3"
          label-color="blue"
        />
      </q-card-section>
      <q-card-section>
        <q-card-actions>
          <q-btn
            color="primary"
            label="Upload"
            @click="handlePostUpload"
            :disable="!image"
          />
          <q-btn
            color="negative"
            label="Cancel"
            @click="showUploadModal = false"
          />
        </q-card-actions>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-layout>
    <q-header elevated>
      <div class="row">
        <div class="col-2">
          <q-toolbar>
            <q-btn
              flat
              round
              dense
              icon="menu"
              aria-label="Menu"
              @click="toggleLeftDrawer"
            />
            <q-toolbar-title>
              <q-icon name="instagram" />
              InstaClone
            </q-toolbar-title>
          </q-toolbar>
        </div>
        <!-- Desktop view-->
        <q-tabs class="desktop-only col-10">
          <q-route-tab
            v-for="link in pageLinks"
            :key="link.title"
            :to="link.link"
            :icon="link.icon"
            :label="link.title"
            class="col-2"
          />
          <q-tab
            v-for="action in actions"
            :key="action.title"
            :icon="action.icon"
            :label="action.title"
            class="col-2"
            @click="action.onClick"
          />
        </q-tabs>
      </div>
    </q-header>

    <!--mobile only left drawer -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      behavior="mobile"
      content-class="bg-grey-1"
    >
      <q-tabs vertical>
        <q-route-tab
          v-for="link in pageLinks"
          :key="link.title"
          :to="link.link"
          :icon="link.icon"
          :label="link.title"
        >
        </q-route-tab>
        <q-tab
          v-for="action in actions"
          :key="action.title"
          @click="action.onClick"
          :label="action.title"
          :icon="action.icon"
        >
        </q-tab>
      </q-tabs>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
  import { AxiosResponse } from 'axios';
  import { storeToRefs } from 'pinia';
  import { useQuasar } from 'quasar';
  import { api } from 'src/boot/axios';
  import { useUserStore } from 'src/stores/user-store';
  import { computed, ref } from 'vue';
  import { useRouter } from 'vue-router';
  const { user } = storeToRefs(useUserStore());
  const router = useRouter();
  const userName = computed(() => user.value.name);
  const isLoggedIn = computed(() => (userName.value ? true : false));
  const $q = useQuasar();
  const showUploadModal = ref(false);

  const imageUrl = ref('');
  const body = ref('');
  const image = ref<File | null>(null);

  const handleImagePreview = (file: File) => {
    imageUrl.value = URL.createObjectURL(file);
  };
  const handlePostUpload = async () => {
    const formData = new FormData();
    formData.append('image', image.value as File);
    formData.append('body', body.value);
    await api
      .post('posts', formData)
      .then((res: AxiosResponse) => {
        $q.notify({
          message: 'Post Uploaded Successfully',
          color: 'positive',
          icon: 'cloud_done',
        });
        showUploadModal.value = false;
      })
      .catch((err) => {
        $q.notify({
          message: 'Post Upload Failed',
          color: 'negative',
          icon: 'cloud_done',
        });
        showUploadModal.value = false;
      });
    showUploadModal.value = false;
  };

  const pageLinks = [
    {
      title: 'Feed',
      caption: 'Daily Updates',
      icon: 'newspaper',
      link: '/',
    },
    {
      title: 'Profile',
      caption: 'Your Instaclone Page',
      icon: 'person',
      link: `/profile/${user.value.id}`,
    },
    {
      title: 'Settings',
      caption: 'Your Personal Settings',
      icon: 'settings',
      link: '/settings',
    },
  ];
  const actions = computed(() => [
    {
      title: 'Upload',
      icon: 'add',
      onClick: () => {
        showUploadModal.value = !showUploadModal.value;
      },
    },
    {
      title: isLoggedIn.value ? 'Logout' : 'Login',
      icon: isLoggedIn.value ? 'logout' : 'login',
      onClick: async () => {
        if (!isLoggedIn.value) {
          router.push('/guest/login');
          return;
        }
        await useUserStore()
          .logout()
          .then(() => {
            $q.notify({
              color: 'positive',
              message: 'Logged out successfully',
              icon: 'check',
            });
            router.push('/guest/login');
          })
          .catch((_) => {
            $q.notify({
              color: 'negative',
              message: 'Error logging out',
              icon: 'report_problem',
            });
          });
      },
    },
  ]);

  const leftDrawerOpen = ref(false);
  const toggleLeftDrawer = () => {
    leftDrawerOpen.value = !leftDrawerOpen.value;
  };
</script>
