import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (typeof window !== 'undefined') {
    axios.defaults.baseURL = window.location.origin;
}

export default axios;
