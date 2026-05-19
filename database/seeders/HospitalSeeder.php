<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('[Seeder] Starting Hospital Infrastructure Refactoring...');

        // 1. Purge existing data (Strict cleanup)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('emergency_audits')->truncate();
        DB::table('emergency_requests')->truncate();
        DB::table('ambulances')->truncate();
        DB::table('hospitals')->truncate();
        
        // Delete users with 'hospital' role but keep admins
        User::where('role', 'hospital')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Log::info('[Seeder] Database purged. Seeding fresh realistic infrastructure...');

        $cities = [
            'Phagwara'   => [31.2248, 75.7720],
            'Jalandhar'  => [31.3260, 75.5762],
            'Ludhiana'   => [30.9010, 75.8573],
            'Chandigarh' => [30.7333, 76.7794],
            'Delhi'      => [28.6139, 77.2090],
            'Mumbai'     => [19.0760, 72.8777],
            'Jaipur'     => [26.9124, 75.7873],
            'Bangalore'  => [12.9716, 77.5946],
            'Kochi'      => [9.9312, 76.2673],
            'Patiala'    => [30.3398, 76.3869],
            'Amritsar'   => [31.6340, 74.8723],
            'Bathinda'   => [30.2110, 74.9455],
        ];

        $hospitalTypes = [
            'Fortis Memorial',
            'Max Super Specialty',
            'Apollo Health City',
            'Manipal Emergency',
            'Narayana Health',
            'Medanta Medicity',
            'Aster DM Clinic',
            'Wockhardt Rescue',
            'Cloudnine Urgent',
            'Columbia Asia Care',
            'General Civil Hospital',
            'Red Cross Emergency',
        ];

        $hospitalCount = 0;
        foreach ($cities as $cityName => $coords) {
            foreach ($hospitalTypes as $index => $type) {
                $name = "{$cityName} {$type}";
                $email = strtolower(str_replace([' ', '-'], '.', $name)) . "@resqflow.hospital";
                
                // Create user
                $user = User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Hash::make('password123'),
                    'role'     => 'hospital',
                ]);

                // Jitter for variety
                $lat = $coords[0] + (mt_rand(-500, 500) / 10000);
                $lng = $coords[1] + (mt_rand(-500, 500) / 10000);

                $hospital = Hospital::create([
                    'user_id'           => $user->id,
                    'name'              => $name,
                    'address'           => "{$cityName}, Punjab/India",
                    'latitude'          => $lat,
                    'longitude'         => $lng,
                    'contact'           => '+91 ' . mt_rand(7000000000, 9999999999),
                    'available_beds'    => mt_rand(20, 100),
                    'reliability_score' => mt_rand(85, 100),
                ]);

                // Create 4-6 ambulances per hospital
                $ambulanceCount = mt_rand(4, 6);
                for ($i = 1; $i <= $ambulanceCount; $i++) {
                    Ambulance::create([
                        'hospital_id'      => $hospital->id,
                        'driver_name'      => "Driver " . chr(mt_rand(65, 90)) . ". " . ucfirst($cityName) . " " . $i,
                        'plate_number'     => "PB-" . mt_rand(10, 99) . "-" . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . "-" . mt_rand(1000, 9999),
                        'status'           => 'available',
                        'current_latitude' => $lat,
                        'current_longitude'=> $lng,
                    ]);
                }
                $hospitalCount++;
            }
        }

        Log::info("[Seeder] Successfully seeded {$hospitalCount} hospitals with associated emergency fleets.");
    }
}
