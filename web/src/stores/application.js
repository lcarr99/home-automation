import { defineStore } from "pinia";

export const useApplicationStore = defineStore("application", () => {
  const isLoading = false;

  var theme = localStorage.getItem("theme") ?? "light";

  const setTheme = (selectedTheme) => {
    localStorage.setItem("theme", selectedTheme);
    theme = selectedTheme;
  };

  return { isLoading, setTheme, theme };
});
