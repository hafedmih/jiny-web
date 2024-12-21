<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateDriverPasswords extends Command
{
    protected $signature = 'update:driver-passwords';

    protected $description = 'Update passwords for drivers based on given conditions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $drivers = DB::table('drivers')->get();

        foreach ($drivers as $driver) {
            // Generate a unique 4-digit pin code for each driver
            do {
                $pincode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                $existingDriver = DB::table('drivers')->where('pincode', $pincode)->first();
            } while ($existingDriver);

            // Hash the pin code securely
            $hashedPincode = Hash::make($pincode);

            // Update user table with the hashed pin code
            DB::table('users')
                ->where('id', $driver->user_id)
                ->update(['password' => $hashedPincode]);

            // Update pincode column in drivers table with the generated pin code
            DB::table('drivers')
                ->where('id', $driver->id)
                ->update(['pincode' => $pincode]);
        }

        $this->info('Driver passwords and pincodes updated successfully.');
    }
}

