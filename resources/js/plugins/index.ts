import echo from './echo'
import pinia from './pinia'
import { ZiggyVue } from 'ziggy-js'
import type { App } from 'vue'

export function registerPlugins(app: App) {
  app
    .use(echo)
    .use(pinia)
    .use(ZiggyVue)
}
