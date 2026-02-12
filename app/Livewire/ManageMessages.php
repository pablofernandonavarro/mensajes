<?php

namespace App\Livewire;

use App\Events\MessageSend;
use App\Models\Message;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class ManageMessages extends Component
{
    use WithFileUploads;

    public string $content = '';
    public $file;

    public function sendMessage()
    {
        // Validar que haya contenido O archivo (estilo WhatsApp)
        $this->validate([
            'content' => 'required_without:file|nullable|max:1000',
            'file' => 'required_without:content|nullable|file|max:10240', // 10MB máximo
        ], [
            'content.required_without' => 'Debes escribir un mensaje o adjuntar un archivo.',
            'content.max' => 'El mensaje no puede superar los :max caracteres.',
            'file.required_without' => 'Debes escribir un mensaje o adjuntar un archivo.',
            'file.max' => 'El archivo no puede superar los 10MB.',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'content' => $this->content ?: 'Archivo adjunto',
        ];

        // Guardar archivo si existe
        if ($this->file) {
            $data['file_path'] = $this->file->store('chat-files', 'public');
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_type'] = $this->file->getClientOriginalExtension();
        }

        Message::create($data);

        // Limpiar variables
        $this->content = '';
        $this->file = null;
        $this->reset(['content', 'file']);

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
