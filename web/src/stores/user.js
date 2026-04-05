import { defineStore } from "pinia";

export const useUserStore = defineStore("user", () => {
  const user = null;
  const isLoggedIn = false;

  return { user, isLoggedIn };
});
