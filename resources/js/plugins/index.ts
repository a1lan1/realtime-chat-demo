import echo from './echo'
import { ZiggyVue } from 'ziggy-js'
import type { App } from 'vue'

export function registerPlugins(app: App) {
  app
    .use(echo)
    .use(ZiggyVue)
}
