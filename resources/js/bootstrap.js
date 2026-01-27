import axios from 'axios';


window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// 👇 Añadimos Laravel Echo y Pusher
window.axios.defaults.withCredentials = true;


