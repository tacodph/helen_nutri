<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(DB::table('users')->get()->count() == 0){

            // Admin
            User::create([
                'tipo_id' => 2, // Admin
                'name' => 'Felipe Campos',
                'email' => 'felipe.vra@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('321321321'),
            ]);

            // Padrão
            for($i=1; $i <= 10; $i++) {
                User::factory()->create([
                    'email' => 'user' . $i . '@gmail.com',
                ]);
            }


            $this->command->info('✅ User: Seed realizado com sucesso.');
 
         } else { $this->command->info('ℹ️ User: já populado. Seed NÃO realizado.'); }
    }
}
