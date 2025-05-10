import { type App } from 'vue'
import axios, { type AxiosInstance } from 'axios'
import { type PiniaPluginContext } from 'pinia'

export function piniaAxiosPlugin({ store, app }: PiniaPluginContext) {
  store.$axios = app.config.globalProperties.$axios
}

export default {
  install(app: App) {
    app.config.globalProperties.$axios = axios.create({
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    }) as AxiosInstance
  }
}
