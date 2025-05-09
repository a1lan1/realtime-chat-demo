import { onMounted, onUnmounted } from 'vue'

type EchoCallback<T = unknown> = (data: T) => void

export default function useEcho() {
  const listen = <T = unknown>(channel: string, event: string, callback: EchoCallback<T>) => {
    onMounted(() => {
      window.Echo.channel(channel).listen(event, callback)
    })

    onUnmounted(() => {
      window.Echo.leave(channel)
    })
  }

  return { listen }
}
