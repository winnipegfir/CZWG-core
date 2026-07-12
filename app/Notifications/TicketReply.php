<?php

namespace App\Notifications;

use App\Notifications\Concerns\SendsDiscordDm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReply extends Notification
{
    use Queueable, SendsDiscordDm;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $ticket, $reply)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->viaChannels($notifiable, ['mail', 'database']);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->view(
            'emails.ticketreply', ['user' => $this->user, 'ticket' => $this->ticket, 'reply' => $this->reply]
        )->subject('#'.$this->ticket->ticket_id.' | New Reply From '.$this->reply->user->fullName('FLC'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'New reply on ticket #' . $this->ticket->ticket_id,
            'body'  => $this->reply->user->fullName('FL') . ' replied to your ticket.',
            'url'   => route('tickets.viewticket', $this->ticket->id),
            'icon'  => 'fa-life-ring',
        ];
    }
}
