<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UsuarioAdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try{
            $usuario = new User();
            $usuario->name = 'admin';
            $usuario->email = 'admin@admin.com';
            $usuario->password = Hash::make('admin2022');
            $usuario->estado = 1;
            $usuario->es_administrador = 1;
            $usuario->save();

            DB::commit();
        }catch(Exception|Throwable $e){
            DB::rollBack();
            $this->command->warn($e->getMessage());
        }
    }
}
