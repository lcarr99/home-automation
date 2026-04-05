import { useAxios } from "./axios";

export const useLogin = () => {
  const { axiosInstance } = useAxios();
  const login = async ({ email, password }) => {
    return new Promise((resolve, reject) => {
      axiosInstance
        .post("/api/login", {
          email: email,
          password: password,
        })
        .then((response) => resolve(response.data))
        .catch((error) => reject(error));
    });
  };

  return { login };
};
