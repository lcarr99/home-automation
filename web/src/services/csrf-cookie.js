import { useAxios } from "./axios";

export const useCsrfCookie = () => {
  const { axiosInstance } = useAxios();

  const retrieveCsrfCookie = () => {
    axiosInstance.get("/sanctum/csrf-cookie");
  };

  return { retrieveCsrfCookie };
};
