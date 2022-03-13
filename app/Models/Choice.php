<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    // protected $casts = [
    //     'choice_number' => 'integer',
    // ];
}
