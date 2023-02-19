<?php

namespace Database\Seeders;

use App\Http\Controllers\ForwarderController;
use App\Models\Forwarder;
use App\Models\ForwarderLocation;
use App\Models\ForwarderStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'data-entry']);


        $mainUser = User::factory()
            ->create(
                ['name' => 'default',
                    'email' => 'default@default.com',
                    'password' => bcrypt('hastyistheowner51')
                ]
            );

        $adminUser = User::factory()
            ->create(
                [
                    'name' => 'Admin',
                    'email' => 'admin@nuqla.didar.co'
                ]
            );

        $mainUser->assignRole('admin');
        $adminUser->assignRole('admin');
//
//        if (app()->environment(['local'])) {
//
//            $user = User::factory()->create([
//                'name' => 'Yad Hoshyar',
//                'email' => 'yad@gmail.com',
//                'password' => bcrypt('password'),
//            ]);
//
//            $user->assignRole('admin');
//
//        }

        Page::factory()->create([
            'name' => 'Nuqla Store',
            'link' => 'no-url',
            'platform' => 'facebook'
        ]);

       $noForwarder =  Forwarder::create([
            'id' => 1,
            'name' => 'No forwarder'
        ]);

        $noForwarderStatus =  ForwarderStatus::create([
            'forwarder_id' => $noForwarder->id,
            'status_id' => 1,
            'name' => 'No forwarder'
        ]);

        $noForwarderLocation =  ForwarderLocation::create([
            'forwarder_id' => $noForwarder->id,
            'location_id' => 1,
            'name' => 'No forwarder'
        ]);

        Forwarder::create([
            'id' => 2,
            'name' => 'Hyperpost'
        ]);

        $getForwarderLocations = new ForwarderController;
        $getForwarderLocations->refreshForwarderLocations(Forwarder::FORWARDER_HYPERPOST);

        $getForwarderStatuses = new ForwarderController;
        $getForwarderStatuses->refreshForwarderStatuses(Forwarder::FORWARDER_HYPERPOST);

    }
}
