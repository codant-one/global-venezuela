<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Circuit extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function parish() {
        return $this->belongsTo(Parish::class, 'parish_id', 'id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    /**** Scopes ****/
    public function scopeWhereSearch($query, $search) {
        foreach (explode(' ', $search) as $term) {
            $query->where('name', 'LIKE', '%' . $term . '%');
        }
    }

    public function scopeWhereState($query, $search) {
        $query->whereHas('parish', function ($q) use ($search) {
            $q->whereHas('municipality', function ($q) use ($search) {
                $q->whereHas('state', function ($q) use ($search) {
                    $q->where('id', $search);
                });
            });
        });
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy) {
        $query->orderBy($orderByField, $orderBy);
    }
    
    public function scopeApplyFilters($query, array $filters) {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('state_id')) {
            $query->whereState($filters->get('state_id'));
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
    public static function createCircuit($request) {
        $circuit = self::create([
            'parish_id' => $request->parish_id,
            'city_id' => $request->city_id,
            'name' => $request->name
        ]);

        return $circuit;
    }

    public static function updateCircuit($request, $circuit) {
        $circuit->update([
            'parish_id' => $request->parish_id,
            'city_id' => $request->city_id,
            'name' => $request->name
        ]);

        return $circuit;
    }

    public static function deleteCircuit($id) {
        self::deleteCircuits(array($id));
    }

    public static function deleteCircuits($ids) {
        foreach ($ids as $id) {
            $circuit = self::find($id);
            $circuit->delete();
        }
    }
}
