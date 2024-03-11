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

    public function scopeWhereState($query, $search) {
        $query->whereHas('parish.municipality.state', function ($q) use ($search) {
            $q->where('state_id', $search);
        });
    }

    public function scopeWhereMunicipality($query, $search) {
        $query->whereHas('parish.municipality', function ($q) use ($search) {
            $q->where('state_id', $search);
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

        if ($filters->get('country_id') !== null) {
            $query->where('country_id', $filters->get('country_id'));
        }

        if($filters->get('state_id')!==null){
            $query->whereState($filters->get('state_id'));
        }

        if($filters->get('municipality_id')!==null){
            $query->whereMunicipality($filters->get('municipality_id'));
        }

        if($filters->get('parish_id')!==null){
            $query->where('parish_id',$filters->get('parish_id'));
        }

        if($filters->get('passport_status')!=null)
        {
            $query->where('passport_status', $filters->get('passport_status'));
        }

        if($filters->get('antecedents')!=null)
        {
            $query->where('antecedents', $filters->get('antecedents'));
        }

        if($filters->get('transient')!=null)
        {
            $query->where('transient', $filters->get('transient'));
        }

        if($filters->get('resident')!=null)
        {
            $query->where('resident', $filters->get('resident'));
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
            'country_id' => $request->country_id,
            'user_id' => auth()->user()->id,
            'gender_id' => $request->gender_id,
            'parish_id' => $request->parish_id,
            'community_council_id' => ($request->community_council_id === '0') ? null : $request->community_council_id,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'passport_number' => $request->passport_number,
            'transient' => $request->transient,
            'resident' => $request->resident,
            'years_in_country' => $request->years_in_country,
            'antecedents' => $request->antecedents,
            'isMarried' => $request->isMarried,
            'has_children' => $request->has_children,
            'children_number' => ($request->children_number === '0') ? null : $request->children_number,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return $inmigrant;
    }

    public static function updateInmigrant($request, $inmigrant) {
        $inmigrant->update([
            'country_id' => $request->country_id,
            'gender_id' => $request->gender_id,
            'parish_id' => $request->parish_id,
            'community_council_id' => ($request->community_council_id === '0') ? null : $request->community_council_id,
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'passport_number' => $request->passport_number,
            'transient' => $request->transient,
            'resident' => $request->resident,
            'years_in_country' => $request->years_in_country,
            'antecedents' => $request->antecedents,
            'isMarried' => $request->isMarried,
            'has_children' => $request->has_children,
            'children_number' => ($request->children_number === '0') ? null : $request->children_number,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return $inmigrant;
    }

    public static function deleteInmigrants($ids) {
        foreach ($ids as $id) {
            $inmigrant = self::find($id);
            $inmigrant->delete();

            if($inmigrant->file_document)
                deleteFile($inmigrant->file_document);
        }
    }

}
