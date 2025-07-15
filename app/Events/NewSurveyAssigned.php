<?php

namespace App\Events;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSurveyAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $survey;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Survey $survey, User $user)
    {
        $this->survey = $survey;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'survey.assigned';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->survey->id,
            'title' => $this->survey->title,
            'client_name' => $this->survey->client->name,
            'due_date' => $this->survey->due_date,
            'assigned_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
