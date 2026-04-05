<script setup>
import { RouterView } from "vue-router";
import { useAuth } from "./services/auth.js";
import { useApplicationStore } from "./stores/application.js";
import { useUserStore } from "./stores/user.js";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import TopNav from "./components/layout/TopNav.vue";
import SideNav from "./components/layout/SideNav.vue";
import Login from "./views/Login.vue";

const authService = useAuth();
const applicationStore = useApplicationStore();
const userStore = useUserStore();
const router = useRouter();

onMounted(async () => {
  applicationStore.isLoading = true;
  document
    .querySelector("html")
    .setAttribute("data-theme", applicationStore.theme);

  try {
    userStore.user = await authService.getLoggedInUser();
    userStore.isLoggedIn = true;
  } catch (error) {
    router.push({
      name: "home",
    });
  }

  applicationStore.isLoading = false;
});
</script>

<template>
  <header>
    <TopNav ref="top" />
  </header>
  <main>
    <dialog v-if="applicationStore.isLoading" open>
      <article aria-busy="true">
        Retrieving your information, thank you for your patience...
      </article>
    </dialog>
    <section v-else-if="!userStore.isLoggedIn">
      <login />
    </section>
    <section v-else id="main-section">
      <SideNav />
      <div id="main-content">
        <RouterView />
      </div>
    </section>
  </main>
</template>

<style scoped></style>
