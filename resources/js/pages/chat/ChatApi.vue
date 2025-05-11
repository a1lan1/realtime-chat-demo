<script setup lang="ts">
import { watch, onMounted, onUnmounted } from 'vue'
import { storeToRefs } from 'pinia'
import useEcho from '@/composables/useEcho'
import { useChatStore } from '@/stores/chat'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ChatRoom from '@/components/chat/ChatRoom.vue'
import type { BreadcrumbItem } from '@/types'

const { listen, leave } = useEcho()

const chatStore = useChatStore()
const { loading, storing, rooms, room, form } = storeToRefs(chatStore)
const { fetchRoomList, fetchRoom, sendMessage } = chatStore

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Chat API',
    href: route('chat.api')
  }
]

watch(() => room.value?.id, (newRoomId, oldRoomId) => {
  if (oldRoomId) {
    leave(`room.${oldRoomId}`)
  }

  if (newRoomId) {
    listen(
      `room.${newRoomId}`,
      '.message.new',
      () => fetchRoom(newRoomId)
    )
  }
}, { immediate: true })

onMounted(() => fetchRoomList())
onUnmounted(() => chatStore.$reset())
</script>

<template>
  <Head title="Chat" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <ChatRoom
      v-model="form.content"
      :loading="loading || storing"
      :rooms="rooms"
      :active-room="room"
      :messages="room?.messages"
      @submit="sendMessage"
      @show-room="fetchRoom"
    />
  </AppLayout>
</template>
