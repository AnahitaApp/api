<?php
declare(strict_types=1);

namespace App\Listeners\Message;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\Message\MessageCreatedEvent;
use App\Events\Message\MessageSentEvent;
use App\Mail\MessageMailer;
use App\Models\User\Message;
use App\Models\User\User;
use Benwilkins\FCM\FcmChannel;
use Benwilkins\FCM\FcmMessage;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class MessageCreatedListener
 * @package App\Listeners\Message
 */
class MessageCreatedListener implements ShouldQueue
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var MessageRepositoryContract
     */
    private $messageRepository;

    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * @var Repository
     */
    private $config;

    /**
     * MessageCreatedListener constructor.
     * @param Mailer $mailer
     * @param Client $client
     * @param MessageRepositoryContract $messageRepository
     * @param Dispatcher $events
     * @param Repository $config
     */
    public function __construct(Mailer $mailer, Client $client, MessageRepositoryContract $messageRepository,
                                Dispatcher $events, Repository $config)
    {
        $this->mailer = $mailer;
        $this->client = $client;
        $this->messageRepository = $messageRepository;
        $this->events = $events;
        $this->config = $config;
    }

    /**
     * Schedules the message to be sent
     *
     * @param MessageCreatedEvent $event
     * @throws Exception
     */
    public function handle(MessageCreatedEvent $event)
    {
        $message = $event->getMessage();

        $this->messageRepository->update($message, [
            'scheduled_at' => Carbon::now(),
        ]);

        $via = $message->via ?? [Message::VIA_EMAIL];

        if (in_array(Message::VIA_PUSH_NOTIFICATION, $via)) {

            try {
                $message->fresh();

                if (!$message->sent_at) {

                    if ($message->to) {
                        $this->sentPushNotification($message->to, $message);
                    }
                    if ($message->thread) {
                        foreach ($message->thread->users as $user) {
                            if ($user->id != $message->from_id) {
                                $this->sentPushNotification($user, $message);
                            }
                        }
                    }
                }

                $this->messageRepository->update($message, [
                    'sent_at' => Carbon::now(),
                ]);

            } catch (Exception $exception) {}
            $this->events->dispatch(new MessageSentEvent($message));
        }
        if (in_array(Message::VIA_EMAIL, $via) && $message->to) {
            $this->mailer->send(new MessageMailer($message));
        }
    }

    /**
     * @param User $user
     * @param Message $message
     * @throws Exception
     */
    public function sentPushNotification(User $user, Message $message)
    {
        if ($user->push_notification_key && $user->receive_push_notifications) {
            $pushNotification = new FcmMessage();
            $pushNotification->to($user->push_notification_key);

            $pushNotification->priority(FcmMessage::PRIORITY_HIGH);
            $pushNotification->contentAvailable(true);

            $notificationData = $message->data;
            $pushNotification->content($notificationData);

            if ($message->action) {
                $pushNotification->data([
                    'action' => $message->action,
                ]);
            }

            $this->client->post(FcmChannel::API_URI, [
                'headers' => [
                    'Authorization' => 'key=' . $this->config->get('services.fcm.key'),
                    'Content-Type'  => 'application/json',
                ],
                'body' => $pushNotification->formatData(),
            ]);
        }
    }
}