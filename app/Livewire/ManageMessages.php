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
        // Validar que haya contenido O archivo
        $this->validate([
            'content' => 'required_without:file|nullable|min:5|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB máximo
        ], [
            'content.required_without' => 'Debes escribir un mensaje o adjuntar un archivo.',
            'content.min' => 'El mensaje debe tener al menos :min caracteres.',
            'content.max' => 'El mensaje no puede superar los :max caracteres.',
            'file.max' => 'El archivo no puede superar los 10MB.',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'content' => $this->content ?: 'Archivo adjunto',
        ];

        // Solo agregar campos de archivo si las columnas existen (después de migración)
        if ($this->file && \Schema::hasColumn('messages', 'file_path')) {
            $data['file_path'] = $this->file->store('chat-files', 'public');
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_type'] = $this->file->getClientOriginalExtension();
        }

        Message::create($data);

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
