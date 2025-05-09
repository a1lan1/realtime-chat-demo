import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

declare global {
    interface Window {
        Pusher: typeof Pusher
        Echo: Echo<any>
    }
}

export default {
  install: () => {
    window.Pusher = Pusher
    window.Echo = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      forceTLS: true,
      wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
      wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
      wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
      enabledTransports: ['ws', 'wss']
    })
  }
}
