<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest; 
use App\Http\Requests\User\UpdateUserRequest; 
use App\Http\Resources\UserResource; 
use App\Models\User; 
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Storage; 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse 
    {
        $users = User::latest()->get(); 
               return response()->json([ 
            'message' => 'Daftar pengguna berhasil diambil.', 
            'data' => UserResource::collection($users) 
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse 
    { 
        $data = $request->validated(); 
        if ($data['role'] === 'admin' && ! $request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya Super Admin yang dapat menetapkan role Admin.'], 403);
        }
        $user = DB::transaction(function () use ($request, $data) {
            $data['password'] = Hash::make($data['password']); 
 
            if ($request->hasFile('foto_profile')) { 
                $data['foto_profile'] = $request->file('foto_profile')->store('profiles', 'public'); 
            } 
            return User::create($data); 
        }); 
        return response()->json([ 
            'message' => 'Pengguna berhasil ditambahkan.', 
            'data' => new UserResource($user) 
        ], 201); 
    } 
    public function show(User $user): JsonResponse 
    { 
        return response()->json([ 
            'data' => new UserResource($user) 
        ]); 
    } 
    public function update(UpdateUserRequest $request, User $user): JsonResponse 
    { 
        $validated = $request->validated();
        $actor = $request->user();
        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya Super Admin yang dapat mengubah data Super Admin.'], 403);
        }
        if (($user->isSuperAdmin() || $user->id === $actor->id) && $validated['role'] !== $user->role) {
            return response()->json(['message' => 'Perubahan role tidak diizinkan.'], 403);
        }
        if ($validated['role'] === 'admin' && ! $actor->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya Super Admin yang dapat menetapkan role Admin.'], 403);
        }
        $data = collect($validated);
        DB::transaction(function () use ($request, $data, $user) { 
            if ($data->get('password')) { 
                $data['password'] = Hash::make($data['password']); 
            } else { 
                $data->forget('password'); // Sama fungsinya dengan unset() 
            } 
 
            if ($request->hasFile('foto_profile')) { 
                if ($user->foto_profile) { 
                    Storage::disk('public')->delete($user->foto_profile); 
                } 
                $data['foto_profile'] = $request->file('foto_profile')->store('profiles', 'public'); 
            } 
            $user->update($data->toArray()); 
        }); 
        return response()->json([ 
        'message' => 'Data pengguna berhasil diperbarui.', 
            'data' => new UserResource($user) 
        ]); 
    } 
    public function destroy(User $user): JsonResponse 
    { 
        if ($user->id === request()->user()->id || $user->isSuperAdmin() || $user->peminjaman()->whereIn('status', ['diajukan', 'dipinjam', 'telat'])->exists()) {
            return response()->json(['message' => 'User tidak dapat dihapus karena dilindungi atau masih memiliki transaksi aktif.'], 422);
        }
        DB::transaction(function () use ($user) { 
            if ($user->foto_profile) { 
                Storage::disk('public')->delete($user->foto_profile); 
            } 
            $user->delete(); 
        }); 
        return response()->json([ 
            'message' => 'Pengguna berhasil dihapus.' 
        ]); 
    } 
}
