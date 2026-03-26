<?php

use Livewire\Volt\Component;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $viewPostId = null;
    public $viewPost = null;

    public function with()
    {
        return [
            'posts' => ForumPost::with(['user'])->withCount('comments')->latest()->paginate(15),
        ];
    }

    public function viewPost($id)
    {
        $this->viewPost = ForumPost::with(['user', 'comments.user'])->findOrFail($id);
        $this->viewPostId = $id;
    }

    public function closeModal()
    {
        $this->viewPost = null;
        $this->viewPostId = null;
    }

    public function deletePost($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        ForumComment::where('forum_post_id', $post->id)->delete();
        $post->delete();

        $this->closeModal();
        session()->flash('success', 'Post and all its comments have been deleted.');
    }

    public function deleteComment($id)
    {
        ForumComment::findOrFail($id)->delete();

        // Refresh the viewPost data
        if ($this->viewPostId) {
            $this->viewPost = ForumPost::with(['user', 'comments.user'])->find($this->viewPostId);
        }

        session()->flash('success', 'Comment deleted successfully.');
    }
}; ?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Forum Moderation</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">View and moderate all community forum posts and comments.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Author</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Post Title & Content</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Category</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Comments</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs">Date</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                                        @if ($post->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm">
                                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $post->user->name }}</div>
                                        <div class="text-xs text-gray-400">Unit {{ $post->user->unit_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900 dark:text-white">{{ Str::limit($post->title, 40) }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($post->content, 60) }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                    {{ ucfirst($post->category) }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-gray-700 dark:text-gray-300 font-semibold">
                                {{ $post->comments_count }}
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $post->created_at->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="viewPost({{ $post->id }})"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-white hover:bg-indigo-600 dark:hover:bg-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 p-2.5 rounded-lg transition-all duration-200 border border-indigo-100 dark:border-indigo-800 hover:border-transparent"
                                        title="View Post">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="Delete this post and ALL its comments? This cannot be undone."
                                        class="text-red-500 hover:text-white bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-600 p-2.5 rounded-lg transition-all duration-200 border border-red-100 dark:border-red-800 hover:border-transparent"
                                        title="Delete Post">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100 dark:border-gray-600">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No forum posts yet</h3>
                                    <p class="text-sm text-gray-500">Community posts will appear here to moderate.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- View Post Modal --}}
    @if ($viewPost)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl w-full max-w-2xl my-8 border border-gray-100 dark:border-gray-700 relative">

                {{-- Modal Header --}}
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-[2rem] flex justify-between items-start">
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 mb-2">
                            {{ ucfirst($viewPost->category) }}
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $viewPost->title }}</h3>
                        <div class="text-sm text-gray-500 mt-1">by <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $viewPost->user->name }}</span> &bull; {{ $viewPost->created_at->diffForHumans() }}</div>
                    </div>
                    <button wire:click="closeModal" class="ml-4 shrink-0 text-gray-400 hover:text-gray-700 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 p-2.5 rounded-full border border-gray-200 dark:border-gray-600 transition-all hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Post Content --}}
                <div class="p-8">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $viewPost->content }}</p>

                    @if ($viewPost->image_path)
                        <div class="mt-4 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            <img src="{{ asset('storage/' . $viewPost->image_path) }}" alt="post image" class="w-full h-auto max-h-64 object-cover">
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button wire:click="deletePost({{ $viewPost->id }})"
                            wire:confirm="Delete this post and ALL its comments? This cannot be undone."
                            class="px-5 py-2.5 bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 hover:bg-red-500 hover:text-white hover:border-transparent rounded-xl font-semibold text-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Entire Post
                        </button>
                    </div>

                    {{-- Comments --}}
                    @if ($viewPost->comments->count() > 0)
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                                Comments ({{ $viewPost->comments->count() }})
                            </h4>
                            <div class="space-y-4">
                                @foreach ($viewPost->comments as $comment)
                                    <div class="flex items-start gap-3 group">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                                            @if ($comment->user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $comment->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-xs">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 bg-gray-50 dark:bg-gray-900/40 rounded-xl px-4 py-3 border border-gray-100 dark:border-gray-700">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $comment->user->name }}</span>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                    <button wire:click="deleteComment({{ $comment->id }})"
                                                        wire:confirm="Delete this comment?"
                                                        class="opacity-0 group-hover:opacity-100 p-1.5 text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-all"
                                                        title="Delete Comment">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 text-center text-gray-400 text-sm">
                            No comments on this post.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
