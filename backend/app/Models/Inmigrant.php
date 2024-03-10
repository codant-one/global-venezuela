<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inmigrant extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    /**** Relationship ****/
    public function country() {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }

    public function parish() {
        return $this->belongsTo(Parish::class, 'parish_id', 'id');
    }

    public function community_council() {
        return $this->belongsTo(CommunityCouncil::class, 'community_council_id', 'id');
    }

    /**** Scopes ****/
    public function scopeWhereSearch($query, $search) {
        foreach (explode(' ', $search) as $term) {
            $query->where('name', 'LIKE', '%' . $term . '%')
                  ->orWhere('email', 'LIKE', '%' . $term . '%')
                  ->orWhere('phone', 'LIKE', '%' . $term . '%')
                  ->orWhere('address', 'LIKE', '%' . $term . '%');
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy) {
        $query->orderBy($orderByField, $orderBy);
    }
    
    public function scopeApplyFilters($query, array $filters) {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'created_at';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopePaginateData($query, $limit) {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    /**** Public methods ****/
    public static function createInmigrant($request) {
        $inmigrant = self::create([
            'parish_id' => $request->parish_id,
            'city_id' => $request->city_id,
            'name' => $request->name
        ]);

        return $inmigrant;
    }

    public static function updateInmigrant($request, $inmigrant) {
        $inmigrant->update([
            'parish_id' => $request->parish_id,
            'city_id' => $request->city_id,
            'name' => $request->name
        ]);

        return $inmigrant;
    }

    public static function deleteInmigrant($id) {
        self::deleteInmigrants(array($id));
    }

    public static function deleteInmigrants($ids) {
        foreach ($ids as $id) {
            $inmigrant = self::find($id);
            $inmigrant->delete();
        }
    }

}
