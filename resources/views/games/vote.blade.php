<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote — Round {{ $round }} — Lie Detector Arena</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
@if($game->status === 'playing')
    <meta http-equiv="refresh" content="5">
@endif
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
.round-tag {
    font-family: var(--mono); font-size: 11px; color: var(--amber);
    border: 1px solid var(--amber); padding: 4px 12px; letter-spacing: 0.1em;
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

.round-header { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.round-num { font-family: var(--mono); font-size: 10px; color: var(--amber); border: 1px solid var(--amber); padding: 3px 10px; letter-spacing: 0.15em; text-transform: uppercase; }
.round-player { font-family: var(--display); font-size: 36px; color: var(--code-fg); letter-spacing: 0.04em; }
.round-hint { font-family: var(--mono); font-size: 11px; color: #443C30; margin-bottom: 24px; }

.flash {
    padding: 10px 16px; margin-bottom: 20px;
    font-family: var(--mono); font-size: 12px; border-left: 3px solid;
}
.flash-error { background: #1A0808; border-color: var(--red); color: var(--red); }

.statement-card {
    background: #120F0B; border: 1px solid #2A2520;
    padding: 20px 22px; margin-bottom: 10px;
    display: flex; align-items: center; gap: 18px;
    cursor: pointer; transition: all 0.15s; position: relative;
}
.statement-card:hover:not(.disabled) { border-color: #443C30; background: #160D0A; }
.statement-card.selected { border-color: var(--red); background: #1E0A08; }
.statement-card.disabled { cursor: default; opacity: 0.6; }

.stmt-n {
    font-family: var(--display); font-size: 50px;
    color: rgba(192,57,43,0.12); line-height: 1; flex-shrink: 0; width: 48px;
    text-align: center; transition: color 0.15s;
}
.statement-card.selected .stmt-n { color: rgba(192,57,43,0.35); }
.stmt-text {
    font-family: var(--mono); font-size: 14px; color: #665A4A;
    line-height: 1.6; flex: 1; transition: color 0.15s;
}
.statement-card.selected .stmt-text { color: var(--code-fg); }

.vote-circle {
    width: 24px; height: 24px; border-radius: 50%; border: 2px solid #2A2520;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    transition: all 0.15s;
}
.statement-card.selected .vote-circle { border-color: var(--red); background: var(--red); }
.vote-circle::after {
    content: ''; width: 8px; height: 8px; border-radius: 50%;
    background: white; opacity: 0; transition: opacity 0.15s;
}
.statement-card.selected .vote-circle::after { opacity: 1; }

.vote-actions { margin-top: 22px; display: flex; align-items: center; gap: 16px; }
.btn {
    background: var(--red); border: none; padding: 12px 32px;
    font-family: var(--display); font-size: 22px; letter-spacing: 0.08em;
    color: white; cursor: pointer; transition: background 0.15s;
}
.btn:hover { background: var(--red-bright); }
.already-voted {
    font-family: var(--mono); font-size: 12px; color: var(--green);
    background: #0A1A0E; border: 1px solid var(--green);
    padding: 10px 18px; display: flex; align-items: center; gap: 8px;
}

.side-title {
    font-family: var(--mono); font-size: 10px; color: #443C30;
    letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 14px;
}

.tally { margin-bottom: 24px; }
.tally-row {
    display: flex; align-items: center; gap: 10px; padding: 8px 0;
    border-bottom: 1px solid #1E1812;
}
.tally-row:last-child { border-bottom: none; }
.tally-name { font-family: var(--mono); font-size: 12px; color: #665A4A; flex: 1; }
.tally-bar-wrap { width: 80px; height: 4px; background: #1E1812; border-radius: 2px; }
.tally-bar { height: 100%; background: var(--red); border-radius: 2px; transition: width 0.4s; }
.tally-n { font-family: var(--mono); font-size: 11px; color: #443C30; width: 20px; text-align: right; }

.leaderboard { list-style: none; }
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
.lb-score { font-family: var(--display); font-size: 20px; color: var(--amber); }
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🕵️ Lie Detector Arena</div>
    <div class="topbar-right">
    <div class="player-pill">Playing as <strong>{{ $currentPlayer->nickname }}</strong></div>
    <div class="round-tag">Round {{ $round }} of {{ $totalRounds }}</div>
    </div>
</div>

<div class="view-label">
    <span>// VOTE — WHICH ONE IS THE LIE?</span>
    @if(!$hasVoted)
    <span>Auto-refreshes every 5 seconds</span>
    @else
    <span>You already voted this round</span>
    @endif
</div>

<div class="layout">
    <div div class="main">

    @if(session('error'))
        <div class="flash flash-error">✗ &nbsp;{{ session('error') }}</div>
    @endif

    <div class="round-header">
        <div class="round-num">Round {{ $round }}</div>
        <div class="round-player">{{ $roundPlayer->nickname }}'s Statements</div>
    </div>
    <div class="round-hint">// Read carefully. One of these is a lie. {{ $hasVoted ? 'You have already voted.' : 'Click to select, then submit.' }}</div>

    @if(!$hasVoted && $currentPlayer->id !== $roundPlayer->id)
        <form action="{{ route('votes.store') }}" method="POST" id="voteForm">
        @csrf
        <input type="hidden" name="voter_id" value="{{ $currentPlayer->id }}" />
        <input type="hidden" name="statement_id" id="selectedStmt" value="" />

        @foreach($statements as $stmt)
            <div class="statement-card" onclick="selectStmt(this, '{{ $stmt->id }}')">
            <div class="stmt-n">{{ $loop->iteration }}</div>
            <div class="stmt-text">{{ $stmt->content }}</div>
            <div class="vote-circle"></div>
            </div>
        @endforeach

        <div class="vote-actions">
            <button type="submit" class="btn">Cast My Vote →</button>
        </div>
        </form>
    @else
        {{-- Show statements without voting controls --}}
        @foreach($statements as $stmt)
        <div class="statement-card disabled">
            <div class="stmt-n">{{ $loop->iteration }}</div>
            <div class="stmt-text">{{ $stmt->content }}</div>
            <div class="vote-circle"></div>
        </div>
        @endforeach

        @if($currentPlayer->id === $roundPlayer->id)
        <div class="already-voted" style="margin-top:20px;">
            🎭 &nbsp;These are your statements — sit back and watch!
        </div>
        @else
        <div class="already-voted" style="margin-top:20px;">
            ✓ &nbsp;Vote submitted — waiting for others…
        </div>
        @endif
    @endif

    </div>

    <div class="side">
    <div class="side-title">// Votes Cast</div>

    <div class="tally">
        @php $totalVotes = $statements->sum(fn($s) => $s->votes->count()); @endphp
        @foreach($statements as $stmt)
        @php $vCount = $stmt->votes->count(); $pct = $totalVotes > 0 ? ($vCount / $totalVotes) * 100 : 0; @endphp
        <div class="tally-row">
            <span class="tally-name">Statement {{ $loop->iteration }}</span>
            <div class="tally-bar-wrap"><div class="tally-bar" style="width: <?php echo $pct; ?>%"></div></div>
            <span class="tally-n">{{ $vCount }}</span>
        </div>
        @endforeach
    </div>

    <div class="side-title">// Leaderboard</div>
    <ul class="leaderboard">
        @foreach($game->players->sortByDesc('score') as $player)
        <li class="lb-row">
            <div class="lb-pos">{{ $loop->iteration }}</div>
            <div class="lb-avatar">{{ mb_substr($player->nickname, 0, 1) }}</div>
            <div class="lb-name">{{ $player->nickname }}</div>
            <div class="lb-score">{{ $player->score }}</div>
        </li>
        @endforeach
    </ul>
    </div>
</div>

<script>
function selectStmt(card, id) {
    document.querySelectorAll('.statement-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('selectedStmt').value = id;
}

document.getElementById('voteForm')?.addEventListener('submit', function(e) {
    if (!document.getElementById('selectedStmt').value) {
    e.preventDefault();
    alert('Please select a statement before voting!');
    }
});
</script>

</body>
</html>
