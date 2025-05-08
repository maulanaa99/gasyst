<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mobil;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update path gambar yang sudah ada
        $mobils = Mobil::all();
        foreach ($mobils as $mobil) {
            if ($mobil->image) {
                // Jika path masih menggunakan format lama (public/mobil/...)
                if (strpos($mobil->image, 'public/mobil/') === 0) {
                    $mobil->image = str_replace('public/mobil/', 'mobil/', $mobil->image);
                }
                // Jika path masih menggunakan format lama (mobil/...)
                elseif (strpos($mobil->image, 'mobil/') !== 0) {
                    $mobil->image = 'mobil/' . $mobil->image;
                }
                $mobil->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan path gambar ke format lama
        $mobils = Mobil::all();
        foreach ($mobils as $mobil) {
            if ($mobil->image && strpos($mobil->image, 'mobil/') === 0) {
                $mobil->image = 'public/' . $mobil->image;
                $mobil->save();
            }
        }
    }
};
