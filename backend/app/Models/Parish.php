<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CommunityCouncil;

class Parish extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function municipality() {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }

     /**** Relationship ****/
     public function communitycouncil() {
        return $this->hasMany(CommunityCouncil::class, 'parish_id', 'id');
    }
}
