import { Ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import type { Room } from '@/types/chat'

export function useChat(room: Ref<Room|null>) {
  const form = useForm({
    content: '',
    room_id: room.value?.id
  })

  const sendMessage = () => {
    if (!form.content.trim() || !room.value) {
      return
    }

    form.processing = true
    form.room_id = room.value.id

    form.post(route('chat.message'), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => form.reset('content'),
      onError: (errors) => console.error('Message send error:', errors),
      onFinish: () => (form.processing = false)
    })
  }

  return {
    form,
    sendMessage
  }
}
