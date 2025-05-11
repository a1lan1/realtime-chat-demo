import type { User } from '@/types'

export interface Room {
    id: number;
    name: string;
    description?: string;
    messages_count?: number;
    messages?: Message[];
    last_message?: Message;
    created_at?: string;
    updated_at?: string;
}

export interface Message {
    id: number;
    content: string;
    user_id: number;
    room_id: number;
    user: User;
    room?: Room;
    created_at: string;
    updated_at?: string;
}

export interface ChatForm {
    content: string;
    room_id?: number;
}

export interface ChatState {
    loading: boolean;
    storing: boolean;
    messages: Message[];
    rooms: Room[];
    room: Room|null;
    form: ChatForm;
}
