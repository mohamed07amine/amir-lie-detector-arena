<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Statements — Lie Detector Arena</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<style>
:root {
    --red: #C0392B; --red-bright: #E74C3C; --red-pale: #FADBD8;
    --green: #1E8449; --amber: #D35400;
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

.body { flex: 1; display: flex; justify-content: center; padding: 40px 32px; }
.center { width: 100%; max-width: 620px; }

.page-title {
    font-family: var(--display); font-size: 38px; color: var(--code-fg);
    letter-spacing: 0.04em; margin-bottom: 4px;
}
.page-sub {
    font-family: var(--mono); font-size: 12px; color: #443C30;
    margin-bottom: 32px; line-height: 1.6;
}

.statement-field {
    background: #120F0B; border: 1px solid #2A2520;
    padding: 20px; margin-bottom: 10px;
    position: relative; transition: border-color 0.15s;
}
.statement-field:focus-within { border-color: #443C30; }
.statement-field.is-lie { border-color: var(--red); }

.stmt-num {
    position: absolute; top: 10px; right: 16px;
    font-family: var(--display); font-size: 36px;
    color: rgba(192,57,43,0.15); line-height: 1; pointer-events: none;
}
.stmt-badge {
    display: inline-block; font-family: var(--mono); font-size: 9px;
    letter-spacing: 0.15em; text-transform: uppercase;
    padding: 3px 10px; background: var(--green); color: white; margin-bottom: 10px;
}
.stmt-badge.lie { background: var(--red); }

.stmt-input {
    width: 100%; background: transparent; border: none; outline: none;
    font-family: var(--mono); font-size: 14px; color: var(--code-fg);
    padding: 4px 0; resize: none; min-height: 56px; line-height: 1.6;
}
.stmt-input::placeholder { color: #2A2520; }
.stmt-char { font-family: var(--mono); font-size: 10px; color: #2A2520; margin-top: 6px; text-align: right; }
.stmt-error { font-family: var(--mono); font-size: 11px; color: var(--red); margin-top: 4px; }

.lie-selector { margin: 20px 0 24px; }
.lie-selector-label {
    font-family: var(--mono); font-size: 10px; color: var(--red);
    letter-spacing: 0.2em; text-transform: uppercase; display: block; margin-bottom: 10px;
}
.lie-options { display: flex; gap: 8px; }
.lie-option {
    flex: 1; padding: 12px 8px; background: #0E0B08;
    border: 1px solid #2A2520; font-family: var(--display); font-size: 18px;
    letter-spacing: 0.05em; color: #443C30; cursor: pointer;
    text-align: center; transition: all 0.15s; user-select: none;
}
.lie-option:hover { border-color: #443C30; color: #665A4A; }
.lie-option.selected { background: #1E0A08; border-color: var(--red); color: var(--red); }

.btn {
    width: 100%; background: var(--red); border: none; padding: 14px;
    font-family: var(--display); font-size: 24px; letter-spacing: 0.08em;
    color: white; cursor: pointer; transition: background 0.15s; margin-top: 8px;
}
.btn:hover { background: var(--red-bright); }

.global-errors {
    background: #1A0808; border-left: 3px solid var(--red);
    padding: 14px 18px; margin-bottom: 20px;
    font-family: var(--mono); font-size: 12px; color: var(--red); line-height: 2;
}
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-logo">🕵️ Lie Detector Arena</div>
    <div class="player-pill">Playing as <strong>{{ $player->nickname }}</strong></div>
</div>

<div class="view-label">
    <span>// SUBMIT YOUR STATEMENTS</span>
    <span>Only you can see this form — make your lie believable!</span>
</div>

<div class="body">
<div class="center">

    <div class="page-title">Your Turn, {{ $player->nickname }}</div>
    <div class="page-sub">
        Write 2 true statements and 1 lie about yourself.<br>
        The AI will analyze all three and try to find the lie.
    </div>

    @if($errors->any())
      <div class="global-errors">
        @foreach($errors->all() as $error)
          <div>✗ {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('statements.store') }}" method="POST" id="stmtForm">
      @csrf
      <input type="hidden" name="player_id" value="{{ $player->id }}" />
      <input type="hidden" name="lie_index" id="lieIndex" value="{{ old('lie_index', '') }}" />

      {{-- Statement 1 --}}
      <div class="statement-field" id="field-0">
        <div class="stmt-badge" id="badge-0">Statement 1</div>
        <div class="stmt-num">1</div>
        <textarea
          class="stmt-input"
          name="statements[0][content]"
          placeholder="Write your first statement here…"
          maxlength="200"
          required
          oninput="countChars(this, 'char-0')"
        >{{ old('statements.0.content') }}</textarea>
        <div class="stmt-char"><span id="char-0">0</span> / 200</div>
        @error('statements.0.content')
          <div class="stmt-error">✗ {{ $message }}</div>
        @enderror
      </div>

      {{-- Statement 2 --}}
      <div class="statement-field" id="field-1">
        <div class="stmt-badge" id="badge-1">Statement 2</div>
        <div class="stmt-num">2</div>
        <textarea
            class="stmt-input"
            name="statements[1][content]"
            placeholder="Write your second statement here…"
            maxlength="200"
            required
            oninput="countChars(this, 'char-1')"
        >{{ old('statements.1.content') }}</textarea>
        <div class="stmt-char"><span id="char-1">0</span> / 200</div>
        @error('statements.1.content')
    <div class="stmt-error">✗ {{ $message }}</div>
        @enderror
    </div>

    {{-- Statement 3 --}}
    <div class="statement-field" id="field-2">
        <div class="stmt-badge" id="badge-2">Statement 3</div>
        <div class="stmt-num">3</div>
        <textarea
    class="stmt-input"
    name="statements[2][content]"
    placeholder="Write your third statement here…"
    maxlength="200"
    required
    oninput="countChars(this, 'char-2')"
        >{{ old('statements.2.content') }}</textarea>
        <div class="stmt-char"><span id="char-2">0</span> / 200</div>
        @error('statements.2.content')
            <div class="stmt-error">✗ {{ $message }}</div>
        @enderror
        </div>

        {{-- Which one is the lie? --}}
        <div class="lie-selector">
        <span class="lie-selector-label">Which statement is your lie?</span>
        <div class="lie-options">
            <div class="lie-option {{ old('lie_index') == '0' ? 'selected' : '' }}" onclick="selectLie(0)">Statement 1</div>
            <div class="lie-option {{ old('lie_index') == '1' ? 'selected' : '' }}" onclick="selectLie(1)">Statement 2</div>
            <div class="lie-option {{ old('lie_index') == '2' ? 'selected' : '' }}" onclick="selectLie(2)">Statement 3</div>
        </div>
        @error('lie_index')
            <div class="stmt-error" style="margin-top:8px;">✗ {{ $message }}</div>
        @enderror
        </div>

        <button type="submit" class="btn">Submit to the Arena →</button>

    </form>
</div>
</div>

<script>
const lieOptions = document.querySelectorAll('.lie-option');
const lieIndex   = document.getElementById('lieIndex');

// Restore selected lie on validation fail
const current = lieIndex.value;
if (current !== '') selectLie(parseInt(current));

function selectLie(idx) {
    lieOptions.forEach((o, i) => {
    o.classList.toggle('selected', i === idx);
    const field = document.getElementById('field-' + i);
    const badge = document.getElementById('badge-' + i);
    if (i === idx) {
        field.classList.add('is-lie');
        badge.textContent = 'YOUR LIE 🔴';
        badge.classList.add('lie');
    } else {
        field.classList.remove('is-lie');
        badge.textContent = 'Statement ' + (i + 1);
        badge.classList.remove('lie');
    }
    });
    lieIndex.value = idx;
}

function countChars(el, spanId) {
    document.getElementById(spanId).textContent = el.value.length;
}

// Init char counts from old() values
document.querySelectorAll('.stmt-input').forEach((ta, i) => {
    document.getElementById('char-' + i).textContent = ta.value.length;
});

// Prevent submit without lie selected
document.getElementById('stmtForm').addEventListener('submit', function(e) {
    if (lieIndex.value === '') {
    e.preventDefault();
    alert('Please select which statement is your lie!');
    }
});
</script>

</body>
</html>
