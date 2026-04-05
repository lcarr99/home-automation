import { useAxios } from "./axios";

export const useLogout = () => {
  const axios = useAxios();

  const logout = () => {
    return new Promise((resolve, reject) => {
      axios
        .post("/api/logout")
        .then(() => resolve(true))
        .catch((error) => reject(error));
    });
  };

  return { logout };
};
