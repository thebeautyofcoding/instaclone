<template>
  <q-page padding class="column justify-center items-center text-grey-9">
    <h3 class="q-mb-none">Register</h3>

    <h6 class="q-mt-none">Signup to enjoy the awesome app</h6>
    <div class="" style="min-width: 400px">
      <q-form autofocus @submit="onSubmit" ref="form" class="q-gutter-md">
        <q-input
          v-model="userData.email"
          lazy-rules
          label="Email"
          type="text"
          :rules="[
            (val) => !!val || 'Please enter your email',
            (val) => {
              if (val.includes('@') && val.includes('.')) {
                return true;
              }
              return 'Please enter a valid email';
            },
          ]"
        />
        <q-input
          v-model="userData.name"
          lazy-rules
          label="Name"
          :rules="[(val) => !!val || 'Please enter your name']"
        />
        <q-input
          v-model="userData.password"
          lazy-rules
          label="Password"
          type="password"
          :rules="[(val) => !!val || 'Please enter your password']"
        />
        <!--Confirm password must match password-->
        <q-input
          v-model="userData.password_confirmation"
          lazy-rules
          label="Confirm Password"
          type="password"
          :rules="[
            (val) => userData.password === val || 'Make sure passwords match',
          ]"
        />
        <div>
          <router-link to="/guest/login"
            >Already have an account? Login</router-link
          >
        </div>

        <q-btn type="submit" label="Register" color="primary" class="q-mt-md" />
      </q-form>
    </div>
  </q-page>
</template>
<script setup lang="ts">
  import { useQuasar } from 'quasar';
  import { useRouter } from 'vue-router';
  import { ref } from 'vue';
  import { useUserStore } from 'src/stores/user-store';

  const $q = useQuasar();
  const router = useRouter();
  const userData = ref({
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
  });
  const userStore = useUserStore();
  const form = ref<HTMLFormElement | null>(null);

  const onSubmit = () => {
    userStore
      .register(userData.value)
      .then(() => {
        $q.notify({
          message: 'Registration successful',
          type: 'positive',
        });
        router.push('/');
      })
      .catch((err) => {
        $q.notify({
          message: err.response.data.message,
          type: 'negative',
        });
      });
  };
</script>
