<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Component;

class Chat extends Component
{
    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $authId;
    public $loginID;

    public function mount()
    {
        $this->users = User::whereNot("id", Auth::id())->latest()->get();
        $this->selectedUser = $this->users->first();
        $this->loadMessages();
        $this->loginID = Auth::id();

        $this->messages = ChatMessage::query()
            ->where(function($q){
            $q->where("sender_id",Auth::id())
            ->where("receiver_id",$this->selectedUser->id);
        })
            ->orwhere(function($q){
            $q->where("sender_id",$this->selectedUser->id)
        ->where("receiver_id",Auth::id());
        })
        ->latest()->get();
    }

public function selectUser($id)
{
    $this->selectedUser = User::find($id);
    $this->loadMessages();
}

public function loadMessages()
{
            $this->messages = ChatMessage::query()
            ->where(function($q){
            $q->where("sender_id",Auth::id())
            ->where("receiver_id",$this->selectedUser->id);
        })
            ->orwhere(function($q){
            $q->where("sender_id",$this->selectedUser->id)
        ->where("receiver_id",Auth::id());
        })
        ->latest()->get();
}

public function submit()
{
    if(!$this->newMessage) return;

    $message = ChatMessage::create([
        "sender_id" => Auth::id(),
        "receiver_id" =>$this->selectedUser->id,
        "message"=> $this->newMessage
    ]);
    $this->messages->push($message);

    $this->newMessage = '';
    broadcast(new MessageSent($message));
}

    public function updatedNewMessage($value)
    {
        $this->dispatch("userTyping", userID: $this->loginID, userName: Auth::user()->name, selectedUserID: $this->selectedUser->id);
    }


    public function getListeners()
    {
        return[
            "echo-private:chat.{$this->loginID},MessageSent"=>"newChatMessageNotification"
        ];
    }

    public function newChatMessageNotification($message)
    {
        if($message['sender_id'] == $this->selectedUser->id){
            $messageObj = ChatMessage::find($message['id']);
            $this->messages->push($messageObj);
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
