<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> origin/main
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
=======
    /**
     * Urutan pemanggilan mengikuti arah foreign key:
     * tabel yang dirujuk harus terisi lebih dulu.
     */
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            DompetSeeder::class,
>>>>>>> origin/main
        ]);
    }
}
