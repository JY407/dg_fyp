<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Component;


use Livewire\Attributes\Computed;
use App\Models\Group;
use App\Models\GroupMember;

class Chat extends Component
{
    // public $users; // Convert to computed
    // public $groups; // Convert to computed
    public $selectedUser;
    public $selectedGroup; // New property for selected group
    public $isGroupChat = false; // Flag to distinguish chat type
    public $newMessage;
    public $chatMessages; // Renamed from messages to avoid conflict
    public $authId;
    public $loginID;

    // Create Group Modal Properties
    public $showCreateGroupModal = false;
    public $newGroupName = '';
    public $selectedUsersForGroup = [];

    public function mount()
    {
        // $this->users and groups are now computed
        $this->authId = Auth::id();
        $this->loginID = Auth::id();

        // Default to first user for now, or null
        // Access computed property directly or via method
        $this->selectedUser = $this->users()->first();
        if ($this->selectedUser) {
            $this->loadMessages();
        } else {
            $this->chatMessages = collect();
        }
    }

    #[Computed]
    public function users()
    {
        return User::whereNot("id", Auth::id())->latest()->get();
    }

    #[Computed]
    public function groups()
    {
        return Group::where('created_by', Auth::id())
            ->orWhereHas('members', function ($q) {
                $q->where('user_id', Auth::id());
            })->latest()->get();
    }

    // Remove old loadGroups method as it is now computed
    // public function loadGroups() ...

    public function selectUser($id)
    {
        $this->isGroupChat = false;
        $this->selectedGroup = null;
        $this->selectedUser = User::find($id);
        $this->loadMessages();
    }

    public function selectGroup($id)
    {
        $this->isGroupChat = true;
        $this->selectedUser = null;
        $this->selectedGroup = Group::find($id);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if ($this->isGroupChat && $this->selectedGroup) {
            $this->chatMessages = ChatMessage::where('group_id', $this->selectedGroup->id)
                ->with('sender')
                ->latest()
                ->get()
                ->reverse(); // Reverse for display (oldest first)
        } elseif (!$this->isGroupChat && $this->selectedUser) {
            $this->chatMessages = ChatMessage::query()
                ->where(function ($q) {
                    $q->where("sender_id", Auth::id())
                        ->where("receiver_id", $this->selectedUser->id);
                })
                ->orwhere(function ($q) {
                    $q->where("sender_id", $this->selectedUser->id)
                        ->where("receiver_id", Auth::id());
                })
                ->latest()
                ->get()
                ->reverse(); // Reverse for display
        } else {
            $this->chatMessages = collect();
        }
    }

    public function createGroup()
    {
        $validatedData = $this->validate([
            'newGroupName' => 'required|string|max:255',
            'selectedUsersForGroup' => 'required|array|min:1'
        ]);

        $group = Group::create([
            'name' => $this->newGroupName,
            'created_by' => Auth::id()
        ]);

        // Add creator as member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id()
        ]);

        // Add selected members
        foreach ($this->selectedUsersForGroup as $userId) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $userId
            ]);
        }

        $this->newGroupName = '';
        $this->selectedUsersForGroup = [];
        $this->showCreateGroupModal = false;

        // Invalidate groups cache if needed, but computed property should be fresh on next request usually, 
        // OR we can unset property if cached. 
        unset($this->groups);

        $this->selectGroup($group->id);
    }

    public function submit()
    {
        if (!$this->newMessage)
            return;

        if ($this->isGroupChat && $this->selectedGroup) {
            $message = ChatMessage::create([
                "sender_id" => Auth::id(),
                "group_id" => $this->selectedGroup->id,
                "message" => $this->newMessage
            ]);
            $this->chatMessages->push($message->load('sender')); // Eager load sender for group chat display
            broadcast(new MessageSent($message))->toOthers(); // Assuming MessageSent handles group channel logic or update logic needed
        } elseif ($this->selectedUser) {
            $message = ChatMessage::create([
                "sender_id" => Auth::id(),
                "receiver_id" => $this->selectedUser->id,
                "message" => $this->newMessage
            ]);
            $this->chatMessages->push($message);
            broadcast(new MessageSent($message));
        }

        $this->newMessage = '';
    }

    public function updatedNewMessage($value)
    {
        if (!$this->isGroupChat && $this->selectedUser) {
            $this->dispatch("userTyping", userID: $this->loginID, userName: Auth::user()->name, selectedUserID: $this->selectedUser->id);
        }
        // Group typing logic could be added here
    }

    public function getListeners()
    {
        // Listen to private channel for direct messages
        // For groups, we might need a presence channel or just listen to group.{id}
        // Ideally, we'd dynamically listen to groups. For now, let's keep it simple.
        return [
            "echo-private:chat.{$this->loginID},MessageSent" => "newChatMessageNotification"
        ];
    }

    public function newChatMessageNotification($message)
    {
        // Handle Direct Messages
        if (!$this->isGroupChat && isset($message['sender_id']) && $this->selectedUser && $message['sender_id'] == $this->selectedUser->id && !isset($message['group_id'])) {
            $messageObj = ChatMessage::find($message['id']);
            $this->chatMessages->push($messageObj);
        }

        // Handle Group Messages (Basic polling/refresh might be needed if not using Echo for groups yet)
        // If we want real-time group messages, we need to listen to group channels.
    }

    public function render()
    {
        return view('livewire.chat', [
            'users' => $this->users,
            'groups' => $this->groups
        ])->layout('layouts.app');
    }
}
