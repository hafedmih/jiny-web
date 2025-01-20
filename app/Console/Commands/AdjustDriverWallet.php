<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdjustDriverWallet extends Command
{
    protected $signature = 'adjust:driver-wallet';
    protected $description = 'Adjust the wallet of drivers listed in the hardcoded JSON file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Hardcoded file path
        $filePath = '/var/www/html/lahagni/storage/app/driver_phones.json';

        // Check if the file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        // Read and decode the JSON file
        $fileContents = file_get_contents($filePath);
        $driverPhones = json_decode($fileContents, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON file: " . json_last_error_msg());
            return;
        }

        $this->info("Processing " . count($driverPhones) . " driver phone numbers.");

        // Loop through each phone number and adjust the wallet
        foreach ($driverPhones as $phoneNumber) {
            $user = DB::table('users')->where('phone_number', $phoneNumber)->first();

            if (!$user) {
                $this->warn("User not found for phone number: $phoneNumber");
                continue;
            }

            $wallet = DB::table('wallet')->where('user_id', $user->id)->first();

            if (!$wallet) {
                // Create a new wallet with a balance of 300
                $walletId = DB::table('wallet')->insertGetId([
                    'user_id' => $user->id,
                    'balance_amount' => 300,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Add a wallet transaction
                DB::table('wallet_transaction')->insert([
                    'wallet_id' => $walletId,
                    'user_id' => $user->id,
                    'amount' => 300,
                    'purpose' => 'Initial wallet adjustment',
                    'type' => 'EARNED',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info("Wallet created for user ID {$user->phone_number} with a balance of 300.");
            } else {
                // Calculate the amount to add to make the balance 300
                $amountToAdd = max(0, 300 - $wallet->balance_amount);

                if ($amountToAdd > 0) {
                    // Update the wallet balance
                    DB::table('wallet')->where('id', $wallet->id)->update([
                        'balance_amount' => 300,
                        'updated_at' => now(),
                    ]);

                    // Add a wallet transaction
                    DB::table('wallet_transaction')->insert([
                        'wallet_id' => $wallet->id,
                        'user_id' => $user->id,
                        'amount' => $amountToAdd,
                        'purpose' => 'Wallet adjustment to reach 300',
                        'type' => 'EARNED',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("Wallet adjusted for user ID {$user->phone_number} with an added amount of $amountToAdd.");
                } else {
                    $this->info("No adjustment needed for user ID {$user->phone_number}, wallet balance is already 300 or more.");
                }
            }
        }

        $this->info('Driver wallet adjustments completed successfully.');
    }
}

