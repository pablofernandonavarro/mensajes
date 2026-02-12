<?php

namespace App\Livewire;

use App\Events\MessageSend;
use App\Models\Message;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;

class ManageMessages extends Component
{
    public string $content = '';

    public function sendMessage()
    {
        $this->validate([
            'content' => 'required|min:5|max:1000',
        ], [
            'content.required' => 'El mensaje no puede estar vacío.',
            'content.min' => 'El mensaje debe tener al menos :min caracteres.',
            'content.max' => 'El mensaje no puede superar los :max caracteres.',
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
        ]);

        $this->reset('content');

        // Disparar evento para hacer scroll
        $this->dispatch('message-sent');

        // Ya no necesitamos broadcast con polling
        // broadcast(new MessageSend());
    }

    public function formatWhatsAppDate(Carbon $date): string
    {
        $now = Carbon::now();

        if ($date->isToday()) {
            return $date->format('H:i');
        }

        if ($date->isYesterday()) {
            return 'Ayer ' . $date->format('H:i');
        }

        if ($date->isSameWeek($now)) {
            return $date->isoFormat('ddd H:mm');
        }

        if ($date->isSameYear($now)) {
            return $date->format('d/m H:i');
        }

        return $date->format('d/m/Y H:i');
    }

    public function render()
    {
        return view('livewire.manage-messages', [
            'messages' => Message::with('user')->oldest()->get(),
        ]);
    }
}
