<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Tabel userr ---
        Schema::create('userr', function (Blueprint $table) {
            $table->id('id_user'); // kolom primary key
            $table->string('nama_user'); // nama user
            $table->string('email')->unique(); // email unik
            $table->string('password'); // password
            $table->enum('role', ['admin', 'dosen', 'user'])->default('user'); // role: admin/dosen/user
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // status akun
            $table->timestamps(); // kolom created_at dan updated_at
        });

        // --- Tabel user_profiles ---
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id('id_profile'); // kolom primary key
            $table->foreignId('user_id')
                ->constrained('userr', 'id_user')
                ->onDelete('cascade'); // foreign key ke tabel userr

            $table->string('alamat')->nullable(); // alamat user
            $table->string('nomor_telepon')->nullable(); // nomor telepon user
            $table->date('tanggal_lahir')->nullable(); // tanggal lahir user
            $table->string('foto_profil')->nullable(); // path foto profil
            $table->text('bio')->nullable(); // bio user
            $table->string('media_sosial')->nullable(); // link media sosial
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); // jenis kelamin (L/P)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('userr');
    }
};