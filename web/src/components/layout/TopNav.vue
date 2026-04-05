<template>
  <article>
    <nav id="top-nav">
      <ul>
        <li>
          <strong> Home Automation </strong>
        </li>
      </ul>
      <ul v-if="userStore.isLoggedIn">
        <li>
          <select @change="setTheme">
            <option
              value="light"
              :selected="applicationStore.theme === 'light'"
            >
              Light
            </option>
            <option value="dark" :selected="applicationStore.theme === 'dark'">
              Dark
            </option>
          </select>
        </li>
      </ul>
    </nav>
  </article>
</template>

<script setup>
import { useUserStore } from "../../stores/user";
import { useApplicationStore } from "../../stores/application";

const userStore = useUserStore();
const applicationStore = useApplicationStore();

const setTheme = (e) => {
  applicationStore.setTheme(e.target.value);
  applicationStore.theme = e.target.value;
  document.querySelector("html").setAttribute("data-theme", e.target.value);
};
</script>

<style scoped></style>
