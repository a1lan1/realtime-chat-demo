type EchoCallback<T = unknown> = (data: T) => void

export default function useEcho() {
  const listen = <T = unknown>(channel: string, event: string, callback: EchoCallback<T>) => {
    window.Echo.channel(channel).listen(event, callback)
  }

  const leave = (channel: string) => {
    window.Echo.leave(channel)
  }

  return { listen, leave }
}
