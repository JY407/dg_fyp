<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public $title = '';
    public $content = '';
    public $category = 'Discussion';
    public $image;

    public $commentContent = [];
    public $showCommentsFor = null;
    
    // Categories list
    public $categories = [
        'Discussion',
        'Suggestion',
        'Announcement',
        'Issue',
        'Workshop',
        'Safety',
        'Gardening',
        'Events'
    ];

    public function with()
    {
        return [
            'posts' => ForumPost::with(['user', 'likes', 'comments.user'])->latest()->get(),
            'trendingCategories' => ForumPost::select('category', \DB::raw('count(*) as total'))
                                        ->groupBy('category')
                                        ->orderBy('total', 'desc')
                                        ->take(5)
                                        ->get()
        ];
    }
    
    public function createPost()
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('forum-images', 'public');
        }

        auth()->user()->forumPosts()->create([
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'image_path' => $imagePath,
        ]);

        $this->reset(['title', 'content', 'category', 'image']);
        session()->flash('success', 'Your post has been published.');
    }

    public function toggleLike($postId)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $userId = auth()->id();
        $like = ForumLike::where('user_id', $userId)->where('forum_post_id', $postId)->first();

        if ($like) {
            $like->delete();
        } else {
            ForumLike::create([
                'user_id' => $userId,
                'forum_post_id' => $postId
            ]);
        }
    }

    public function toggleComments($postId)
    {
        if ($this->showCommentsFor === $postId) {
            $this->showCommentsFor = null;
        } else {
            $this->showCommentsFor = $postId;
        }
    }

    public function addComment($postId)
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $content = trim($this->commentContent[$postId] ?? '');

        if (empty($content)) {
            return;
        }

        ForumComment::create([
            'user_id' => auth()->id(),
            'forum_post_id' => $postId,
            'content' => $content
        ]);

        $this->commentContent[$postId] = '';
        $this->showCommentsFor = $postId; // Ensure comments are shown
    }
}; ?>

