<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoDeUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(DB::table('tipos_de_usuarios')->get()->count() == 0){

            DB::table('tipos_de_usuarios')->insert([
                ['tipo' => 'padrao'],
                ['tipo' => 'admin'],
            ]);

            $this->command->info('✅ Tipo de Usuário: Seed realizado com sucesso.');
 
         } else { $this->command->info('ℹ️ Tipo de Usuário: já populado. Seed NÃO realizado.'); }
    }
}
