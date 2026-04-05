{{--
    partials/multicultural-banner.blade.php
    Global multicultural hero banner — included in layouts/app.blade.php (appears on every page)
--}}
<div style="
    position: relative;
    width: 100%;
    height: 90px;
    overflow: hidden;
    flex-shrink: 0;
    background: #0f172a;
">
    {{-- Hero background image --}}
    <div style="
        position: absolute;
        inset: 0;
        background-image: url('{{ asset('images/multicultural-hero.png') }}');
        background-size: cover;
        background-position: center 40%;
        filter: brightness(0.75) saturate(1.2);
    "></div>

    {{-- Gradient overlay – darker at edges, lighter in centre --}}
    <div style="
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to right,
            rgba(6,78,59,0.55) 0%,
            rgba(0,0,0,0.25) 33%,
            rgba(127,29,29,0.3) 50%,
            rgba(0,0,0,0.25) 67%,
            rgba(76,29,149,0.55) 100%
        );
    "></div>

    {{-- Bottom vignette to blend with page content --}}
    <div style="
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 32px;
        background: linear-gradient(to top, #0f172a, transparent);
    "></div>

    {{-- Content row --}}
    <div style="
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 24px;
        gap: 12px;
    ">
        {{-- Left: Culture identity --}}
        <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:20px; line-height:1;">🕌</span>
            <span style="font-size:20px; line-height:1;">🏮</span>
            <span style="font-size:20px; line-height:1;">🪔</span>
            <div style="
                width: 1px; height: 24px;
                background: rgba(255,255,255,0.2);
                margin: 0 4px;
            "></div>
            <span style="
                font-size: 12px; font-weight: 800; color: rgba(255,255,255,0.9);
                letter-spacing: 0.05em; text-transform: uppercase;
                text-shadow: 0 1px 4px rgba(0,0,0,0.6);
            ">Malaysia's Multicultural Community</span>
        </div>

        {{-- Right: Culture pills --}}
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; justify-content:flex-end;">
            <span class="mc-pill mc-pill-malay">🌙 Malay</span>
            <span class="mc-pill mc-pill-chinese">🏮 Chinese</span>
            <span class="mc-pill mc-pill-indian">🪔 Indian</span>
            <span class="mc-pill mc-pill-general">✨ All</span>
        </div>
    </div>
</div>
