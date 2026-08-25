<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Charge;

class ChargesController extends Controller
{
    public function getCharges()
    {
        $charges = Charge::where('charge_status', 1)->get();

        if ($charges->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active charges found',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Charges fetched successfully',
            'data' => $charges
        ]);
    }
}
