import echo from './echo'
import pinia from './pinia'
import axios from './axios'
import { ZiggyVue } from 'ziggy-js'
import type { App } from 'vue'

export function registerPlugins(app: App) {
  app
    .use(echo)
    .use(pinia)
    .use(axios)
    .use(ZiggyVue)
}
