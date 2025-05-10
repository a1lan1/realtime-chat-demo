<script setup lang="ts">
import { ref, watch, computed, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useEcho from '@/composables/useEcho'
import RoomList from '@/components/chat/RoomList.vue'
import MessageList from '@/components/chat/MessageList.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import type { Message, Room } from '@/types/chat'

const props = defineProps<{
    rooms: Room[];
    activeRoom: Room|null;
}>()

const { listen } = useEcho()

const messages = ref<Message[]>(props.activeRoom?.messages || [])

const form = useForm({
  content: '',
  room_id: props.activeRoom?.id
})

const title = computed(() => {
  return props.rooms.find(({ id }) => id === props.activeRoom?.id)?.name || 'Chat'
})

const scrollBottom = () => {
  nextTick(() => {
    const scrollArea = document.getElementById('messages')
    scrollArea?.scrollTo(0, scrollArea.scrollHeight)
  })
}

const sendMessage = () => {
  if (!form.content.trim() || !props.activeRoom) {
    return
  }

  form.processing = true

  form.post(route('chat.message'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => form.reset('content'),
    onError: (errors) => console.error('Message send error:', errors),
    onFinish: () => (form.processing = false)
  })
}

watch(
  () => messages.value.length,
  () => scrollBottom(),
  { immediate: true }
)

listen(`room.${form.room_id}`, '.message.new', (data: { message: Message }) => {
  messages.value.push(data.message)
})
</script>

<template>
  <div class="flex h-[calc(100vh-60px)] rounded-lg border">
    <div class="w-64 space-y-2 overflow-auto border-r p-4">
      <h3 class="mb-2 text-lg font-semibold">
        Rooms
      </h3>

      <RoomList
        :rooms="rooms"
        :active-room="activeRoom?.id"
      />
    </div>

    <div class="flex flex-1 flex-col">
      <Card class="flex h-full flex-col">
        <CardHeader class="border-b">
          <CardTitle>
            {{ title }}
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

        <CardFooter class="border-t p-4">
          <form
            class="flex w-full gap-2"
            @submit.prevent="sendMessage"
          >
            <Input
              v-model="form.content"
              placeholder="Type a message..."
              autofocus
              :disabled="!activeRoom"
              class="flex-1"
            />
            <Button
              type="submit"
              :disabled="form.processing || !activeRoom"
            >
              Send
            </Button>
          </form>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
