<div wire:poll.3s class="p-4">
    {{-- Lista de mensajes --}}
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-4 h-96 overflow-y-auto" id="chat-box">
        @forelse($messages as $message)
            <div class="mb-3 flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-900' }}">
                    <p class="text-xs font-bold mb-1">{{ $message->user->name }}</p>
                    <p class="text-sm">{{ $message->content }}</p>
                    <p class="text-xs mt-1 {{ $message->user_id === auth()->id() ? 'text-indigo-200' : 'text-gray-500' }}">
                        {{ $message->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center">No hay mensajes aún.</p>
        @endforelse
    </div>

    {{-- Formulario para enviar --}}
    <form wire:submit="sendMessage" class="flex gap-2">
        <input type="text" wire:model="content" placeholder="Escribe un mensaje..."
            class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Enviar
        </button>
    </form>

    @error('content')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
