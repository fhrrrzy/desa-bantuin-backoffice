<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InformationResource;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Information::with('laporanType');

            // Filter by laporan_type_id if provided
            if ($request->has('laporan_type_id')) {
                $query->where('laporan_type_id', $request->laporan_type_id);
            }

            // Search by title if provided
            if ($request->has('search') && $request->search) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $information = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Information retrieved successfully',
                'data' => InformationResource::collection($information),
                'pagination' => [
                    'current_page' => $information->currentPage(),
                    'last_page' => $information->lastPage(),
                    'per_page' => $information->perPage(),
                    'total' => $information->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'laporan_type_id' => 'required|exists:laporan_types,id',
                'attachment.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            ]);

            $attachmentPaths = [];
            
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    // Preserve original filename with extension
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    
                    // Generate unique filename while preserving extension
                    $uniqueFilename = $filename . '_' . uniqid() . '.' . $extension;
                    $path = $file->storeAs('information-attachments', $uniqueFilename, 'public');
                    $attachmentPaths[] = $path;
                }
            }

            $validated['attachment'] = $attachmentPaths;

            $information = Information::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Information created successfully',
                'data' => new InformationResource($information->load('laporanType'))
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Information $information): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Information retrieved successfully',
                'data' => new InformationResource($information->load('laporanType'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Information $information): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'laporan_type_id' => 'sometimes|required|exists:laporan_types,id',
                'attachment.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            ]);

            if ($request->hasFile('attachment')) {
                $attachmentPaths = [];
                
                foreach ($request->file('attachment') as $file) {
                    // Preserve original filename with extension
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    
                    // Generate unique filename while preserving extension
                    $uniqueFilename = $filename . '_' . uniqid() . '.' . $extension;
                    $path = $file->storeAs('information-attachments', $uniqueFilename, 'public');
                    $attachmentPaths[] = $path;
                }
                
                $validated['attachment'] = $attachmentPaths;
            }

            $information->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Information updated successfully',
                'data' => new InformationResource($information->load('laporanType'))
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information $information): JsonResponse
    {
        try {
            $information->delete();

            return response()->json([
                'success' => true,
                'message' => 'Information deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get information by laporan type.
     */
    public function getByLaporanType($laporanTypeId): JsonResponse
    {
        try {
            $information = Information::where('laporan_type_id', $laporanTypeId)
                ->with('laporanType')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Information retrieved successfully',
                'data' => InformationResource::collection($information)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
