<script setup lang="ts">
import { watch, computed, nextTick } from 'vue'
import RoomList from '@/components/chat/RoomList.vue'
import MessageList from '@/components/chat/MessageList.vue'
import ChatFormInput from '@/components/chat/ChatFormInput.vue'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import type { Message, Room } from '@/types/chat'

const props = defineProps<{
    modelValue?: string;
    loading: boolean;
    rooms: Room[];
    activeRoom: Room|null;
    messages?: Message[];
}>()
const emit = defineEmits(['update:modelValue', 'submit', 'showRoom'])

const chatInput = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const scrollBottom = () => {
  nextTick(() => {
    const scrollArea = document.getElementById('messages')
    scrollArea?.scrollTo(0, scrollArea.scrollHeight)
  })
}

watch(
  () => props.messages?.length,
  () => scrollBottom(),
  { immediate: true }
)
</script>

<template>
  <div class="flex h-[calc(100vh-60px)]">
    <div class="w-80 space-y-2 overflow-auto border-r p-4">
      <h3 class="mb-2 text-lg font-semibold">
        Rooms
      </h3>

      <RoomList
        :rooms="rooms"
        :active-room="activeRoom?.id"
        @show-room="$emit('showRoom', $event)"
      />
    </div>

    <div class="flex flex-1 flex-col">
      <Card class="flex h-full flex-col border-0">
        <CardHeader class="border-b [.border-b]:pb-3">
          <CardTitle>
            {{ activeRoom?.name || 'Chat' }}
          </CardTitle>
        </CardHeader>

        <CardContent class="flex-1 overflow-auto p-0">
          <div
            id="messages"
            class="h-full space-y-4 overflow-y-auto p-4"
          >
            <MessageList :messages="messages" />
          </div>
        </CardContent>

        <CardFooter class="border-t px-4">
          <ChatFormInput
            v-model="chatInput"
            :loading="loading"
            :disabled="!activeRoom"
            @submit="$emit('submit')"
          />
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
