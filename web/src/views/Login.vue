<template>
  <div id="login">
    <article v-if="errorMessages.length" id="loginErrors">
      <ul>
        <li v-for="(message, key) in errorMessages">{{ message }}</li>
      </ul>
    </article>
    <form method="post" @submit.prevent="login">
      <fieldset>
        <label>
          Email
          <input name="email" placeholder="Email" v-model="email" />
        </label>
        <label>
          Password
          <input
            type="password"
            name="password"
            placeholder="Password"
            v-model="password"
          />
        </label>
      </fieldset>
      <button
        :aria-busy="loggingIn"
        :disabled="loggingIn"
        type="submit"
        role="button"
      >
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useLogin } from "../services/login.js";
import { useAuth } from "../services/auth.js";
import { useCsrfCookie } from "../services/csrf-cookie.js";
import { useApplicationStore } from "../stores/application";
import { useUserStore } from "../stores/user";

const csrfCookieService = useCsrfCookie();
const loginService = useLogin();
const authService = useAuth();
const applicationStore = useApplicationStore();
const userStore = useUserStore();

const email = ref(null);
const password = ref(null);
const loggingIn = ref(false);
const errorMessages = ref([]);

csrfCookieService.retrieveCsrfCookie();

const login = () => {
  loggingIn.value = true;

  loginService
    .login({
      email: email.value,
      password: password.value,
    })
    .then(async (response) => {
      applicationStore.isLoading = true;
      userStore.user = await authService.getLoggedInUser();
      userStore.isLoggedIn = true;
      applicationStore.isLoading = false;
    })
    .catch((error) => {
      switch (error?.response?.status) {
        case 422:
          errorMessages.value = [error.response.data.message];
          break;
        default:
          errorMessages.value = ["Unexpected error when logging in"];
          break;
      }
    })
    .finally(() => (loggingIn.value = false));
};
</script>

<style scoped>
#login {
  margin: 10px;
}
</style>
