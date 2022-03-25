<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Superhero extends Model
{
    use HasFactory;
    protected $table = 'superheros';
    protected $fillable = [
        'name',
        'fullName',
        'strength',
        'speed',
        'durability',
        'power',
        'combat',
        'race',
        'height/0',
        'height/1',
        'weight/0',
        'weight/1',
        'eyeColor',
        'hairColor',
        'publisher'
        														
    ];
}
