import { useAxios } from "./axios";

export const useAuth = () => {
  const { axiosInstance } = useAxios();

  const getLoggedInUser = async () => {
    const response = await axiosInstance.get("/api/user");
    return response.data;
  };

  return { getLoggedInUser };
};
