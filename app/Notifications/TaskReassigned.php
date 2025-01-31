<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Task;

class TaskReassigned extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Notify via database and email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Task Reassigned')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The task "' . $this->task->title . '" has been reassigned to someone else.')
            ->action('View Task', route('tasks.show', $this->task->id))
            ->line('If this is an error, please contact your administrator.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'The task "' . $this->task->title . '" was reassigned to someone else.',
            'task_id' => $this->task->id,
        ];
    }
}
