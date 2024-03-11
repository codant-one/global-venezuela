<?php

namespace App\Http\Controllers;

use Spatie\Permission\Middlewares\PermissionMiddleware;
use Illuminate\Http\Request;

use App\Models\Migrant;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':ver reportes|administrador')->only(['index']);
        }

    public function reports(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $query = Migrant::with(['country', 'user', 'gender', 'community_council.circuit', 'parish.municipality.state'])
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

        $count = $query->applyFilters(
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

        $migrants = ($limit == -1) ? $query->paginate($query->count()) : $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [ 
            'migrants' => $migrants,
            'migrantsTotalCount' => $count
            ]
        ], 200);
    }
}
