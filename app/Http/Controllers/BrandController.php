<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isBranOwner()) {
            $brands = $user->brands()->get();
        } else {
            $brands = Brand::all();
        }

        return Inertia::render('brand/BrandList', [
            'brands' => $brands,
            'canEdit' => $user->canEdit(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->isBranOwner()) {
            $brands = $user->brands()->get();
        } else {
            $brands = Brand::all();
        }

        return Inertia::render('brand/BrandForm', [
            'brands' => $brands,
            'canEdit' => $user->canEdit(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama_brand' => 'required|string|max:255|unique:brands',
            'pemilik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $data = [
            'nama_brand' => $validated['nama_brand'],
            'pemilik' => $validated['pemilik'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'logo_path' => $logoPath,
        ];

        if ($user->isBranOwner()) {
            $data['user_id'] = $user->id;
        }

        Brand::create($data);

        return redirect()->route('brand.input')->with('success', 'Brand berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $user = auth()->user();
        if ($user->isBranOwner() && $brand->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke brand ini.');
        }

        return Inertia::render('brand/BrandShow', [
            'brand' => $brand
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $user = auth()->user();
        if ($user->isBranOwner() && $brand->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke brand ini.');
        }

        return Inertia::render('brand/BrandEdit', [
            'brand' => $brand
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $user = auth()->user();

        if ($user->isBranOwner() && $brand->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke brand ini.');
        }

        $validated = $request->validate([
            'nama_brand' => 'required|string|max:255|unique:brands,nama_brand,' . $brand->id,
            'pemilik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nama_brand' => $validated['nama_brand'],
            'pemilik' => $validated['pemilik'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            if ($brand->logo_path && \Storage::disk('public')->exists($brand->logo_path)) {
                \Storage::disk('public')->delete($brand->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $brand->update($updateData);

        return redirect()->route('brand.input')->with('success', 'Brand berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $user = auth()->user();

        if ($user->isBranOwner() && $brand->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke brand ini.');
        }

        if ($brand->logo_path && \Storage::disk('public')->exists($brand->logo_path)) {
            \Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->delete();

        return redirect()->route('brand.input')->with('success', 'Brand berhasil dihapus.');
    }
}
