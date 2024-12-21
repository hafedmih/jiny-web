<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDriverEmails extends Command
{
    protected $signature = 'update:driver-emails';

    protected $description = 'Update emails for drivers based on given conditions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $drivers = DB::table('drivers')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->select('drivers.id as driver_id', 'users.phone_number')
            ->get();

        foreach ($drivers as $driver) {
            // Concatenate phone number with '@lahagni.com'
            $email = $driver->phone_number . '@lahagni.com';

            // Update user table with the new email
            DB::table('users')
                ->where('id', $driver->driver_id)
                ->update(['email' => $email]);
        }

        $this->info('Driver emails updated successfully.');
    }
}

