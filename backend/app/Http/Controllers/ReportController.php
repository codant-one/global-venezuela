<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inmigrant;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':ver reportes|administrador')->only(['index']);
        }

    public function reports(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Inmigrant::with(['country', 'user', 'gender', 'community_council.circuit', 'parish.municipality.state'])
                        ->where('user_id', auth()->user()->id)
                        ->applyFilters(
                            $request->only([
                                'search',
                                'country_id',
                                'state_id',
                                'municipality_id',
                                'parish_id',
                                'passport_status',
                                'antecedents',
                                'transient',
                                'resident',
                                'orderByField',
                                'orderBy'
                            ])
                        );

        $count = $query->where('user_id', auth()->user()->id)
                        ->applyFilters(
                            $request->only([
                                'search',
                                'country_id',
                                'state_id',
                                'municipality_id',
                                'parish_id',
                                'passport_status',
                                'antecedents',
                                'transient',
                                'resident',
                                'orderByField',
                                'orderBy'
                            ])
                        )->count();

        $inmigrants = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
            'inmigrants' => $inmigrants,
            'inmigrantsTotalCount' => $count
            ]
        ], 200);
    }
}
