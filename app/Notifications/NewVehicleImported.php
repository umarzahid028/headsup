<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVehicleImported extends Notification
{
    use Queueable;

    /**
     * The vehicle instance.
     *
     * @var Vehicle
     */
    protected $vehicle;

    /**
     * The file name.
     *
     * @var string
     */
    protected $fileName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vehicle $vehicle, string $fileName)
    {
        $this->vehicle = $vehicle;
        $this->fileName = $fileName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $vehicleInfo = "{$this->vehicle->year} {$this->vehicle->make} {$this->vehicle->model}";
        
        return (new MailMessage)
            ->subject("New Vehicle Imported - {$vehicleInfo}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new vehicle has been imported from the file: {$this->fileName}")
            ->line("Vehicle Details:")
            ->line("Stock #: {$this->vehicle->stock_number}")
            ->line("VIN: {$this->vehicle->vin}")
            ->line("Vehicle: {$vehicleInfo}")
            ->line("Color: {$this->vehicle->exterior_color}")
            ->action('View Vehicle Details', url("/vehicles/{$this->vehicle->id}"))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'stock_number' => $this->vehicle->stock_number,
            'vin' => $this->vehicle->vin,
            'vehicle_info' => "{$this->vehicle->year} {$this->vehicle->make} {$this->vehicle->model}",
            'file_name' => $this->fileName,
            'message' => "New vehicle imported from {$this->fileName}",
            'type' => 'vehicle_imported'
        ];
    }
}
