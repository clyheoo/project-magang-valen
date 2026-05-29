<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\SuratMasuk;

class SuratMasukUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $suratMasuk;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(SuratMasuk $suratMasuk, $action = 'updated')
    {
        $this->suratMasuk = $suratMasuk;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('surat-masuk'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'surat-masuk.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->suratMasuk->id,
            'status' => $this->suratMasuk->status,
            'action' => $this->action,
            'belum_ditindak_count' => \App\Models\SuratMasuk::where('status', 'baru')->count(),
        ];
    }
}
