<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['name', 'code', 'status', 'current_player_id'];

    // Game.php
public function players() {
    return $this->hasMany(Player::class);
}
}
