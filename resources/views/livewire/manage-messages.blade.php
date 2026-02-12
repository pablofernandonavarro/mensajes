<div class="p-4" wire:poll.3s x-data="{ scrollToBottom() { $nextTick(() => { this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight; }); } }" x-init="scrollToBottom()" @message-sent.window="scrollToBottom()">
    {{-- Lista de mensajes --}}
    <div x-ref="chatBox" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-4 h-96 overflow-y-auto" id="chat-box">
        @forelse($messages as $message)
            <div style="margin-bottom: 1.25rem;" class="flex items-end gap-2 {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                @if($message->user_id !== auth()->id())
                    <img src="{{ $message->user->profile_photo_url }}" alt="{{ $message->user->name }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
                @endif
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-900' }}">
                    <p class="text-xs font-bold mb-1">{{ $message->user->name }}</p>
                    <p class="text-sm">{{ $message->content }}</p>
                    <p class="text-xs mt-1 {{ $message->user_id === auth()->id() ? 'text-indigo-200' : 'text-gray-500' }}">
                        {{ $this->formatWhatsAppDate($message->created_at) }}
                    </p>
                </div>
                @if($message->user_id === auth()->id())
                    <img src="{{ $message->user->profile_photo_url }}" alt="{{ $message->user->name }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-center">No hay mensajes aún.</p>
        @endforelse
    </div>

    {{-- Formulario para enviar --}}
    <form wire:submit="sendMessage" @submit="scrollToBottom()">
        <div class="flex gap-2">
            <input type="text" wire:model="content" placeholder="Escribe un mensaje..."
                class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('content') border-red-500 @enderror" />
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Enviar
            </button>
        </div>
        @error('content')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    </form>
</div>

@script
<script>
    // Scroll automático después de actualizaciones de Livewire
    $wire.on('message-sent', () => {
        setTimeout(() => {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }, 100);
    });

    // Scroll después del polling
    Livewire.hook('morph.updated', ({ el, component }) => {
        setTimeout(() => {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }, 100);
    });
</script>
@endscript
