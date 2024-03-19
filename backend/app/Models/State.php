<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class State extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function volunteers() {
        return $this->hasMany(Volunteer::class, 'state_id', 'id');
    }
}
