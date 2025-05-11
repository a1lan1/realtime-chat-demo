import { defineStore } from 'pinia'
import type { ChatForm, ChatState } from '@/types/chat'

const defaultForm: ChatForm = {
  content: '',
  room_id: undefined
}

export const useChatStore = defineStore('chat', {
  state: (): ChatState => ({
    loading: false,
    storing: false,
    messages: [],
    rooms: [],
    room: null,
    form: { ...defaultForm }
  }),

  actions: {
    async fetchRoomList() {
      try {
        this.loading = true
        const { data } = await this.$axios.get(route('rooms.index'))
        this.rooms = data
      } catch (error: any) {
        throw error
      } finally {
        this.loading = false
      }
    },
    async fetchRoom(roomId: number) {
      try {
        this.loading = true
        const { data } = await this.$axios.get(route('rooms.show', { roomId }))
        this.room = data
      } catch (error: any) {
        throw error
      } finally {
        this.loading = false
      }
    },
    async sendMessage() {
      if (!this.form.content.trim() || !this.room) {
        return
      }

      try {
        this.storing = true
        this.form.room_id = this.room.id

        const { data } = await this.$axios.post(route('messages.store'), this.form)
        this.room.messages?.push(data)
        this.form.content = ''
      } catch (error: any) {
        throw error
      } finally {
        this.storing = false
      }
    },
    resetForm() {
      this.form = { ...defaultForm }
    }
  }
})
