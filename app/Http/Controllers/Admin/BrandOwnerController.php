<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandOwnerController extends Controller
{
    /**
     * Display a listing of brands with their owner assignments.
     */
    public function index()
    {
        $brands = Brand::with('user:id,name,email')->get()->map(function ($brand) {
            return [
                'id' => $brand->id,
                'nama_brand' => $brand->nama_brand,
                'pemilik' => $brand->pemilik,
                'deskripsi' => $brand->deskripsi,
                'user_id' => $brand->user_id,
                'owner_name' => $brand->user?->name,
                'owner_email' => $brand->user?->email,
            ];
        });

        $branOwners = User::where('role', 'bran_owner')
            ->select(['id', 'name', 'email'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        return Inertia::render('admin/BrandOwnership', [
            'brands' => $brands,
            'branOwners' => $branOwners,
        ]);
    }

    /**
     * Update the brand owner assignment for a brand.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['user_id'] !== null) {
            $user = User::findOrFail($validated['user_id']);
            if ($user->role !== 'bran_owner') {
                return redirect()->back()->with('error', 'User yang dipilih harus memiliki role bran_owner.');
            }
        }

        $brand->update([
            'user_id' => $validated['user_id'],
        ]);

        return redirect()->back()->with('success', 'Relasi brand owner berhasil diperbarui.');
    }
}
