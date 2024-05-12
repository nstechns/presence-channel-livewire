<?php

use App\Events\MessageSent;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public array $messages = [];
    public string $message = '';

    public int $userCount = 0;

    public string $typingMessage = '';

    public function addMessage()
    {
        MessageSent::dispatch(auth()->user()->name, $this->message);
        $this->reset('message');
        $this->dispatch('typing', message: '');
    }

    #[On('echo-presence:chats,MessageSent')]
    public function onMessageSent($event)
    {
        //dd($event);
        $this->messages[] = $event;
    }

    #[On('echo-presence:chats,here')]
    public function onUserHere($event)
    {
        $this->userCount = count($event);

        $this->messages[] = [
            'type'=> 'system',
            'name' => auth()->user()->name,
            'text' => 'Welcome, '.auth()->user()->name.' to the chat room!'
        ];
    }

    #[On('echo-presence:chats,joining')]
    public function onUserJoining($event)
    {
        $this->userCount++;

        $this->messages[] = [
            'type'=> 'system',
            'name' => $event['name'],
            'text' => $event['name'].' has joined the chat room.'
        ];
    }

    #[On('echo-presence:chats,leaving')]
    public function onUserLeaving($event)
    {
        $this->userCount--;

        $this->messages[] = [
            'type'=> 'system',
            'name' => $event['name'],
            'text' => $event['name'].' has left the chat room.'
        ];
    }

    public function updatedMessage($value)
    {
        $newTypingMessage = strlen($value)>0?auth()->user()->name.' is typing...':'';
        $this->dispatch('typing', message: $newTypingMessage);
    }

}; ?>

<div x-data="{ open: true }" >
    <div :class="{'-translate-y-0': open, 'translate-y-full': !open}" class="fixed transition-all duration-300 transform bottom-10 right-12 h-60 w-80">
        <div class="mb-2">
            <button @click="open = !open" type="button" :class="{ 'text-indigo-600 dark:text-white hover:bg-red-400': open }" class="w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-400 dark:bg-indigo-600 dark:hover:bg-indigo-400">
                Chat <span class="text-gray-300">{{ $userCount }} users is online</span>

                <svg x-show="!open" class="ms-auto block size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"></path>
                </svg>

                <svg x-show="open" class="ms-auto block size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
            </button>
        </div>
        <div class="w-full h-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded overflow-auto flex flex-col px-2 py-4">
            <div x-ref="chatBox" class="flex-1 p-4 text-sm flex flex-col gap-y-1">
                @foreach($messages as $msg)
                    @if($msg['type'] === 'system')
                    <div><span class="italic text-gray-500 dark:text-white">{{ $msg['text'] }}</span></div>
                    @else
                    <div><span class="text-indigo-600">{{ $msg['name'] }}:</span> <span class="dark:text-white">{{ $msg['text'] }}</span></div>
                    @endif
                @endforeach
            </div>

            @if(strlen($typingMessage))
                <div class="italic text-gray-500"><span class="px-4">{{ $typingMessage }}</span></div>
            @endif
            <div>
                <form wire:submit.prevent="addMessage" class="flex gap-2">
                    <x-text-input wire:model.live="message" x-ref="messageInput" name="message" id="message" class="block w-full" />
                    <x-primary-button>
                        Send
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('typing', (message) => {
        window.Echo.private('chats')
            .whisper('typing', {data: message})
    });

    window.Echo.private('chats').listenForWhisper('typing', (e) => {
        $wire.set('typingMessage', e.data.message);

    })
</script>
@endscript
