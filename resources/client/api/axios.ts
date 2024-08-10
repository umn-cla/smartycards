import Axios from 'axios'
import config from '@/config'

const axios = Axios.create({
  baseURL: config.api.baseUrl,
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true,
  withXSRFToken: true
})

export default axios
export { AxiosError } from 'axios'
