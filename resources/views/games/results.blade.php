<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Final Results — {{ $game->name }} — Lie Detector Arena</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<style>
:root {
    --red: #C0392B; --red-bright: #E74C3C; --red-pale: #FADBD8;
    --amber: #D35400; --green: #1E8449;
    --ink: #1A1208; --code-fg: #E8DFC8;
    --mono: 'IBM Plex Mono', monospace;
    --serif: 'Lora', Georgia, serif;
    --display: 'Bebas Neue', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--serif); background: var(--ink); color: var(--code-fg); min-height: 100vh; display: flex; flex-direction: column; }

.topbar {
    background: #120F0B; border-bottom: 1px solid #2A2520;
    padding: 12px 32px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.topbar-logo { font-family: var(--display); font-size: 22px; color: var(--red); letter-spacing: 0.06em; }
.topbar-right { display: flex; align-items: center; gap: 20px; }
.player-pill {
    background: #1E1812; border: 1px solid #2A2520;
    padding: 6px 14px; font-family: var(--mono); font-size: 12px; color: #887A68;
}
.player-pill strong { color: var(--code-fg); }
.round-complete {
    font-family: var(--mono); font-size: 11px; color: var(--green);
    border: 1px solid var(--green); padding: 4px 12px; letter-spacing: 0.1em;
}
.view-label {
    background: var(--red); color: white;
    font-family: var(--mono); font-size: 11px; letter-spacing: 0.15em;
    text-transform: uppercase; padding: 6px 32px;
    display: flex; justify-content: space-between;
}
.view-label span { opacity: 0.6; font-size: 10px; }

.layout { flex: 1; display: grid; grid-template-columns: 1fr 300px; }
.main { padding: 32px; }
.side { padding: 24px; background: #0E0B08; border-left: 1px solid #2A2520; }

.side-title {
    font-family: var(--mono); font-size: 10px; color: #443C30;
    letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 14px;
}

/* AI Verdict */
.ai-box {
    background: #0E0B08; border: 1px solid var(--red);
    padding: 24px; margin-bottom: 28px; position: relative;
}
.ai-box::before {
    content: '🤖 AI VERDICT';
    position: absolute; top: -10px; left: 20px;
    background: var(--ink); color: var(--red);
    font-family: var(--mono); font-size: 10px; letter-spacing: 0.2em;
    padding: 0 10px;
}
.ai-reasoning {
    font-family: var(--mono); font-size: 13px; color: #887A68;
    line-height: 1.9; margin-bottom: 18px; font-style: italic;
}
.ai-reasoning strong { color: var(--code-fg); font-style: normal; }
.ai-confidence {
    display: flex; align-items: center; gap: 20px;
}
.ai-pct { font-family: var(--display); font-size: 36px; color: var(--red); letter-spacing: 0.04em; }
.ai-label { font-family: var(--mono); font-size: 11px; color: var(--red); }
.ai-note { font-family: var(--mono); font-size: 11px; color: #443C30; }

/* No AI verdict fallback */
.no-ai {
    background: #0E0B08; border: 1px solid #2A2520;
    padding: 16px 20px; margin-bottom: 28px;
    font-family: var(--mono); font-size: 12px; color: #443C30;
}

/* Statement results */
.stmt-section-title {
    font-family: var(--mono); font-size: 10px; color: #443C30;
    letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 12px;
}
.result-card {
    background: #120F0B; border: 1px solid #2A2520;
    padding: 18px 22px; margin-bottom: 8px;
    display: flex; align-items: center; gap: 16px; position: relative; overflow: hidden;
}
.result-card.is-lie { border-color: var(--red); background: #1A0808; }
.result-card.is-lie::after {
    content: 'THE LIE';
    position: absolute; top: 0; right: 0;
    background: var(--red); color: white;
    font-family: var(--mono); font-size: 9px; letter-spacing: 0.2em; padding: 4px 12px;
}
.result-n {
    font-family: var(--display); font-size: 44px;
    color: rgba(255,255,255,0.05); line-height: 1; width: 44px; flex-shrink: 0;
}
.result-card.is-lie .result-n { color: rgba(192,57,43,0.18); }
.result-text {
    font-family: var(--mono); font-size: 13px; color: #665A4A; flex: 1; line-height: 1.6;
}
.result-card.is-lie .result-text { color: var(--code-fg); }
.score-col { text-align: right; flex-shrink: 0; }
.score-pct { font-family: var(--display); font-size: 26px; color: #2A2520; line-height: 1; }
.result-card.is-lie .score-pct { color: var(--red); }
.score-lbl { font-family: var(--mono); font-size: 9px; color: #2A2520; text-transform: uppercase; letter-spacing: 0.1em; }
.ai-pick { font-family: var(--mono); font-size: 9px; color: var(--red); letter-spacing: 0.08em; margin-top: 2px; }
.votes-on { font-family: var(--mono); font-size: 9px; color: #443C30; margin-top: 2px; }

/* Score update banner */
.score-banner {
    display: flex; align-items: center; gap: 20px;
    padding: 18px 22px; margin-top: 24px; border: 1px solid;
}
.score-banner.won  { background: #0A1A0E; border-color: var(--green); }
.score-banner.lost { background: #0E0B08; border-color: #2A2520; }
.score-banner.own  { background: #120A00; border-color: var(--amber); }
.score-emoji { font-size: 28px; }
.score-text-col { flex: 1; }
.score-text-title {
    font-family: var(--display); font-size: 22px; letter-spacing: 0.04em;
}
.won .score-text-title  { color: var(--green); }
.lost .score-text-title { color: #443C30; }
.own .score-text-title  { color: var(--amber); }
.score-text-sub { font-family: var(--mono); font-size: 12px; color: #443C30; }
.score-delta {
    font-family: var(--display); font-size: 38px; letter-spacing: 0.04em;
}
.won .score-delta  { color: var(--green); }
.lost .score-delta { color: #2A2520; }
.own .score-delta  { color: var(--amber); }

/* Leaderboard */
.leaderboard { list-style: none; margin-bottom: 24px; }
.lb-row {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid #1E1812;
}
.lb-row:last-child { border-bottom: none; }
.lb-pos { font-family: var(--display); font-size: 20px; color: #2A2520; width: 24px; flex-shrink: 0; text-align: center; }
.lb-row:nth-child(1) .lb-pos { color: #FFD700; }
.lb-row:nth-child(2) .lb-pos { color: #C0C0C0; }
.lb-row:nth-child(3) .lb-pos { color: #CD7F32; }
.lb-avatar { width: 28px; height: 28px; background: var(--red); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.lb-name { font-family: var(--mono); font-size: 12px; color: var(--code-fg); flex: 1; }
.lb-scores { text-align: right; }
.lb-score { font-family: var(--display); font-size: 20px; color: var(--amber); line-height: 1; }
.lb-delta { font-family: var(--mono); font-size: 10px; color: var(--green); }
.lb-row.is-me .lb-name { color: var(--amber); }

/* Next round */
.next-section { padding-top: 20px; border-top: 1px solid #2A2520; }
.next-label { font-family: var(--mono); font-size: 10px; color: #443C30; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 10px; }
.next-player { font-family: var(--display); font-size: 22px; color: var(--code-fg); letter-spacing: 0.04em; margin-bottom: 14px; }
.btn {
    width: 100%; background: var(--red); border: none; padding: 12px;
    font-family: var(--display); font-size: 20px; letter-spacing: 0.08em;
    color: white; cursor: pointer; transition: background 0.15s;
}
.btn:hover { background: var(--red-bright); }
.waiting-text { font-family: var(--mono); font-size: 11px; color: #443C30; line-height: 1.8; }

/* Game over */
.game-over {
    background: #0E0B08; border: 1px solid var(--amber);
    padding: 20px; text-align: center;
}
.game-over-title { font-family: var(--display); font-size: 32px; color: var(--amber); letter-spacing: 0.06em; margin-bottom: 6px; }
.game-over-winner { font-family: var(--mono); font-size: 13px; color: var(--code-fg); }
.btn-home { background: #120F0B; border: 1px solid #2A2520; color: #665A4A; margin-top: 12px; }
.btn-home:hover { border-color: var(--red); color: var(--red); background: #120F0B; }
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🕵️ Lie Detector Arena</div>
    <div class="topbar-right">
    <div class="player-pill">Playing as <strong>{{ $currentPlayer->nickname }}</strong></div>
    <div class="round-complete">✓ Game Complete</div>
    </div>
</div>

<div class="view-label">
    <span>// FINAL RESULTS — {{ $game->name }}</span>
    <span>All rounds complete</span>
</div>

<div class="layout">
    <div class="main">
        <div style="font-family: var(--display); font-size: 32px; color: var(--code-fg); letter-spacing: 0.04em; margin-bottom: 32px;">
            🏆 Final Leaderboard
        </div>

        {{-- Leaderboard --}}
        <div style="background: #120F0B; border: 1px solid #2A2520; padding: 24px;">
            <div style="margin-bottom: 24px;">
                @foreach($leaderboard as $i => $player)
                <div style="display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: {{ $loop->last ? '0' : '1px solid #1E1812' }};">
                    <div style="font-family: var(--display); font-size: 28px; color: {{ $i === 0 ? 'var(--red)' : ($i === 1 ? '#D4A76A' : ($i === 2 ? '#888888' : '#443C30')) }}; width: 40px; text-align: center;">
                        {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : ($i + 1) . '.')) }}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-family: var(--mono); font-size: 14px; color: var(--code-fg); margin-bottom: 4px;">{{ $player->nickname }}</div>
                        <div style="font-family: var(--mono); font-size: 12px; color: #443C30;">{{ $player->statements->count() }} statement@if($player->statements->count() !== 1)s@endif</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-family: var(--display); font-size: 24px; color: {{ $player->score > 0 ? 'var(--green)' : '#443C30' }};">{{ $player->score }}</div>
                        <div style="font-family: var(--mono); font-size: 11px; color: #443C30; letter-spacing: 0.1em;">POINTS</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 32px; text-align: center;">
            <a href="{{ route('home') }}" style="display: inline-block; background: var(--red); color: white; padding: 12px 32px; text-decoration: none; font-family: var(--display); font-size: 18px; letter-spacing: 0.08em;">
                Play Again
            </a>
        </div>
    </div>

    <div class="side">
        <div class="side-title">// Game Summary</div>
        <div style="background: #120F0B; border: 1px solid #2A2520; padding: 16px; margin-bottom: 20px;">
            <div style="font-family: var(--mono); font-size: 11px; color: #443C30; margin-bottom: 8px; letter-spacing: 0.1em; text-transform: uppercase;">Game Name</div>
            <div style="font-family: var(--serif); font-size: 14px; color: var(--code-fg); margin-bottom: 16px;">{{ $game->name }}</div>

            <div style="font-family: var(--mono); font-size: 11px; color: #443C30; margin-bottom: 8px; letter-spacing: 0.1em; text-transform: uppercase;">Players</div>
            <div style="font-family: var(--mono); font-size: 13px; color: var(--code-fg); margin-bottom: 16px;">{{ $game->players->count() }} total</div>

            <div style="font-family: var(--mono); font-size: 11px; color: #443C30; margin-bottom: 8px; letter-spacing: 0.1em; text-transform: uppercase;">Winner</div>
            <div style="font-family: var(--serif); font-size: 13px; color: var(--green);">{{ $leaderboard->first()?->nickname ?? 'N/A' }}</div>
        </div>
    </div>
</div>

</body>
</html>
