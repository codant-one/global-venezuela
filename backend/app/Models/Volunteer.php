<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Theme;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Circuit;

class Volunteer extends Model
{
    use HasFactory;

    protected $guarded = [];

     /**** Relationship ****/

     public function theme() {
        return $this->belongsTo(Theme::class, 'theme_id', 'id');
    }

     public function state() {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function municipality() {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }

    public function circuit() {
        return $this->belongsTo(Circuit::class, 'circuit_id', 'id');
    }

    /**** Public methods ****/
    public static function createVolunteer($request) {
        $circuit = self::create([
            'theme_id' => $request->theme_id,
            'state_id' => $request->state_id,
            'municipality_id'=>$request->municipality_id,
            'circuit_id' => $request->circuit_id,
            'name' => $request->name,
            'last_name'=> $request->last_name,
            'document' => $request->document,
            'email' => $request->email,
            'phone' => $request->phone 
        ]);

        return $circuit;
    }
}
