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
    public static function createVolunteer($request) {
        $volunteer = self::create([
            'theme_id' => $request->theme_id,
            'state_id' => $request->state_id,
            'municipality_id'=>$request->municipality_id,
            'circuit_id' => $request->circuit_id,
            'name' => $request->name,
            'document' => $request->document,
            'email' => $request->email,
            'phone' => $request->phone 
        ]);

        return $volunteer;
    }

    public static function updateVolunteer($request, $volunteer) {
        $volunteer->update([
            'theme_id' => $request->theme_id,
            'state_id' => $request->state_id,
            'municipality_id'=>$request->municipality_id,
            'circuit_id' => $request->circuit_id,
            'name' => $request->name,
            'document' => $request->document,
            'email' => $request->email,
            'phone' => $request->phone 
        ]);

        return $volunteer;
    }

    public static function registerVolunteer($request) {

        if($request->responsible['name'] !== null) {
            self::create([
                'theme_id' => $request->theme_id,
                'state_id' => $request->type === '1' ? $request->state_id : null,
                'municipality_id' => $request->type === '2' ? $request->municipality_id : null,
                'circuit_id' => $request->type === '3' ? $request->circuit_id : null,
                'isResponsible' => 1,
                'name' => $request->responsible['name'],
                'document' => $request->responsible['document'],
                'email' => $request->responsible['email'],
                'phone' => $request->responsible['phone'] 
            ]);
        }

        foreach($request->form as $volunteer){
            self::create([
                'theme_id' => $request->theme_id,
                'state_id' => $request->type === '1' ? $request->state_id : null,
                'municipality_id' => $request->type === '2' ? $request->municipality_id : null,
                'circuit_id' => $request->type === '3' ? $request->circuit_id : null,
                'isResponsible' => 0,
                'name' => $volunteer['name'],
                'document' => $volunteer['document'],
                'email' => $volunteer['email'],
                'phone' => $volunteer['phone'] 
            ]);
        }

        return $volunteer;
    }

    public static function deleteVolunteer($id) {
        self::deleteVolunteers(array($id));
    }

    public static function deleteVolunteers($ids) {
        foreach ($ids as $id) {
            $volunteer = self::find($id);
            $volunteer->delete();
        }
    }
}
