<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanType;
use Illuminate\Http\JsonResponse;

class LaporanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $laporanTypes = LaporanType::all();

            return response()->json([
                'success' => true,
                'message' => 'Laporan types retrieved successfully',
                'data' => $laporanTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve laporan types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
