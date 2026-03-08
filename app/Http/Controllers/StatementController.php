<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Statement;
use App\Models\Player;
use App\Models\Game;
use App\Services\LieDetectorService;

class StatementController extends Controller
{
    public function store(Request $request, LieDetectorService $detector)
{
    $data = $request->validate([
        'game_id'              => 'required|exists:games,id',
        'statements'           => 'required|array|size:3',
        'statements.*.content' => 'required|string|max:200',
        'statements.*.is_lie'  => 'nullable|boolean',
    ]);

    $player = Player::find(session('player_id'));

    if (!$player || $player->game_id != $data['game_id']) {
        return back()->withErrors('Invalid request.');
    }

    // Prevent multiple submissions
    if ($player->statements()->exists()) {
        return back()->withErrors('You have already submitted your statements.');
    }

    // Check that exactly one is marked as lie
    $lieCount = collect($data['statements'])->where('is_lie', true)->count();
    if ($lieCount !== 1) {
        return back()->withErrors('You must mark exactly one statement as the lie.');
    }

    // Save all 3 statements to the database
    $statements = [];
    foreach ($data['statements'] as $stmt) {
        $statements[] = Statement::create([
            'player_id' => $player->id,
            'content'   => $stmt['content'],
            'is_lie'    => $stmt['is_lie'] ?? false,
        ]);
    }

    // Call Claude AI and store verdict
    try {
        $verdict = $detector->analyze($data['statements']);
        foreach ($statements as $i => $s) {
            $s->update(['ai_verdict' => [
                'score'    => $verdict['scores'][$i],
                'ai_guess' => ($i + 1) === $verdict['lie_index'],
                'reasoning'=> $verdict['reasoning'],
            ]]);
        }
    } catch (\Exception $e) {
        // Graceful fallback — game continues without AI verdict
        logger()->error('AI failed: ' . $e->getMessage());
    }

    // Check if all players have submitted
    $game = $player->game()->first(); // Ensure game is loaded
    $game->load('players'); // Load players collection
    $allSubmitted = $game->players->every(function ($p) {
        return $p->statements()->count() === 3;
    });
    if ($allSubmitted) {
        $game->update([
            'status' => 'voting',
            'current_player_id' => $game->players()->first()->id
        ]);
    }

    return redirect()->route('games.show', $player->game_id)
                    ->with('success', 'Statements submitted!');
    }
}
