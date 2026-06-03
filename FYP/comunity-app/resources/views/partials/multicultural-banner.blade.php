{{--
    partials/multicultural-banner.blade.php
    Global multicultural hero banner — included in layouts/app.blade.php
--}}
<div style="
    position: relative;
    width: 100%;
    height: 96px;
    overflow: hidden;
    flex-shrink: 0;
    background: #0b1120;
">
    {{-- Hero background image --}}
    <div style="
        position: absolute;
        inset: 0;
        background-image: url('{{ asset('images/multicultural-hero.png') }}');
        background-size: cover;
        background-position: center 35%;
        filter: brightness(0.65) saturate(1.3);
    "></div>

    {{-- Gradient overlay: deeper at edges --}}
    <div style="
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to right,
            rgba(5, 46, 22, 0.65) 0%,
            rgba(0, 0, 0, 0.2) 30%,
            rgba(127, 29, 29, 0.28) 50%,
            rgba(0, 0, 0, 0.2) 70%,
            rgba(56, 14, 110, 0.65) 100%
        );
    "></div>

    {{-- Thin 3-colour strip at top --}}
    <div style="position: absolute; top:0; left:0; right:0; height:2px; display:flex;">
        <div style="flex:1; background:linear-gradient(90deg,#059669,#34d399);"></div>
        <div style="flex:1; background:linear-gradient(90deg,#dc2626,#f59e0b);"></div>
        <div style="flex:1; background:linear-gradient(90deg,#7c3aed,#f97316);"></div>
    </div>

    {{-- Bottom fade into page --}}
    <div style="
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 36px;
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
        {{-- Left: Culture icons + label --}}
        <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px; line-height:1; filter:drop-shadow(0 1px 3px rgba(0,0,0,.6));">🕌</span>
            <span style="font-size:18px; line-height:1; filter:drop-shadow(0 1px 3px rgba(0,0,0,.6));">🏮</span>
            <span style="font-size:18px; line-height:1; filter:drop-shadow(0 1px 3px rgba(0,0,0,.6));">🪔</span>
            <div style="width:1px; height:22px; background:rgba(255,255,255,0.2); margin:0 6px;"></div>
            <span style="
                font-size: 11px; font-weight: 800; color: rgba(255,255,255,.92);
                letter-spacing: 0.06em; text-transform: uppercase;
                text-shadow: 0 1px 5px rgba(0,0,0,.7);
            ">Malaysia's Multicultural Community</span>
        </div>

        {{-- Right: Culture pills --}}
        <div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap; justify-content:flex-end;">
            <span class="mc-pill mc-pill-malay">🌙 Malay</span>
            <span class="mc-pill mc-pill-chinese">🏮 Chinese</span>
            <span class="mc-pill mc-pill-indian">🪔 Indian</span>
            <span class="mc-pill mc-pill-general">✨ All</span>
        </div>
    </div>
</div>
