<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Player;
use App\Models\Vote;
use Illuminate\Support\Str;

class GameController extends Controller
{

public function index()
{
    $games = Game::all();
    return view('welcome', compact('games')); 
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:60',
        'nickname' => 'required|string|max:30',
    ]);

    $code = strtoupper(Str::random(6));
    while (Game::where('code', $code)->exists()) {
        $code = strtoupper(Str::random(6));
    }

    $game = Game::create([
        'name' => $request->name,
        'code' => $code,
    ]);

    $player = Player::create([
        'game_id' => $game->id,
        'nickname' => $request->nickname,
        'score' => 0,
    ]);

    session(['player_id' => $player->id]);

    return redirect()->route('games.show', $game);
}

public function show(Game $game)
{
    $currentPlayer = Player::find(session('player_id'));

    if (!$currentPlayer || $currentPlayer->game_id !== $game->id) {
        return redirect()->route('home')->withErrors('You are not part of this game.');
    }

    $hasSubmitted = $currentPlayer->statements()->exists();

    $currentPlayerStatements = null;
    $hasVoted = false;
    if ($game->status === 'voting' && $game->current_player_id) {
        $currentPlayerObj = Player::find($game->current_player_id);
        $currentPlayerStatements = $currentPlayerObj->statements;
        // Check if current player has voted on this round
        $hasVoted = Vote::where('voter_id', $currentPlayer->id)
            ->whereIn('statement_id', $currentPlayerStatements->pluck('id'))
            ->exists();
    }

    return view('games.show', compact('game', 'currentPlayer', 'hasSubmitted', 'currentPlayerStatements', 'hasVoted'));
}

public function start(Game $game)
{
    $currentPlayer = Player::find(session('player_id'));

    if (!$currentPlayer || $currentPlayer->game_id !== $game->id) {
        return redirect()->route('home')->withErrors('You are not part of this game.');
    }

    if ($currentPlayer->id !== $game->players->first()->id) {
        return back()->withErrors('Only the host can start the game.');
    }

    $game->update(['status' => 'playing', 'current_player_id' => $game->players()->first()->id]);

    return redirect()->route('games.show', $game);
}

public function join(Request $request)
{
    $request->validate([
        'nickname' => 'required|string|max:30',
        'code' => 'required|string|size:6',
    ]);

    $game = Game::where('code', strtoupper($request->code))->first();

    if (!$game) {
        return back()->withErrors(['code' => 'Game not found.']);
    }

    // Check if already in this game
    $currentPlayer = Player::find(session('player_id'));
    if ($currentPlayer && $currentPlayer->game_id == $game->id) {
        return redirect()->route('games.show', $game)->withErrors('You are already in this game.');
    }

    $player = Player::create([
        'game_id' => $game->id,
        'nickname' => $request->nickname,
        'score' => 0,
    ]);

    session(['player_id' => $player->id]);

    return redirect()->route('games.show', $game);
}

public function results(Game $game)
{
    $leaderboard   = $game->players()->orderByDesc('score')->get();
    $currentPlayer = $game->players()->find($game->current_player_id) ?? $game->players()->first();

    return view('games.results', compact('game', 'leaderboard', 'currentPlayer'));
}
    public function calculateRoundScores(Player $currentPlayer): void
{
    $lieStatement = $currentPlayer->statements
        ->firstWhere('is_lie', true);

    // Voters who guessed correctly get +150
    foreach ($lieStatement->votes as $vote) {
        $vote->voter->increment('score', 150);
    }

    // Author gets +100 for each wrong vote (successful bluff)
    $wrongVotes = $currentPlayer->statements
        ->where('is_lie', false)
        ->flatMap->votes
        ->count();

    $currentPlayer->increment('score', $wrongVotes * 100);
}
}
