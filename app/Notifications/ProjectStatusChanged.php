<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Project;
use App\Models\User;

class ProjectStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $previousStage;
    protected $currentStage;
    protected $changedBy;

    public function __construct(Project $project, $previousStage, $currentStage, User $changedBy)
    {
        $this->project = $project;
        $this->previousStage = $previousStage;
        $this->currentStage = $currentStage;
        $this->changedBy = $changedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_code' => $this->project->code,
            'previous_stage' => $this->previousStage,
            'current_stage' => $this->currentStage,
            'changed_by' => $this->changedBy->name,
            'message' => "Proyek {$this->project->name} berpindah dari tahap '{$this->previousStage}' ke '{$this->currentStage}'"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'project_status_changed',
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'message' => "Proyek {$this->project->name} kini berada di tahap '{$this->currentStage}'",
            'time' => now()->diffForHumans(),
            'icon' => 'bi-building',
            'color' => 'primary'
        ]);
    }
}