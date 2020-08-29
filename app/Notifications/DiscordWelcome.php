<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use Auth;

class DiscordWelcome extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * @param $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return DiscordMessage::create("Hi " . Auth::user()->fullName('F') . ", welcome to the Winnipeg FIR Discord server!\n\nWelcome to the Winnipeg FIR’s official discord server. This is mainly used for interaction within the FIR as well as communicating details such as events, OTS’, and important updates. Our rules are as follows:\n```\n1. Our server follows all VATSIM, VATCAN and local FIR policies. This means any user found to be breaking these policies will be dealt with accordingly.\n2. Usernames will be assigned by the FIR bot based on your display name. This is in accordance with our Privacy Policy found at https://winnipegfir.ca/privacy-policy/.\n3. NSFW content shall not be posted.\n4. Harassment of any member will not be tolerated. This includes but not limited to: racism, sexism, hate speech.\n```\n\nFailure to comply with this rules can result in your removal from the server.\n\nCommon sense is best practice to use on our server like you should with any server.\n\nIf you have any questions, please @FIR Staff. They should be able to help you out real fast.\n\nThanks for joining! We look forward to working with you.");
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            //
        ];
    }
}
