<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Parish;


class CommunityCouncil extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function Parish() {
        return $this->belongsTo(Parish::class, 'parish_id', 'id');
    }


     /**** Public methods ****/
     public static function createCommunityCouncil($request) {
        $council = self::create([
            'parish_id' => $request->parish_id,
            'name' => $request->name
        ]);

        return $council;
    }

    public static function updateCommunityCouncil($request, $council) {
        $council->update([
            'parish_id' => $request->parish_id,
            'name' => $request->name
        ]);

        return $council;
    }

    public static function deleteCommunityCouncil($id) {
        self::deleteCommunityCouncils(array($id));
    }

    public static function deleteCommunityCouncils($ids) {
        foreach ($ids as $id) {
            $council = self::find($id);
            $council->delete();
        }
    }


}
