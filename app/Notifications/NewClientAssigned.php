<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewClientAssigned extends Notification
{
    use Queueable;

    protected $client;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     *
     * @param Client $client
     * @param User $assignedBy
     */
    public function __construct(Client $client, User $assignedBy)
    {
        $this->client = $client;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'assigned_by' => $this->assignedBy->name,
            'message' => "Client baru '{$this->client->name}' telah ditambahkan"
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->client->id,
            'type' => 'client_assigned',
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'message' => "Client baru '{$this->client->name}' telah ditambahkan",
            'time' => now()->diffForHumans()
        ]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Client baru '{$this->client->name}' telah ditambahkan")
            ->line("Client baru '{$this->client->name}' telah ditambahkan oleh {$this->assignedBy->name}.")
            ->action('View Client', url('/clients/'.$this->client->id))
            ->line('Thank you for using our application!');
    }
}
