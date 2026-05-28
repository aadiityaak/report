<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_brand',
        'pemilik',
        'deskripsi',
        'logo_path',
    ];

    /**
     * Get the user that owns the brand.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaksi for the brand.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'brand', 'nama_brand');
    }
}
