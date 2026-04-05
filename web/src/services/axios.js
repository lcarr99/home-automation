import axios from "axios";

export const useAxios = () => {
  const axiosInstance = axios.create({
    baseURL: "http://localhost",
    withCredentials: true,
    withXSRFToken: true,
  });

  return { axiosInstance };
};
