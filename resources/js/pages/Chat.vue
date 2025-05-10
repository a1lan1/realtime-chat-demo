<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ChatRoom from '@/components/chat/ChatRoom.vue'
import type { BreadcrumbItem } from '@/types'
import type { Room } from '@/types/chat'

defineProps<{
    rooms: Room[];
    room: Room|null;
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Chat', href: '/chat' }
]
</script>

<template>
  <Head title="Chat" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <ChatRoom
      :rooms="rooms"
      :active-room="room"
    />
  </AppLayout>
</template>
