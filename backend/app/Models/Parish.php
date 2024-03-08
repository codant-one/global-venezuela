<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parish extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function municipality() {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
}
