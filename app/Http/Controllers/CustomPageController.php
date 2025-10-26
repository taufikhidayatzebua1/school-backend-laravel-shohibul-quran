<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomPageRequest;
use App\Http\Requests\UpdateCustomPageRequest;
use App\Http\Resources\CustomPageResource;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomPageController extends Controller
{
    /**
     * Display a listing of custom pages.
     * 
     * - Admin & Super-admin: dapat melihat semua halaman
     * - User lain: hanya dapat melihat halaman sesuai role mereka
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika tidak ada user yang login
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = CustomPage::query();

        // Admin dan Super-admin dapat melihat semua halaman
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            // User lain hanya dapat melihat halaman sesuai role mereka
            $query->viewableByUser($user);
        }

        // Filter by search (title)
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        $pages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Custom pages retrieved successfully',
            'data' => CustomPageResource::collection($pages),
            'meta' => [
                'current_page' => $pages->currentPage(),
                'from' => $pages->firstItem(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'to' => $pages->lastItem(),
                'total' => $pages->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created custom page.
     * Hanya admin dan super-admin yang dapat membuat halaman
     */
    public function store(StoreCustomPageRequest $request)
    {
        $user = $request->user();

        // Check authentication
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        // Check authorization - only admin and super-admin
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            Log::channel('security')->warning('Unauthorized custom page creation attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin or super-admin can create custom pages.',
            ], 403);
        }

        // Create custom page
        $customPage = CustomPage::create([
            'title' => $request->title,
            'html_content' => $request->html_content,
            'role' => $request->role,
        ]);

        Log::info('Custom page created', [
            'page_id' => $customPage->id,
            'created_by' => $user->id,
            'title' => $customPage->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom page created successfully',
            'data' => new CustomPageResource($customPage),
        ], 201);
    }

    /**
     * Display the specified custom page.
     * User hanya dapat melihat halaman sesuai role mereka
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();

        // Check authentication
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        $customPage = CustomPage::find($id);

        if (!$customPage) {
            return response()->json([
                'success' => false,
                'message' => 'Custom page not found',
            ], 404);
        }

        // Admin dan Super-admin dapat melihat semua halaman
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            // User lain hanya dapat melihat halaman sesuai role mereka
            if (!$customPage->canBeViewedByUser($user)) {
                Log::channel('security')->warning('Unauthorized custom page view attempt', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'page_id' => $customPage->id,
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to view this page.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom page retrieved successfully',
            'data' => new CustomPageResource($customPage),
        ], 200);
    }

    /**
     * Update the specified custom page.
     * Hanya admin dan super-admin yang dapat mengupdate halaman
     */
    public function update(UpdateCustomPageRequest $request, string $id)
    {
        $user = $request->user();

        // Check authentication
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        // Check authorization - only admin and super-admin
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            Log::channel('security')->warning('Unauthorized custom page update attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'page_id' => $id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin or super-admin can update custom pages.',
            ], 403);
        }

        $customPage = CustomPage::find($id);

        if (!$customPage) {
            return response()->json([
                'success' => false,
                'message' => 'Custom page not found',
            ], 404);
        }

        // Update only filled fields
        if ($request->filled('title')) {
            $customPage->title = $request->title;
        }

        if ($request->filled('html_content')) {
            $customPage->html_content = $request->html_content;
        }

        if ($request->filled('role')) {
            $customPage->role = $request->role;
        }

        $customPage->save();

        Log::info('Custom page updated', [
            'page_id' => $customPage->id,
            'updated_by' => $user->id,
            'title' => $customPage->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom page updated successfully',
            'data' => new CustomPageResource($customPage),
        ], 200);
    }

    /**
     * Remove the specified custom page.
     * Hanya admin dan super-admin yang dapat menghapus halaman
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        // Check authentication
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        // Check authorization - only admin and super-admin
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            Log::channel('security')->warning('Unauthorized custom page deletion attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'page_id' => $id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin or super-admin can delete custom pages.',
            ], 403);
        }

        $customPage = CustomPage::find($id);

        if (!$customPage) {
            return response()->json([
                'success' => false,
                'message' => 'Custom page not found',
            ], 404);
        }

        $pageTitle = $customPage->title;
        $customPage->delete();

        Log::info('Custom page deleted', [
            'page_id' => $id,
            'deleted_by' => $user->id,
            'title' => $pageTitle,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom page deleted successfully',
        ], 200);
    }
}