<div style="background-color: #0f0f23 !important; min-height: 100vh; padding-top: 80px; font-family: 'Outfit', sans-serif; color: white;">

    <!-- Background Ambient Glow -->
    <div style="position: fixed; top: 20%; left: 20%; width: 300px; height: 300px; background: #764ba2; filter: blur(150px); opacity: 0.2; pointer-events: none; z-index: 0;"></div>
    <div style="position: fixed; bottom: 20%; right: 20%; width: 400px; height: 400px; background: #667eea; filter: blur(150px); opacity: 0.2; pointer-events: none; z-index: 0;"></div>

    <div class="container mx-auto px-6 relative z-10 pb-16" style="max-width: 1200px;">

        <!-- Header -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-6">
            <div>
                <h1 style="font-size: 3.5rem; font-weight: 800; background: linear-gradient(to right, #fff, #a5b4fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.05em; margin-bottom: 0.5rem;">
                    Community Forum
                </h1>
                <p style="color: #94a3b8; font-size: 1.1rem;">Connect with your neighborhood.</p>
            </div>
            
            @if (session()->has('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-2 rounded-xl text-sm font-medium animate-pulse">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Feed -->
            <div class="lg:col-span-8 flex flex-col gap-8">

                <!-- Create Post Card -->
                @auth
                    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px;">
                        <form wire:submit="createPost">
                            <div class="flex gap-4 mb-4">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                                    {{ auth()->user()->initials() ?? 'ME' }}
                                </div>
                                <div class="w-full space-y-3">
                                    <input wire:model="title" type="text" placeholder="Subject or Title..." class="form-input" required>
                                    @error('title') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                                    
                                    <textarea wire:model="content" placeholder="Share something with the community..." rows="3" class="form-textarea" required></textarea>
                                    @error('content') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-4 border-t border-white/5 ml-16">
                                <div class="flex gap-3 w-full sm:w-auto">
                                    <select wire:model="category" class="bg-black/20 border-none text-gray-300 text-sm rounded-lg px-3 py-2 outline-none focus:bg-black/40">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" class="bg-gray-900 text-white">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <label class="flex items-center gap-2 cursor-pointer bg-black/20 hover:bg-black/40 transition-colors px-3 py-2 rounded-lg text-sm text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        <span class="hidden sm:inline">Image</span>
                                        <input wire:model="image" type="file" class="hidden" accept="image/*">
                                    </label>
                                </div>
                                
                                <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" class="w-full sm:w-auto text-white px-6 py-2 rounded-full font-bold shadow-[0_4px_15px_rgba(102,126,234,0.4)] hover:-translate-y-0.5 transition-transform flex items-center justify-center gap-2">
                                    <svg wire:loading wire:target="createPost" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Post
                                </button>
                            </div>
                            
                            @if($image)
                                <div class="ml-16 mt-3 text-xs text-indigo-400 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Image attached successfully
                                </div>
                            @endif
                        </form>
                    </div>
                @else
                    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px; text-align: center;">
                        <p class="text-gray-400 mb-4">You must be logged in to participate in the community discussions.</p>
                        <a href="{{ route('login') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 24px; border-radius: 9999px; font-weight: 700; text-decoration: none;">Log In</a>
                    </div>
                @endauth

                <!-- Posts List -->
                @forelse($posts as $post)
                    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 0; overflow: hidden; transition: transform 0.3s;" class="hover:-translate-y-1">
                        <div style="padding: 24px;">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex gap-3 items-center">
                                    @if($post->user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 48px; height: 48px; border-radius: 50%; background: #334155; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                                            {{ $post->user->initials() }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 style="margin: 0; font-weight: 700; color: white;">{{ $post->user->name }}</h3>
                                        <span style="color: #64748b; font-size: 0.85rem;">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    // Assign colors based on category
                                    $catColor = 'rgba(16, 185, 129, 0.1)';
                                    $catText = '#34d399';
                                    
                                    switch(strtolower($post->category)) {
                                        case 'discussion': $catColor = 'rgba(99, 102, 241, 0.1)'; $catText = '#818cf8'; break;
                                        case 'announcement': $catColor = 'rgba(239, 68, 68, 0.1)'; $catText = '#f87171'; break;
                                        case 'suggestion': $catColor = 'rgba(245, 158, 11, 0.1)'; $catText = '#fbbf24'; break;
                                        case 'issue': $catColor = 'rgba(220, 38, 38, 0.1)'; $catText = '#fca5a5'; break;
                                        case 'events': $catColor = 'rgba(236, 72, 153, 0.1)'; $catText = '#f472b6'; break;
                                    }
                                @endphp
                                
                                <span style="background: {{ $catColor }}; color: {{ $catText }}; padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                    {{ $post->category }}
                                </span>
                            </div>

                            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 12px; color: #f1f5f9;">
                                {{ $post->title }}
                            </h2>
                            <p style="color: #cbd5e1; line-height: 1.6; margin-bottom: 20px; white-space: pre-wrap;">{{ $post->content }}</p>

                            @if($post->image_path)
                                <div style="width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 20px;">
                                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" class="w-full h-auto object-cover max-h-96">
                                </div>
                            @endif
                        </div>

                        <!-- Action Bar -->
                        <div style="padding: 16px 24px; background: rgba(0,0,0,0.2); display: flex; gap: 24px; border-top: 1px solid rgba(255,255,255,0.05);">
                            @php
                                $hasLiked = auth()->check() && $post->likes->contains('user_id', auth()->id());
                            @endphp
                            
                            <button wire:click="toggleLike({{ $post->id }})" class="flex items-center gap-2 font-semibold transition-colors {{ $hasLiked ? 'text-pink-500' : 'text-gray-400 hover:text-pink-400' }}">
                                <svg style="width: 20px;" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                {{ $post->likes->count() }} Likes
                            </button>
                            
                            <button wire:click="toggleComments({{ $post->id }})" class="flex items-center gap-2 font-semibold text-gray-400 hover:text-indigo-400 transition-colors">
                                <svg style="width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                {{ $post->comments->count() }} Comments
                            </button>
                        </div>
                        
                        <!-- Comments Section Dropdown -->
                        @if($showCommentsFor === $post->id)
                            <div class="bg-black/30 w-full p-6 border-t border-white/5 space-y-4">
                                
                                <!-- Existing Comments -->
                                @if($post->comments->count() > 0)
                                    <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                                        @foreach($post->comments as $comment)
                                            <div class="flex gap-3">
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #475569; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.75rem; flex-shrink: 0;">
                                                    {{ $comment->user->initials() }}
                                                </div>
                                                <div class="bg-white/5 rounded-2xl rounded-tl-none px-4 py-3 flex-1">
                                                    <div class="flex justify-between items-baseline mb-1">
                                                        <span class="font-bold text-sm text-gray-200">{{ $comment->user->name }}</span>
                                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-300">{{ $comment->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 text-center pb-2">No comments yet. Be the first to share your thoughts!</p>
                                @endif
                                
                                <!-- Add Comment Form -->
                                @auth
                                    <form wire:submit="addComment({{ $post->id }})" class="flex gap-3 pt-2">
                                        <input wire:model="commentContent.{{ $post->id }}" type="text" placeholder="Write a comment..." class="form-input !rounded-full" required>
                                        <button type="submit" class="bg-indigo-600/80 hover:bg-indigo-500 text-white rounded-full w-12 h-12 flex items-center justify-center shrink-0 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center pt-2 text-sm text-gray-500">
                                        Please <a href="{{ route('login') }}" class="text-indigo-400 hover:underline">log in</a> to leave a comment.
                                    </div>
                                @endauth
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 48px 24px; text-align: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-600 mb-4"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <h3 class="text-xl font-bold text-gray-300 mb-2">No Discussions Yet</h3>
                        <p class="text-gray-500">Be the first to start a conversation with the community.</p>
                    </div>
                @endforelse

            </div>

            <!-- Sticky Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="sticky top-24" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px;">
                    <h3 style="color: white; font-weight: 800; font-size: 1.2rem; margin-bottom: 20px;">Trending Topics</h3>
                    
                    @if(count($trendingCategories) > 0)
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            @foreach($trendingCategories as $trend)
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    @php
                                        // Assign colors based on category
                                        $trendColor = '#34d399';
                                        switch(strtolower($trend->category)) {
                                            case 'discussion': $trendColor = '#818cf8'; break;
                                            case 'announcement': $trendColor = '#f87171'; break;
                                            case 'suggestion': $trendColor = '#fbbf24'; break;
                                            case 'issue': $trendColor = '#fca5a5'; break;
                                            case 'events': $trendColor = '#f472b6'; break;
                                        }
                                    @endphp
                                    <span style="color: {{ $trendColor }}; font-weight: 600;">#{{ $trend->category }}</span>
                                    <span style="background: rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; padding: 2px 8px; border-radius: 99px;">
                                        {{ $trend->total }} Posts
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Not enough data to show trending topics yet.</p>
                    @endif
                    
                    <div class="mt-8 pt-6 border-t border-white/5">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Forum Rules</h4>
                        <ul class="text-xs text-gray-500 space-y-3">
                            <li class="flex gap-2"><span class="text-indigo-400">•</span> Be respectful and kind to neighbors.</li>
                            <li class="flex gap-2"><span class="text-indigo-400">•</span> No spam or unauthorized advertisements.</li>
                            <li class="flex gap-2"><span class="text-indigo-400">•</span> Keep discussions relevant to the community.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
