<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique()->nullable();

            $table->enum('role', ['user', 'admin'])->default('user');

            $table->rememberToken();
            $table->timestamps(); // create_at & updated_at
            $table->string('agama', 100)->nullable();
            $table->string('jenis_kelamin', 100)->nullable();
            $table->integer('Angkatan')->nullable();
            $table->string('Kelas', 100)->nullable();
            $table->string('Status', 100)->nullable();
            $table->string('program_studi', 100)->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
