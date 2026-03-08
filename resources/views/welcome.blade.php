<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lie Detector Arena</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<style>
:root {
    --red: #C0392B; --red-bright: #E74C3C; --red-pale: #FADBD8;
    --ink: #1A1208; --paper: #FDFAF4;
    --code-bg: #1A1612; --code-fg: #E8DFC8;
    --mono: 'IBM Plex Mono', monospace;
    --serif: 'Lora', Georgia, serif;
    --display: 'Bebas Neue', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: var(--serif);
    background: var(--ink);
    color: var(--code-fg);
    min-height: 100vh;
    display: flex; flex-direction: column;
}
.topbar {
    background: #120F0B;
    border-bottom: 1px solid #2A2520;
    padding: 14px 32px;
    display: flex; align-items: center; justify-content: space-between;
}
.topbar-logo {
    font-family: var(--display); font-size: 22px; color: var(--red);
    letter-spacing: 0.06em;
}
.topbar-sub {
    font-family: var(--mono); font-size: 11px; color: #443C30;
    letter-spacing: 0.15em;
}
.body {
    flex: 1; display: flex; align-items: center; justify-content: center;
    padding: 60px 32px;
    background: radial-gradient(ellipse 60% 60% at 50% 40%, rgba(192,57,43,0.08) 0%, transparent 70%);
    position: relative; overflow: hidden;
}
.body::before {
    content: '';
    position: absolute; inset: 0;
    background-image:
    linear-gradient(rgba(192,57,43,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(192,57,43,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}
.center { width: 100%; max-width: 460px; position: relative; }
.hero-title {
    font-family: var(--display); font-size: 88px; line-height: 0.9;
    color: #F5EFE0; letter-spacing: 0.02em; margin-bottom: 6px;
}
.hero-title em { color: var(--red-bright); font-style: normal; display: block; }
.hero-sub {
    font-family: var(--mono); font-size: 12px; color: #665A4A;
    letter-spacing: 0.12em; margin-bottom: 40px;
}
.card {
    background: #120F0B; border: 1px solid #2A2520;
    padding: 28px; margin-bottom: 14px;
}
.card-title {
    font-family: var(--display); font-size: 22px; color: var(--code-fg);
    letter-spacing: 0.06em; margin-bottom: 18px;
    padding-bottom: 12px; border-bottom: 1px solid #2A2520;
}
.field { margin-bottom: 16px; }
.field label {
    display: block; font-family: var(--mono); font-size: 10px;
    color: var(--red); letter-spacing: 0.2em; text-transform: uppercase;
    margin-bottom: 6px;
}
.field input {
    width: 100%; background: #0E0B08; border: 1px solid #2A2520;
    padding: 10px 14px; font-family: var(--mono); font-size: 13px;
    color: var(--code-fg); outline: none; transition: border-color 0.15s;
}
.field input:focus { border-color: var(--red); }
.btn {
    width: 100%; background: var(--red); border: none; padding: 13px;
    font-family: var(--display); font-size: 22px; letter-spacing: 0.08em;
    color: white; cursor: pointer; transition: background 0.15s;
}
.btn:hover { background: var(--red-bright); }
.btn-ghost {
    background: transparent; border: 1px solid #2A2520;
    color: #665A4A; transition: all 0.15s;
}
.btn-ghost:hover { border-color: var(--red); color: var(--red); }
.divider-or {
    display: flex; align-items: center; gap: 14px; margin: 4px 0;
}
.divider-or::before, .divider-or::after {
    content: ''; flex: 1; height: 1px; background: #2A2520;
}
.divider-or span {
    font-family: var(--mono); font-size: 10px; color: #443C30; letter-spacing: 0.15em;
}
.flash {
    padding: 10px 16px; margin-bottom: 16px;
    font-family: var(--mono); font-size: 12px;
    border-left: 3px solid var(--red);
    background: #1A0808; color: var(--red);
}
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🕵️ Lie Detector Arena</div>
    <div class="topbar-sub">// THE TRUTH WILL BE REVEALED</div>
</div>

<div class="body">
    <div class="center">

    <div class="hero-title">LIE<em>DETECTOR</em></div>
    <div class="hero-sub">// 2 truths · 1 lie · AI decides</div>

    @if(session('error'))
        <div class="flash">✗ &nbsp;{{ session('error') }}</div>
    @endif

    {{-- CREATE GAME --}}
    <div class="card">
        <div class="card-title">Create New Game</div>
        <form action="{{ route('games.store') }}" method="POST">
        @csrf
        <div class="field">
            <label>Your Nickname</label>
            <input type="text" name="nickname" placeholder="e.g. ShadowFox_42" required maxlength="30" value="{{ old('nickname') }}" />
            @error('nickname') <div style="color:var(--red);font-family:var(--mono);font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label>Game Name</label>
            <input type="text" name="name" placeholder="e.g. Friday Night Deception" required maxlength="60" value="{{ old('name') }}" />
            @error('name') <div style="color:var(--red);font-family:var(--mono);font-size:11px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn">Create Arena →</button>
        </form>
    </div>

    <div class="divider-or"><span>or</span></div>

    {{-- JOIN GAME --}}
    <div class="card">
        <div class="card-title">Join Existing Game</div>
        <form action="{{ route('games.join') }}" method="POST">
        @csrf
        <div class="field">
            <label>Your Nickname</label>
            <input type="text" name="nickname" placeholder="e.g. GhostWriter" required maxlength="30" />
        </div>
        <div class="field">
            <label>Game Code</label>
            <input type="text" name="code" placeholder="e.g. XK4T9W"
            style="letter-spacing:0.2em;font-size:18px;text-transform:uppercase;"
            required maxlength="6" />
        </div>
        <button type="submit" class="btn btn-ghost">Join Game →</button>
        </form>
    </div>

    </div>
</div>

</body>
</html>
