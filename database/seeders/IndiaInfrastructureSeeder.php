<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class IndiaInfrastructureSeeder extends Seeder
{
    private $cities = [
        ['name' => 'Delhi', 'lat' => 28.6139, 'lng' => 77.2090],
        ['name' => 'Mumbai', 'lat' => 19.0760, 'lng' => 72.8777],
        ['name' => 'Bangalore', 'lat' => 12.9716, 'lng' => 77.5946],
        ['name' => 'Hyderabad', 'lat' => 17.3850, 'lng' => 78.4867],
        ['name' => 'Chennai', 'lat' => 13.0827, 'lng' => 80.2707],
        ['name' => 'Kolkata', 'lat' => 22.5726, 'lng' => 88.3639],
        ['name' => 'Jaipur', 'lat' => 26.9124, 'lng' => 75.7873],
        ['name' => 'Pune', 'lat' => 18.5204, 'lng' => 73.8567],
        ['name' => 'Lucknow', 'lat' => 26.8467, 'lng' => 80.9462],
        ['name' => 'Ahmedabad', 'lat' => 23.0225, 'lng' => 72.5714],
        ['name' => 'Kochi', 'lat' => 9.9312, 'lng' => 76.2673],
        ['name' => 'Chandigarh', 'lat' => 30.7333, 'lng' => 76.7794],
        ['name' => 'Indore', 'lat' => 22.7196, 'lng' => 75.8577],
    ];

    private $hospitalNames = [
        'City General', 'LifeCare', 'St. Marys', 'Apollo Alpha', 'Metro Health',
        'Fortis Care', 'Max Super Specialty', 'AIMS', 'Holy Cross', 'Global Trust',
        'Regency', 'Medanta', 'Nanavati', 'Lilavati', 'Manipal', 'Columbia Asia',
        'Narayan Health', 'Wockhardt', 'Jaslok', 'Breach Candy'
    ];

    private $driverNames = [
        'Rajesh Kumar', 'Amit Singh', 'Sandeep Sharma', 'Vikram Aditya', 'Suresh Raina',
        'Pankaj Tripathi', 'Arjun Kapoor', 'Manoj Bajpayee', 'Nitin Gadkari', 'Rahul Dravid',
        'Sunil Gavaskar', 'Kapil Dev', 'Virat Kohli', 'MS Dhoni', 'Sachin Tendulkar'
    ];

    public function run(): void
    {
        $count = 0;
        foreach ($this->cities as $city) {
            $numHospitals = rand(10, 15);
            $this->command->info("Seeding {$numHospitals} hospitals in {$city['name']}...");

            for ($i = 0; $i < $numHospitals; $i++) {
                $hName = $this->hospitalNames[array_rand($this->hospitalNames)] . ' ' . $city['name'] . ' ' . ($i + 1);
                $email = strtolower(str_replace(' ', '.', $hName)) . '@resqflow.com';

                $user = User::create([
                    'name'     => $hName . ' Admin',
                    'email'    => $email,
                    'password' => Hash::make('password'),
                    'role'     => 'hospital',
                ]);

                $hospital = Hospital::create([
                    'name'           => $hName,
                    'address'        => $city['name'] . ' Sector ' . rand(1, 50),
                    'latitude'       => $city['lat'] + (rand(-100, 100) / 1000),
                    'longitude'      => $city['lng'] + (rand(-100, 100) / 1000),
                    'contact'        => '+91 ' . rand(7000, 9999) . ' ' . rand(100000, 999999),
                    'available_beds' => rand(5, 50),
                    'user_id'        => $user->id,
                ]);

                // Seed Ambulances for this hospital
                $numAmbulances = rand(3, 10);
                for ($j = 0; $j < $numAmbulances; $j++) {
                    Ambulance::create([
                        'hospital_id'       => $hospital->id,
                        'driver_name'       => $this->driverNames[array_rand($this->driverNames)],
                        'plate_number'      => strtoupper(substr($city['name'], 0, 2)) . '-' . rand(10, 99) . '-' . strtoupper(Str::random(2)) . '-' . rand(1000, 9999),
                        'status'            => 'available',
                        'current_latitude'  => $hospital->latitude,
                        'current_longitude' => $hospital->longitude,
                    ]);
                }
                $count++;
            }
        }

        $this->command->info("✅ Successfully seeded {$count} hospitals across India.");
    }
}
