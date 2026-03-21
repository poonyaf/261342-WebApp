<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')],
            [ 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'nannapat_chaip@cmu.ac.th'],
            ['name' => 'fame', 'password' => bcrypt('fame1846')],
            [ 'role' => 'admin']
        );
        User::updateOrCreate(
            ['email' => 'pandarayuti@gmail.com'],
            ['name' => 'panda', 'password' => bcrypt('Pandarayuti0707')],
            [ 'role' => 'admin']
        );
         User::updateOrCreate(
            ['email' => 'forcolingzie@gmail.com'],
            ['name' => 'forcollapsse', 'password' => bcrypt('670615022')],
            [ 'role' => 'admin']
        );
  User::updateOrCreate(
    [ 'email' => 'fai123@gmail.com' ],
    [ 'name'     => 'Fai', 'password' => bcrypt('12345678')],
    [ 'role'     => 'admin' ]
);
  
        $this->call([
        TagSeeder::class,
        ProductSeeder::class,
        
    ]);
    }
}
