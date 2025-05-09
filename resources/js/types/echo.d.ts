import 'laravel-echo'

declare module 'laravel-echo' {
    interface Echo {
        channel(name: string): Channel;
        private(name: string): Channel;
        notification(callback: CallableFunction): any;
        presence(channel: string): any;
        join(name: string): PresenceChannel;
        leave(name: string): void;
        listen(eventName: string, callback: (e: any) => void): void;
        socketId(): string;
    }

    interface Channel {
        listen(event: string, callback: (e: any) => void): Channel;
    }

    interface PresenceChannel extends Channel {
        here(callback: (users: any[]) => void): PresenceChannel;
        joining(callback: (user: any) => void): PresenceChannel;
        leaving(callback: (user: any) => void): PresenceChannel;
    }
}
