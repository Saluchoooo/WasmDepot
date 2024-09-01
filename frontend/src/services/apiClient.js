import axios from 'axios';

// Crear una instància d'Axios
const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api', 
  headers: {
    'Content-Type': 'application/json',
  },
});

// Afegir un interceptor per incloure el token JWT
apiClient.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token'); // Obtenir el token emmagatzemat
    console.log(token);
    if (token) {
      console.log('ha entrat');
      config.headers['Authorization'] = `Bearer ${token}`; // Afegir el token a les encapçalaments
      console.log('ha sortit');
    }
    return config;
  },
  error => Promise.reject(error)
);

export default apiClient;
