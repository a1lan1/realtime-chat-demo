<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { LoaderCircle } from 'lucide-vue-next'

interface Props {
    modelValue?: string;
    loading?: boolean;
    disabled?: boolean;
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue', 'submit'])

const contentValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})
</script>

<template>
  <form
    class="flex w-full gap-2"
    @submit.prevent="$emit('submit')"
  >
    <Input
      v-model="contentValue"
      placeholder="Type a message..."
      autofocus
      :disabled="disabled"
      class="flex-1"
    />
    <Button
      type="submit"
      :disabled="disabled || loading"
    >
      <LoaderCircle
        v-if="loading"
        class="h-4 w-4 animate-spin"
      />

      Send
    </Button>
  </form>
</template>
