<?php

use App\Events\MessageSent;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public array $messages = [];
    public string $message = '';

    public function addMessage()
    {
        MessageSent::dispatch(auth()->user()->name, $this->message);
        $this->reset('message');
    }

    #[On('echo-private:chats,MessageSent')]
    public function onMessageSent($event)
    {
        //dd($event);
        $this->messages[] = $event;
    }
}; ?>

<div x-data="{ open: true }" >
    <div :class="{'-translate-y-0': open, 'translate-y-full': !open}" class="fixed transition-all duration-300 transform bottom-10 right-12 h-60 w-80">
        <div class="mb-2">
            <button @click="open = !open" type="button" :class="{ 'text-indigo-600 dark:text-white hover:bg-red-400': open }" class="w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-400 dark:bg-indigo-600 dark:hover:bg-indigo-400">
                Chat

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
                    <div><span class="text-indigo-600">{{ $msg['name'] }}:</span> <span class="dark:text-white">{{ $msg['text'] }}</span></div>
                @endforeach
            </div>
            <div>
                <form wire:submit.prevent="addMessage" class="flex gap-2">
                    <x-text-input wire:model="message" x-ref="messageInput" name="message" id="message" class="block w-full" />
                    <x-primary-button>
                        Send
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</div>

