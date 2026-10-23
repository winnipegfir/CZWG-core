<?php

namespace App\Notifications;

use App\Models\Settings\CoreSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNewUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $settings = CoreSettings::find(1);

        return ! $settings || $settings->welcome_email_enabled !== false ? ['mail'] : [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $settings = CoreSettings::find(1);
        $subject = $settings->welcome_email_subject ?? 'Welcome to Winnipeg, {first_name}!';
        $body = $settings->welcome_email_body ?? "Welcome to the Winnipeg FIR, we're very excited that you're here!\n\nWelcome to Winnipeg! This is the home to all things Winnipeg - from controller files, to roster info, to training, contact info and more. Thanks for stopping by!";
        $signature = $settings->welcome_email_signature ?? "Winnipeg FIR Staff";

        $replacements = [
            '{first_name}' => $this->user->fullName('F'),
            '{full_name}' => $this->user->fullName('FLC'),
            '{cid}' => (string) $this->user->id,
        ];

        return (new MailMessage)->view(
            'emails.welcomenewuser', [
                'user' => $this->user,
                'welcomeBody' => strtr($body, $replacements),
                'welcomeSignature' => strtr($signature, $replacements),
            ]
        )->subject(strtr($subject, $replacements))->from('no-reply@winnipegfir.ca', 'Winnipeg FIR');
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
