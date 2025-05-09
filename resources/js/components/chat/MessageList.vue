<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { formatDate } from '@/utils/date'
import type { Message } from '@/types/chat'
import type { SharedData, User } from '@/types'

defineProps<{
    messages?: Message[]
}>()

const user = usePage<SharedData>().props.auth.user as User
</script>

<template>
  <div
    v-for="message in messages"
    :key="message.id"
    class="flex gap-3"
    :class="{ 'justify-end': message.user_id === user.id }"
  >
    <div
      class="max-w-[75%] p-3 rounded-lg"
      :class="{
        'bg-primary text-primary-foreground': message.user_id === user.id,
        'bg-gray-100 dark:bg-gray-800': message.user_id !== user.id
      }"
    >
      <div class="text-xs text-muted-foreground mb-1">
        {{ message.user?.name }} • {{ formatDate(message.created_at) }}
      </div>
      <div>{{ message.content }}</div>
    </div>
  </div>
</template>
