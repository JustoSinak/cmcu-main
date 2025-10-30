<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = \App\Models\Role::create([
            'name' => 'ADMINISTRATEUR',
        ]);

        $role2 = \App\Models\Role::create([
            'name' => 'MEDECIN',
        ]);

        $role3 = \App\Models\Role::create([
            'name' => 'GESTIONNAIRE',
        ]);

        $role4 = \App\Models\Role::create([
            'name' => 'INFIRMIER',
        ]);

        $role5 = \App\Models\Role::create([
            'name' => 'LOGISTIQUE',
        ]);

        $role6 = \App\Models\Role::create([
            'name' => 'SECRETAIRE',
        ]);

        $role7 = \App\Models\Role::create([
            'name' => 'PHARMACIEN',
        ]);

        $role8 = \App\Models\Role::create([
            'name' => 'QUALITE',
        ]);

        $role9 = \App\Models\Role::create([
            'name' => 'COMPTABLE',
        ]);

    }
}
