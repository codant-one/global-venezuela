<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Circuit;

class CommunityCouncil extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**** Relationship ****/
    public function circuit() {
        return $this->belongsTo(Circuit::class, 'circuit_id', 'id');
    }

    /**** Scopes ****/
    public function scopeWhereSearch($query, $search) {
        foreach (explode(' ', $search) as $term) {
            $query->where('name', 'LIKE', '%' . $term . '%');
        }
    }

    public function scopeWhereState($query, $search) {
        $query->whereHas('circuit', function ($q) use ($search) {
            $q->whereHas('parish', function ($q) use ($search) {
                $q->whereHas('municipality', function ($q) use ($search) {
                    $q->whereHas('state', function ($q) use ($search) {
                        $q->where('id', $search);
                    });
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
    public static function createCommunityCouncil($request) {
        $council = self::create([
            'circuit_id' => $request->circuit_id,
            'name' => $request->name
        ]);

        return $council;
    }

    public static function updateCommunityCouncil($request, $council) {
        $council->update([
            'circuit_id' => $request->circuit_id,
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
