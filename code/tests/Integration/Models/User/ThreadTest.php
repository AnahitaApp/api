<?php
declare(strict_types=1);

namespace Tests\Integration\Models\User;

use App\Models\User\Message;
use App\Models\User\Thread;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ThreadTest
 * @package Tests\Integration\Models\User
 */
class ThreadTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testLastMessage()
    {
        $messageDispatcher = mock(Dispatcher::class);
        Message::setEventDispatcher($messageDispatcher);
        $messageDispatcher->shouldReceive('dispatch');
        $messageDispatcher->shouldReceive('until');

        /** @var Thread $thread */
        $thread = factory(Thread::class)->create();
        factory(Message::class)->create([
            'created_at' => '2018-10-10 12:00:00',
            'thread_id' => $thread->id,
        ]);
        $newMessage = factory(Message::class)->create([
            'created_at' => '2018-10-11 12:00:00',
            'thread_id' => $thread->id,
        ]);

        $this->assertEquals($thread->last_message->id, $newMessage->id);
    }
}