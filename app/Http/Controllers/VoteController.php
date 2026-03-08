<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Statement;
use App\Models\Vote;
use App\Models\Player;

class VoteController extends Controller
{
    public function store(Request $request)
{
    $data = $request->validate([
        'statement_id' => 'required|exists:statements,id',
    ]);

    $voter = Player::find(session('player_id'));
    $statement = Statement::findOrFail($data['statement_id']);

    if ($voter->game_id !== $statement->player->game_id) {
        return back()->withErrors('Invalid vote.');
    }

    // Prevent double voting on same player's round
    $alreadyVoted = Vote::where('voter_id', $voter->id)
        ->whereIn('statement_id',
            Statement::where('player_id', $statement->player_id)->pluck('id')
        )->exists();

    if ($alreadyVoted) {
        return back()->with('error', 'You already voted this round!');
    }

    Vote::create([
        'voter_id' => $voter->id,
        'statement_id' => $data['statement_id'],
    ]);

    // Check if all other players voted
    $game = $statement->player->game;
    $otherPlayers = $game->players->where('id', '!=', $statement->player_id);
    $allVoted = $otherPlayers->every(function ($p) use ($statement) {
        return Vote::where('voter_id', $p->id)
            ->whereIn('statement_id', Statement::where('player_id', $statement->player_id)->pluck('id'))
            ->exists();
    });

    if ($allVoted) {
        // Calculate scores for this round
        $this->calculateRoundScores($statement->player);

        // Move to next player
        $players = $game->players->sortBy('id');
        $currentIndex = $players->search(fn($p) => $p->id == $game->current_player_id);
        $nextIndex = $currentIndex + 1;
        if ($nextIndex < $players->count()) {
            $game->update(['current_player_id' => $players[$nextIndex]->id]);
        } else {
            // All rounds done
            $game->update(['status' => 'finished']);
        }
    }

    return redirect()->route('games.show', $game)
                    ->with('success', 'Vote submitted!');
}

    private function calculateRoundScores(Player $currentPlayer): void
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
