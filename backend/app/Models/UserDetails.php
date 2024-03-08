<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'user_id',
        'parish_id',
        'city_id',
        'gender_id',
        'phone',
        'address',
        'document'
    ];

    /**** Relationship ****/
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }

    public function parish() {
        return $this->belongsTo(Parish::class, 'parish_id', 'id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

     /**** Public methods ****/
    public static function updateOrCreateUser($request, $user) {
        $userD = UserDetails::updateOrCreate(
            [    'user_id' => $user->id ],
            [
                'city_id' => $request->city_id,
                'gender_id' => $request->gender_id ?? null,
                'parish_id' => $request->parish_id,
                'phone' => $request->phone ?? null,
                'address' => $request->address ?? null,
                'document' => $request->document ?? null
            ]
        );

        return $userD;
    }
}
