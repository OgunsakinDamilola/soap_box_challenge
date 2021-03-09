<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceInviteNotification extends Notification
{
    use Queueable;

    public $user, $workspace;

    /**
     * Create a new notification instance.
     *
     * @param $workspaceUser
     */
    public function __construct($workspaceUser)
    {
        $this->user = $workspaceUser->user;
        $this->workspace = $workspaceUser->workspace;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->workspace->name.' Invitation')
            ->greeting('Hi '. $this->user->first_name. ',')
            ->line('You have been invited to join this workspace, click on this link to become a member')
            ->action('Join '.$this->workspace->name, url('/api/workspaces/accept-invite?userId='. $this->user->id.'&workspaceId='.$this->workspace->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
