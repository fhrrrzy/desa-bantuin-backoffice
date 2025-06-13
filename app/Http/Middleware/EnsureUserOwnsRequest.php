<?php

namespace App\Http\Middleware;

use App\Models\UserRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $requestId = $request->route('id') ?? $request->route('userRequest');

        if ($requestId) {
            $userRequest = UserRequest::find($requestId);

            if (!$userRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            if ($userRequest->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke permintaan ini'
                ], 403);
            }
        }

        return $next($request);
    }
}
