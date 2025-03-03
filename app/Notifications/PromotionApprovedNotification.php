<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PromotionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $vendor;
    public $promotion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($vendor, $promotion)
    {
        $this->vendor = $vendor;
        $this->promotion = $promotion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; 
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
            'title' => 'Promotion Approved',
            'message' => "Your promotion '{$this->promotion->name}' has been approved.",
            'promotion_id' => $this->promotion->id,
            'type' => 'approved',
        ];
    }
}
