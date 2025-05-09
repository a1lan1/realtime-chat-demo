<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import type { Room } from '@/types/chat'

defineProps<{
    rooms: Room[]
    activeRoom?: number
}>()

const navigateToRoom = (roomId: number) => {
  router.visit(route('chat', { roomId }))
}
</script>

<template>
  <div
    v-for="room in rooms"
    :key="room.id"
    class="p-3 rounded-lg cursor-pointer transition-colors"
    :class="{
      'bg-gray-100 dark:bg-gray-800': activeRoom === room.id,
      'hover:bg-gray-50 dark:hover:bg-gray-900': activeRoom !== room.id
    }"
    @click="navigateToRoom(room.id)"
  >
    <div class="flex justify-between items-center">
      <div>
        <span>{{ room.name }}</span>
        <p
          v-if="room.last_message"
          class="text-xs text-muted-foreground truncate w-40"
        >
          {{ room.last_message.content }}
        </p>
      </div>

      <span
        v-if="room.messages_count"
        class="bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center"
      >
        {{ room.messages_count }}
      </span>
    </div>
  </div>
</template>
