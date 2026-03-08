<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lobby — {{ $game->name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
@if($game->status === 'waiting')
    <meta http-equiv="refresh" content="5">
@endif
<style>
:root {
    --red: #C0392B; --red-bright: #E74C3C; --red-pale: #FADBD8;
    --amber: #D35400; --green: #1E8449; --green-pale: #D5F5E3;
    --ink: #1A1208; --code-bg: #1A1612; --code-fg: #E8DFC8;
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
.player-pill {
    background: #1E1812; border: 1px solid #2A2520;
    padding: 6px 14px; font-family: var(--mono); font-size: 12px; color: #887A68;
}
.player-pill strong { color: var(--code-fg); }
.view-label {
    background: var(--red); color: white;
    font-family: var(--mono); font-size: 11px; letter-spacing: 0.15em;
    text-transform: uppercase; padding: 6px 32px;
    display: flex; justify-content: space-between;
}
.view-label span { opacity: 0.6; font-size: 10px; }

.layout { flex: 1; display: grid; grid-template-columns: 1fr 300px; }
.main { padding: 32px; border-right: 1px solid #2A2520; }
.side { padding: 24px; background: #0E0B08; }

.section-head {
    font-family: var(--display); font-size: 30px; color: var(--code-fg);
    letter-spacing: 0.04em; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid #2A2520;
}
.section-head small {
    display: block; font-family: var(--mono); font-size: 10px;
    color: #443C30; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 2px;
}

.flash {
    padding: 10px 16px; margin-bottom: 20px;
    font-family: var(--mono); font-size: 12px; border-left: 3px solid;
}
.flash-success { background: #0A1A0E; border-color: var(--green); color: var(--green); }
.flash-error   { background: #1A0808; border-color: var(--red); color: var(--red); }

.player-list { list-style: none; margin-bottom: 28px; }
.player-list li {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 0; border-bottom: 1px solid #1E1812;
}
.player-list li:last-child { border-bottom: none; }
.avatar {
    width: 36px; height: 36px; background: var(--red);
    display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
}
.pname { font-family: var(--mono); font-size: 13px; color: var(--code-fg); flex: 1; }
.host-badge {
    font-family: var(--mono); font-size: 9px; letter-spacing: 0.1em;
    color: var(--amber); border: 1px solid var(--amber); padding: 2px 8px; margin-left: 8px;
}
.status-tag {
    font-family: var(--mono); font-size: 10px; letter-spacing: 0.1em;
    text-transform: uppercase; padding: 3px 8px; border: 1px solid;
}
.status-ready   { color: var(--green); border-color: var(--green); }
.status-waiting { color: #443C30; border-color: #2A2520; }
.status-done    { color: var(--amber); border-color: var(--amber); }

.btn {
    background: var(--red); border: none; padding: 13px 32px;
    font-family: var(--display); font-size: 22px; letter-spacing: 0.08em;
    color: white; cursor: pointer; transition: background 0.15s;
}
.btn:hover { background: var(--red-bright); }
.btn:disabled { background: #2A2520; color: #443C30; cursor: not-allowed; }

.hint { font-family: var(--mono); font-size: 11px; color: #443C30; margin-bottom: 14px; line-height: 1.6; }

.game-code-box {
    background: var(--ink); border: 1px solid #2A2520;
    padding: 16px; text-align: center; margin-bottom: 20px;
}
.code-label { font-family: var(--mono); font-size: 10px; color: #443C30; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 6px; }
.code-value { font-family: var(--display); font-size: 38px; color: var(--red); letter-spacing: 0.2em; }

.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 0; border-bottom: 1px solid #1E1812;
}
.info-row:last-child { border-bottom: none; }
.info-key { font-family: var(--mono); font-size: 11px; color: #443C30; letter-spacing: 0.08em; }
.info-val { font-family: var(--mono); font-size: 12px; color: var(--code-fg); }
.info-val.amber { color: var(--amber); }
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🕵️ Lie Detector Arena</div>
    <div class="player-pill">Playing as <strong>{{ $currentPlayer->nickname }}</strong></div>
</div>

<div class="view-label">
    <span>// GAME LOBBY — {{ $game->name }}</span>
    <span>Auto-refreshes every 5 seconds</span>
</div>

<div class="layout">
    <div class="main">

    @if(session('success'))
        <div class="flash flash-success">✓ &nbsp;{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">✗ &nbsp;{{ session('error') }}</div>
    @endif

    <div class="section-head">
        <small>// Players in arena ({{ $game->players->count() }})</small>
        {{ $game->name }}
    </div>

    <ul class="player-list">
        @foreach($game->players as $player)
        <li>
            <div class="avatar">{{ mb_substr($player->nickname, 0, 1) }}</div>
            <div class="pname">
            {{ $player->nickname }}
            @if($loop->first)
                <span class="host-badge">HOST</span>
            @endif
            </div>
            @if($player->statements->count() >= 3)
            <div class="status-tag status-done">Ready</div>
            @else
            <div class="status-tag status-waiting">Waiting</div>
            @endif
        </li>
        @endforeach
    </ul>

    @if($game->status === 'waiting' && $game->players->first() && $currentPlayer->id === $game->players->first()->id)
        {{-- Host controls --}}
        <p class="hint">// You are the host. Start the game when all players have joined.</p>
        <form action="{{ route('games.start', $game) }}" method="POST">
        @csrf
        <button type="submit" class="btn"
            {{ $game->players->count() < 2 ? 'disabled' : '' }}>
            Start Game ({{ $game->players->count() }} players) →
        </button>
        </form>
    @elseif($game->status === 'playing' && !$hasSubmitted)
        {{-- Submit statements --}}
        <p class="hint">// Submit 2 truths and 1 lie. Mark the lie with the checkbox.</p>
        <form action="{{ route('statements.store') }}" method="POST">
        @csrf
        <input type="hidden" name="game_id" value="{{ $game->id }}">
        <div style="margin-bottom: 20px;">
            <input type="text" name="statements[0][content]" placeholder="Statement 1" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #1A1612; border: 1px solid #2A2520; color: var(--code-fg);">
            <label><input type="checkbox" name="statements[0][is_lie]" value="1"> This is the lie</label>
        </div>
        <div style="margin-bottom: 20px;">
            <input type="text" name="statements[1][content]" placeholder="Statement 2" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #1A1612; border: 1px solid #2A2520; color: var(--code-fg);">
            <label><input type="checkbox" name="statements[1][is_lie]" value="1"> This is the lie</label>
        </div>
        <div style="margin-bottom: 20px;">
            <input type="text" name="statements[2][content]" placeholder="Statement 3" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #1A1612; border: 1px solid #2A2520; color: var(--code-fg);">
            <label><input type="checkbox" name="statements[2][is_lie]" value="1"> This is the lie</label>
        </div>
        <button type="submit" class="btn">Submit Statements →</button>
        </form>
    @elseif($game->status === 'playing' && $hasSubmitted)
        <p class="hint">// Waiting for other players to submit their statements…</p>
    @elseif($game->status === 'voting' && $currentPlayerStatements)
        @if($currentPlayer->id === $game->current_player_id)
            <p class="hint">// Waiting for other players to vote on your statements…</p>
        @elseif($hasVoted)
            <p class="hint">// You have voted. Waiting for others…</p>
        @else
            @if($currentPlayerStatements->first())
            <p class="hint">// Vote on which statement is the lie for {{ $currentPlayerStatements->first()->player->nickname }}.</p>
            <form action="{{ route('votes.store') }}" method="POST">
            @csrf
            @foreach($currentPlayerStatements as $statement)
            <div style="margin-bottom: 10px;">
                <label>
                    <input type="radio" name="statement_id" value="{{ $statement->id }}" required>
                    {{ $statement->content }}
                    @if($statement->ai_verdict)
                        <small style="color: #887A68;">(AI suspicion: {{ number_format($statement->ai_verdict['score'], 1) }}%)</small>
                    @endif
                </label>
            </div>
            @endforeach
            <button type="submit" class="btn">Submit Vote →</button>
            </form>
            @else
            <p class="hint">// No statements available yet. Waiting…</p>
            @endif
        @endif
    @elseif($game->status === 'finished')
        <a href="{{ route('games.results', $game) }}" class="btn">View Results</a>
    @else
        <p class="hint">// Waiting for the host to start the game…</p>
    @endif

    </div>

    <div class="side">
    <div class="game-code-box">
        <div class="code-label">Share this code</div>
        <div class="code-value">{{ $game->code }}</div>
    </div>

    <div style="font-family:var(--mono);font-size:10px;color:#443C30;letter-spacing:0.15em;text-transform:uppercase;margin-bottom:12px;">// Game Info</div>

    <div class="info-row">
        <span class="info-key">Status</span>
        <span class="info-val amber">{{ ucfirst($game->status) }}</span>
    </div>
    <div class="info-row">
        <span class="info-key">Players</span>
        <span class="info-val">{{ $game->players->count() }}</span>
    </div>
    <div class="info-row">
        <span class="info-key">Your Score</span>
        <span class="info-val amber">{{ $currentPlayer->score }} pts</span>
    </div>

    <div style="margin-top:24px;">
        <div style="font-family:var(--mono);font-size:10px;color:#443C30;letter-spacing:0.15em;text-transform:uppercase;margin-bottom:10px;">// How to play</div>
        <div style="font-family:var(--mono);font-size:11px;color:#443C30;line-height:2.2;">
        1. Each player submits<br>
        &nbsp;&nbsp;&nbsp;2 truths + 1 lie<br>
        2. AI judges suspicion %<br>
        3. Class votes on the lie<br>
        4. +150 correct · +100 bluff
        </div>
    </div>
    </div>
</div>

</body>
</html>
