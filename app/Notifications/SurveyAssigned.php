<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Survey;
use App\Models\User;

class SurveyAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $survey;
    protected $assignedBy;

    public function __construct(Survey $survey, User $assignedBy)
    {
        $this->survey = $survey;
        $this->assignedBy = $assignedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tugas Survey Baru')
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda ditugaskan untuk melakukan survey lapangan pada proyek: {$this->survey->project->name}")
            ->line("Tanggal Survey: {$this->survey->scheduled_date->format('d M Y, H:i')}")
            ->line("Lokasi: {$this->survey->project->location}")
            ->action('Lihat Tugas Survey', route('surveys.show', $this->survey->id))
            ->line('Harap selesaikan survey dan upload hasil survey melalui aplikasi sesuai jadwal.');
    }

    public function toArray($notifiable)
    {
        return [
            'survey_id' => $this->survey->id,
            'project_id' => $this->survey->project->id,
            'project_name' => $this->survey->project->name,
            'scheduled_date' => $this->survey->scheduled_date->format('d M Y, H:i'),
            'location' => $this->survey->project->location,
            'assigned_by' => $this->assignedBy->name,
            'message' => "Anda ditugaskan survey baru pada proyek {$this->survey->project->name}"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'survey_assigned',
            'survey_id' => $this->survey->id,
            'project_name' => $this->survey->project->name,
            'scheduled_date' => $this->survey->scheduled_date->format('d M Y, H:i'),
            'location' => $this->survey->project->location,
            'message' => "Survey lapangan baru untuk proyek {$this->survey->project->name} pada {$this->survey->scheduled_date->format('d M Y, H:i')}",
            'time' => now()->diffForHumans(),
            'icon' => 'bi-clipboard-check',
            'color' => 'success'
        ]);
    }
}