<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $table = 'games';
    public $timestamps = false;
    protected $fillable = ['user', 'age', 'number','intents','time','state','evaluation','win'];
}
