import { format } from 'date-fns'

export const formatDate = (date: string, type: string = 'dd.MM.yyyy HH:mm'): string|null => {
  if (!date) {
    return null
  }

  return format(new Date(date), type)
}
