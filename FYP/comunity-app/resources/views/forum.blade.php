@extends('layouts.app')

@section('title', 'Community Forum')

@section('content')
    {{-- Isolated Dark/Glass Theme --}}
    <div
        style="background-color: #0f0f23 !important; min-height: 100vh; padding-top: 80px; font-family: 'Outfit', sans-serif; color: white;">

        <!-- Background Ambient Glow -->
        <div
            style="position: fixed; top: 20%; left: 20%; width: 300px; height: 300px; background: #764ba2; filter: blur(150px); opacity: 0.2; pointer-events: none; z-index: 0;">
        </div>
        <div
            style="position: fixed; bottom: 20%; right: 20%; width: 400px; height: 400px; background: #667eea; filter: blur(150px); opacity: 0.2; pointer-events: none; z-index: 0;">
        </div>

        <div class="container mx-auto px-6 relative z-10" style="max-width: 1200px;">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-6">
                <div>
                    <h1
                        style="font-size: 3.5rem; font-weight: 800; background: linear-gradient(to right, #fff, #a5b4fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.05em; margin-bottom: 0.5rem;">
                        Community Forum
                    </h1>
                    <p style="color: #94a3b8; font-size: 1.1rem;">Connect with your neighborhood.</p>
                </div>

                <button
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 9999px; font-weight: 700; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); border: none; cursor: pointer; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    + New Discussion
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Feed -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- Input Card -->
                    <div
                        style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px;">
                        <div class="flex gap-4">
                            <div
                                style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                ME</div>
                            <input type="text" placeholder="Share something with the community..."
                                style="width: 100%; background: rgba(0,0,0,0.2); border: none; padding: 12px 20px; border-radius: 12px; color: white; outline: none; transition: background 0.3s;"
                                onfocus="this.style.background='rgba(0,0,0,0.4)'"
                                onblur="this.style.background='rgba(0,0,0,0.2)'">
                        </div>
                    </div>

                    <!-- Post Card 1 -->
                    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 0; overflow: hidden; transition: transform 0.3s;"
                        onmouseover="this.style.transform='translateY(-5px)'"
                        onmouseout="this.style.transform='translateY(0)'">
                        <div style="padding: 24px;">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex gap-3 items-center">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=0D8ABC&color=fff"
                                        style="width: 48px; height: 48px; border-radius: 50%;">
                                    <div>
                                        <h3 style="margin: 0; font-weight: 700; color: white;">Sarah Johnson</h3>
                                        <span style="color: #64748b; font-size: 0.85rem;">2 hours ago</span>
                                    </div>
                                </div>
                                <span
                                    style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Gardening</span>
                            </div>

                            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 12px; color: #f1f5f9;">Community
                                Garden Initiative 🌻</h2>
                            <p style="color: #94a3b8; line-height: 1.6; margin-bottom: 20px;">
                                Excited to announce our new community garden project! Join us this Saturday to help plant
                                the first seeds. We have tomatoes, basil, and peppers ready to go!
                            </p>

                            <!-- Gradient Image Placeholder -->
                            <div
                                style="height: 200px; width: 100%; border-radius: 16px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(59, 130, 246, 0.2)); display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <span style="color: rgba(255,255,255,0.5); font-weight: 600;">Event Image</span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div
                            style="padding: 16px 24px; background: rgba(0,0,0,0.2); display: flex; gap: 24px; border-top: 1px solid rgba(255,255,255,0.05);">
                            <button
                                style="background: none; border: none; color: #94a3b8; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                                24 Likes
                            </button>
                            <button
                                style="background: none; border: none; color: #94a3b8; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                12 Comments
                            </button>
                        </div>
                    </div>

                    <!-- Post Card 2 -->
                    <div
                        style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px;">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex gap-3 items-center">
                                <img src="https://ui-avatars.com/api/?name=Mike+Chen&background=6366f1&color=fff"
                                    style="width: 48px; height: 48px; border-radius: 50%;">
                                <div>
                                    <h3 style="margin: 0; font-weight: 700; color: white;">Mike Chen</h3>
                                    <span style="color: #64748b; font-size: 0.85rem;">5 hours ago</span>
                                </div>
                            </div>
                            <span
                                style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Safety</span>
                        </div>

                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 12px; color: #f1f5f9;">Neighborhood
                            Watch Update 🛡️</h2>
                        <p style="color: #94a3b8; line-height: 1.6; margin-bottom: 20px;">
                            Monthly neighborhood watch meeting scheduled for next week. Important safety updates to discuss
                            regarding the new gate system.
                        </p>

                        <div
                            style="padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 24px;">
                            <button
                                style="background: none; border: none; color: #94a3b8; font-weight: 600; cursor: pointer;">❤️
                                45 Likes</button>
                            <button
                                style="background: none; border: none; color: #94a3b8; font-weight: 600; cursor: pointer;">💬
                                8 Comments</button>
                        </div>
                    </div>

                </div>

                <!-- Sticky Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    <div
                        style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 24px; padding: 24px;">
                        <h3 style="color: white; font-weight: 800; font-size: 1.2rem; margin-bottom: 20px;">Trending Topics
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #818cf8; font-weight: 600;">#Gardening</span>
                                <span
                                    style="background: rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; padding: 2px 8px; border-radius: 99px;">23
                                    Posts</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #f472b6; font-weight: 600;">#Safety</span>
                                <span
                                    style="background: rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; padding: 2px 8px; border-radius: 99px;">18
                                    Posts</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #34d399; font-weight: 600;">#Events</span>
                                <span
                                    style="background: rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.75rem; padding: 2px 8px; border-radius: 99px;">12
                                    Posts</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection