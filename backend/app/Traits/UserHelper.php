<?php

namespace App\Traits;

use App\Models\UserDetails;
use App\Models\UserMenu;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
/**
 * Trait for models with stores
 */
trait UserHelper
{
    /**** Relationship ****/
    public function menu(){
        return $this->hasOne(UserMenu::class, 'user_id');
    }

    public function userDetail() {
        return $this->hasOne(UserDetails::class, 'user_id', 'id');
    }

    public function gender() {
        return $this->hasOne(Gender::class, 'gender_id', 'id');
    }

    /**** Public methods ****/
    public function getOnlineAttribute($value) {
        if($value!=null)
            return $this->asDateTime($value);
        else
            return $value;
    }

    public static function updateProfile($request, $user) {
        $user->update([
            'last_name' => $request->last_name,
            'username' => Str::slug($user->name . ' ' . $request->last_name . ' ' . $user->id),
            'full_profile' => true
        ]);

        UserDetails::updateOrCreateUser($request, $user);
        
        return $user;
    }

    public static function createUser($request) {
        $user = self::create([
            'name' => $request->name,
            'last_name' =>  $request->last_name,
            'username' => Str::slug($request->name . ' ' . $request->last_name . ' ' . rand(1,100)),
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        UserDetails::updateOrCreateUser($request, $user);

        return $user;
    }

    public static function updateUser($request, $user) {
        $user->update([
            'name' => $request->name,
            'last_name' =>  $request->last_name,
            'email' => $request->email
        ]);
        
        UserDetails::updateOrCreateUser($request, $user);

        return $user;
    }

    public static function deleteUser($id) {
        $user = self::find($id);
        $user->roles()->detach();
        $user->delete();
    }

    public static function getOnline($request) {

        $users = self::select('id','online')
                     ->whereIn('id', explode(',', $request->ids))
                     ->get();

        return $users;
    }

    public static function updateAvatar($request, $user) {

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $path = 'avatars/';

            $file_data = uploadFile($image, $path, $user->avatar);

            $user->update([
                'avatar' => $file_data['filePath']
            ]);
        }
        
        return $user;
    }

    /**** Scopes ****/
    public function scopeWhereSearch($query, $search) {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('roles', function ($q) use ($term) {
                $q->where('name', 'LIKE', '%' . $term . '%');
            })
            ->orWhere('name', 'LIKE', '%' . $term . '%')
            ->orWhere('email', 'LIKE', '%' . $term . '%');
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

}
