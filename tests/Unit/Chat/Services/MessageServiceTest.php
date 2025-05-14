<?php

use App\Contracts\MessageRepositoryInterface;
use App\Contracts\MessageServiceInterface;
use App\DTO\MessageData;
use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('MessageService', function () {
    it('implements the MessageServiceInterface', function () {
        $messageRepositoryMock = mock(MessageRepositoryInterface::class);
        $messageService = new MessageService($messageRepositoryMock);

        expect($messageService)->toBeInstanceOf(MessageServiceInterface::class);
    });

    describe('getLastMessages', function () {
        it('returns cached messages if they exist', function () {
            $roomId = 1;
            $cachedMessages = Message::factory(2)->make(['room_id' => $roomId]);

            Cache::shouldReceive('remember')
                ->once()
                ->with("room:{$roomId}:messages", 3600, Mockery::type(Closure::class))
                ->andReturn($cachedMessages);

            $messageRepositoryMock = mock(MessageRepositoryInterface::class);
            $messageService = new MessageService($messageRepositoryMock);
            $messages = $messageService->getLastMessages($roomId);

            expect($messages)->toBe($cachedMessages);
            $messageRepositoryMock->shouldNotHaveReceived('getLastMessages');
        });

        it('retrieves messages from the repository and caches them if they do not exist in cache', function () {
            $roomId = 1;
            $roomMessages = Message::factory(3)->make(['room_id' => $roomId]);
            $messageRepositoryMock = mock(MessageRepositoryInterface::class);
            $messageService = new MessageService($messageRepositoryMock);

            Cache::shouldReceive('get')
                ->with("room:{$roomId}:messages")
                ->andReturn(null);

            Cache::shouldReceive('remember')
                ->once()
                ->with("room:{$roomId}:messages", 3600, Mockery::type(Closure::class))
                ->andReturnUsing(fn ($key, $ttl, $closure) => $closure());

            $messageRepositoryMock->shouldReceive('getLastMessages')
                ->once()
                ->with($roomId)
                ->andReturn($roomMessages);

            $messages = $messageService->getLastMessages($roomId);

            expect($messages)->toBe($roomMessages);
        });
    });

    describe('sendMessage', function () {
        it('creates a new message using the repository, loads the user relation, dispatches the NewMessage event, and returns the message', function () {
            Event::fake();

            $roomId = 1;
            $userId = 2;
            $content = 'Hello World';
            $mockUser = User::factory()->make(['id' => $userId]);

            $createdMessage = Message::factory()->make([
                'room_id' => $roomId,
                'user_id' => $userId,
                'content' => $content,
            ]);
            $createdMessage->setRelation('user', $mockUser);

            $messageRepositoryMock = mock(MessageRepositoryInterface::class);
            $messageRepositoryMock->shouldReceive('createMessage')
                ->once()
                ->with(Mockery::on(function (MessageData $data) use ($roomId, $userId, $content) {
                    return $data->room_id === $roomId &&
                        $data->user_id === $userId &&
                        $data->content === $content;
                }))
                ->andReturn($createdMessage);

            $messageService = new MessageService($messageRepositoryMock);
            $message = $messageService->sendMessage($roomId, $userId, $content);

            $message->setRelation('user', $mockUser);

            expect($message)->toBe($createdMessage)
                ->and($message->relationLoaded('user'))->toBeTrue()
                ->and($message->user->id)->toBe($userId);

            Event::assertDispatched(NewMessage::class, function (NewMessage $event) use ($createdMessage) {
                return $event->message->is($createdMessage);
            });
        });
    });
});
