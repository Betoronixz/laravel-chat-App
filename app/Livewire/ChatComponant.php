<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatComponant extends Component
{
    public $user;
    public $sender_id;
    public $recever_id;
    public $message = "";
    public $messages = [];

    public function render()
    {
        return view('livewire.chat-componant');
    }
        
    public function mount($user_id)
    {
        $this->user = User::find($user_id); // Use `find` for cleaner syntax
        $this->recever_id = $user_id;
        $this->sender_id = auth()->id(); // Cleaner way to get the authenticated user ID

        $messages = Message::where(function ($query) {
            $query->where('recever_id', $this->recever_id)
                  ->where('sender_id', $this->sender_id);
        })
        ->orWhere(function ($query) {
            $query->where('recever_id', $this->sender_id)
                  ->where('sender_id', $this->recever_id);
        })
        ->with(['sender:id,name', 'recevier:id,name'])
        ->get();

        foreach ($messages as $message) {
            $this->appendChild($message); // Call the corrected method
        }

    }
#[On('echo-private:chat-channel.{sender_id},MessageSent')]
public function listenForMessage($event){
    $chatmsg=Message::whereId($event["msg"]["id"])
    ->with('sender:id,name','recevier:id,name')
    ->first();
    $this->appendChild($chatmsg); 
}

    public function appendChild($message)
    {
        // Access sender and receiver names through their relationships
        $this->messages[] = [
            'id' => $message->id,
            'message' => $message->message,
            'sender' => $message->sender->name, // Accessing sender's name correctly
            'recever' => $message->recevier->name, // Accessing receiver's name correctly
        ];
    }

    public function sendMessage()
    {
        $chatmsg = new Message();
        $chatmsg->sender_id = $this->sender_id;
        $chatmsg->recever_id = $this->recever_id;
        $chatmsg->message = $this->message;
        $chatmsg->save();

        broadcast(new MessageSent($chatmsg))->toOthers();
        // Reset input and append the new message to the messages array
        $this->appendChild($chatmsg); 
        $this->message = ""; // Clear the message input
    }
}
