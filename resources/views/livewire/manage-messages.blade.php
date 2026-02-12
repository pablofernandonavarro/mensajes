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

                    @if($message->hasFile())
                        <div class="mt-2 p-2 rounded {{ $message->user_id === auth()->id() ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            @if(in_array($message->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                {{-- Mostrar imagen --}}
                                <a href="{{ $message->getFileUrl() }}" target="_blank">
                                    <img src="{{ $message->getFileUrl() }}" alt="{{ $message->file_name }}" class="max-w-full h-auto rounded">
                                </a>
                            @else
                                {{-- Mostrar enlace de descarga --}}
                                <a href="{{ $message->getFileUrl() }}" download="{{ $message->file_name }}" class="flex items-center gap-2 {{ $message->user_id === auth()->id() ? 'text-white hover:text-indigo-100' : 'text-gray-900 hover:text-gray-700' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-xs">{{ $message->file_name }}</span>
                                </a>
                            @endif
                        </div>
                    @endif

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

        {{-- Indicador de carga mientras sube el archivo --}}
        <div wire:loading wire:target="file" style="margin-bottom: 1.25rem;" class="flex items-end gap-2 justify-end">
            <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg bg-gray-300 text-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-sm">Cargando archivo...</span>
                </div>
            </div>
            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
        </div>

        {{-- Preview de archivo seleccionado (dentro del chat) --}}
        @if($file)
            <div style="margin-bottom: 1.25rem;" class="flex items-end gap-2 justify-end">
                <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg bg-indigo-500 text-white">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold">Vista previa</span>
                        <button type="button" wire:click="$set('file', null)" class="text-white hover:text-red-300 bg-red-500 rounded-full p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @php
                        $extension = strtolower($file->getClientOriginalExtension());
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                    @endphp

                    @if($isImage)
                        {{-- Vista previa de imagen --}}
                        <div class="mb-2 bg-white rounded-lg overflow-hidden">
                            @try
                                <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-full h-auto max-h-64 object-contain">
                            @catch(\Exception $e)
                                <div class="p-4 text-center">
                                    <p class="text-sm text-gray-600">No se puede previsualizar</p>
                                </div>
                            @endtry
                        </div>
                        <p class="text-xs text-indigo-100 mb-1">{{ $file->getClientOriginalName() }}</p>
                    @else
                        {{-- Vista previa de archivo no imagen --}}
                        <div class="mb-2 p-4 bg-indigo-600 rounded-lg">
                            <div class="flex items-center gap-3">
                                @if($extension === 'pdf')
                                    <svg class="w-10 h-10 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                        <text x="7" y="16" font-size="6" fill="white" font-weight="bold">PDF</text>
                                    </svg>
                                @elseif(in_array($extension, ['doc', 'docx']))
                                    <svg class="w-10 h-10 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                    </svg>
                                @elseif(in_array($extension, ['zip', 'rar']))
                                    <svg class="w-10 h-10 flex-shrink-0 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                    </svg>
                                @else
                                    <svg class="w-10 h-10 flex-shrink-0" fill="white" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM6 20V4h6v6h6v10H6z"/>
                                    </svg>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-indigo-200">{{ strtoupper($extension) }} • {{ number_format($file->getSize() / 1024, 2) }} KB</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="text-xs text-indigo-200">📎 Listo para enviar</p>
                </div>
                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
            </div>
        @endif
    </div>

    {{-- Formulario para enviar (estilo WhatsApp) --}}
    <form wire:submit="sendMessage" @submit="scrollToBottom()">
        <div class="flex items-center gap-3 bg-white rounded-full shadow-lg border border-gray-300 px-4 py-3">
            {{-- Botón de adjuntar (clip) --}}
            <label for="file-upload" class="flex-shrink-0 cursor-pointer">
                <svg class="w-6 h-6 text-gray-600 hover:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
            </label>
            <input id="file-upload" type="file" wire:model="file" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar" />

            {{-- Input de texto --}}
            <input type="text" wire:model="content" placeholder="Escribe un mensaje..."
                class="flex-1 px-2 py-1 border-0 focus:ring-0 focus:outline-none bg-transparent text-base text-gray-900 placeholder-gray-500 @error('content') text-red-500 @enderror" />

            {{-- Botón de enviar (estilo WhatsApp) CON FONDO VERDE VISIBLE --}}
            <button type="submit" style="background-color: #25D366 !important; min-width: 48px; min-height: 48px;" class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-full hover:bg-green-600 active:bg-green-700 transition-all duration-200 shadow-lg">
                <span style="color: white; font-size: 24px; font-weight: bold;">▶</span>
            </button>
        </div>

        @error('content')
            <p class="text-red-500 text-xs mt-2 ml-3">{{ $message }}</p>
        @enderror
        @error('file')
            <p class="text-red-500 text-xs mt-2 ml-3">{{ $message }}</p>
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
