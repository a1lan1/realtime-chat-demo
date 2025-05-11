<script setup lang="ts">
import { toRef } from 'vue'
import useEcho from '@/composables/useEcho'
import { useChat } from '@/composables/useChat'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ChatRoom from '@/components/chat/ChatRoom.vue'
import type { BreadcrumbItem } from '@/types'
import type { Room } from '@/types/chat'

const props = defineProps<{
    rooms: Room[];
    room: Room|null;
}>()

const { listen } = useEcho()
const { form, sendMessage } = useChat(toRef(props, 'room'))

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Chat Inertia',
    href: route('chat.inertia')
  }
]

const navigateToRoom = (roomId: number) => {
  router.visit(
    route('chat.inertia', { roomId })
  )
}

listen(
  `room.${props.room?.id}`,
  '.message.new',
  () => router.reload()
)
</script>

<template>
  <Head title="Chat" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <ChatRoom
      v-model="form.content"
      :loading="form.processing"
      :rooms="rooms"
      :active-room="room"
      :messages="room?.messages"
      @submit="sendMessage"
      @show-room="navigateToRoom"
    />
  </AppLayout>
</template>
