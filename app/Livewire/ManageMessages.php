<?php

namespace App\Livewire;

use App\Models\Message;
use Livewire\Component;

class ManageMessages extends Component
{
    public string $content = '';

    public function sendMessage()
    {
        $this->validate([
            'content' => 'required|max:1000',
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
        ]);

        $this->reset('content');
    }

    public function render()
    {
        return view('livewire.manage-messages', [
            'messages' => Message::with('user')->oldest()->get(),
        ]);
    }
}
