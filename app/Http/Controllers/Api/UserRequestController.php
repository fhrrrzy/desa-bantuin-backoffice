<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Filament\Notifications\Notification;

class UserRequestController extends Controller
{
    /**
     * Transform lampiran array to array of objects with filename, url, and path
     */
    private function transformLampiran($lampiran)
    {
        if (!is_array($lampiran)) {
            return [];
        }
        return array_map(function ($path) {
            return [
                'filename' => basename($path),
                'url' => url('storage/' . $path),
                'path' => $path,
            ];
        }, $lampiran);
    }

    /**
     * Get authenticated user's requests
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');
            $type = $request->get('type');

            $query = UserRequest::where('user_id', $user->id)
                ->with(['laporanType'])
                ->orderBy('created_at', 'desc');

            // Filter by status if provided
            if ($status) {
                $query->where('status', $status);
            }

            // Filter by type if provided
            if ($type) {
                $query->where('type', $type);
            }

            $requests = $query->paginate($perPage);

            // Transform the data for API response
            $requests->getCollection()->transform(function ($request) {
                return [
                    'id' => $request->id,
                    'laporan_type' => [
                        'id' => $request->laporanType->id,
                        'name' => $request->laporanType->name,
                    ],
                    'title' => $request->title,
                    'type' => $request->type,
                    'description' => $request->description,
                    'status' => $request->status,
                    'return_message' => $request->return_message,
                    'lampiran' => $this->transformLampiran($request->lampiran),
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data permintaan berhasil diambil',
                'data' => [
                    'requests' => $requests->items(),
                    'pagination' => [
                        'current_page' => $requests->currentPage(),
                        'last_page' => $requests->lastPage(),
                        'per_page' => $requests->perPage(),
                        'total' => $requests->total(),
                        'from' => $requests->firstItem(),
                        'to' => $requests->lastItem(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific user request by ID (only if it belongs to the authenticated user)
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            $userRequest = UserRequest::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['laporanType'])
                ->first();

            if (!$userRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data permintaan berhasil diambil',
                'data' => [
                    'request' => [
                        'id' => $userRequest->id,
                        'laporan_type' => [
                            'id' => $userRequest->laporanType->id,
                            'name' => $userRequest->laporanType->name,
                        ],
                        'title' => $userRequest->title,
                        'type' => $userRequest->type,
                        'description' => $userRequest->description,
                        'status' => $userRequest->status,
                        'return_message' => $userRequest->return_message,
                        'lampiran' => $this->transformLampiran($userRequest->lampiran),
                        'created_at' => $userRequest->created_at,
                        'updated_at' => $userRequest->updated_at,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new user request
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'laporan_type_id' => 'required|exists:laporan_types,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:permintaan,pelaporan',
            'description' => 'required|string',
            'lampiran' => 'nullable|array',
            'lampiran.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ], [
            'laporan_type_id.required' => 'Jenis laporan wajib dipilih',
            'laporan_type_id.exists' => 'Jenis laporan tidak valid',
            'title.required' => 'Judul wajib diisi',
            'title.string' => 'Judul harus berupa teks',
            'title.max' => 'Judul maksimal 255 karakter',
            'type.required' => 'Tipe permintaan wajib dipilih',
            'type.in' => 'Tipe permintaan harus permintaan atau pelaporan',
            'description.required' => 'Deskripsi wajib diisi',
            'lampiran.array' => 'Lampiran harus berupa array',
            'lampiran.*.file' => 'File lampiran tidak valid',
            'lampiran.*.mimes' => 'Format file tidak didukung',
            'lampiran.*.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['user_id'] = $request->user()->id;

            // Handle file uploads - only process valid files
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    if ($file && $file->isValid()) {
                        // Preserve original filename with extension
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $filename = pathinfo($originalName, PATHINFO_FILENAME);
                        
                        // Generate unique filename while preserving extension
                        $uniqueFilename = $filename . '_' . uniqid() . '.' . $extension;
                        $path = $file->storeAs('lampiran', $uniqueFilename, 'public');
                        $lampiranPaths[] = $path;
                    }
                }
            }
            $data['lampiran'] = $lampiranPaths;

            $userRequest = UserRequest::create($data);

            // Send notification to all admin users
            $adminUsers = User::where('role', 'admin')->get();
            
            foreach ($adminUsers as $adminUser) {
                Notification::make()
                    ->title('Permintaan Baru')
                    ->body("Permintaan baru dari {$userRequest->user->name}: {$userRequest->title}")
                    ->info()
                    ->sendToDatabase($adminUser);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dibuat',
                'data' => [
                    'request' => [
                        'id' => $userRequest->id,
                        'laporan_type' => [
                            'id' => $userRequest->laporanType->id,
                            'name' => $userRequest->laporanType->name,
                        ],
                        'title' => $userRequest->title,
                        'type' => $userRequest->type,
                        'description' => $userRequest->description,
                        'status' => $userRequest->status,
                        'lampiran' => $userRequest->lampiran,
                        'created_at' => $userRequest->created_at,
                        'updated_at' => $userRequest->updated_at,
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get request statistics for authenticated user
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $statistics = [
                'total' => UserRequest::where('user_id', $user->id)->count(),
                'onprocess' => UserRequest::where('user_id', $user->id)->where('status', 'onprocess')->count(),
                'selesai' => UserRequest::where('user_id', $user->id)->where('status', 'accepted')->count(),
                'ditolak' => UserRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
                'permintaan' => UserRequest::where('user_id', $user->id)->where('type', 'permintaan')->count(),
                'pelaporan' => UserRequest::where('user_id', $user->id)->where('type', 'pelaporan')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik permintaan berhasil diambil',
                'data' => [
                    'statistics' => $statistics
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
