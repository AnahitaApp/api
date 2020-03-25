<?php
declare(strict_types=1);

namespace App\Mail;

use App\Events\Message\MessageSentEvent;
use App\Models\User\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotificationMailer
 * @package App\Mail
 */
class MessageMailer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Message
     */
    private $message;

    /**
     * NotificationMailer constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->chain([new MessageSentEvent($message)]);
    }

    /**
     * Builds the email
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->message->subject)
            ->to($this->message->email, $this->message->to->name)
            ->from('thehaeckelsociety@gmail.com', 'Project Athenia')
            ->bcc('thehaeckelsociety@gmail.com', 'Project Athenia')
            ->view('mailers.' . $this->message->template, $this->message->data);
    }
}