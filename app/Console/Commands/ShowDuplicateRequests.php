<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowDuplicateRequests extends Command
{
    protected $signature = 'show:duplicate-requests';

    protected $description = 'Show duplicate requests affected to drivers without deleting them';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Retrieve request_id values with multiple driver_ids from request_meta table
        $duplicateRequestIdsMeta = DB::table('request_meta')
            ->select('request_id')
            ->groupBy('request_id')
            ->havingRaw('COUNT(driver_id) > 1')
            ->get()
            ->pluck('request_id');

        // Retrieve request_id values with multiple driver_ids from request_dedicated_drivers table
        $duplicateRequestIdsDedicatedDrivers = DB::table('request_dedicated_drivers')
            ->select('request_id')
            ->groupBy('request_id')
            ->havingRaw('COUNT(driver_id) > 1')
            ->get()
            ->pluck('request_id');

        // Combine duplicate request IDs from both tables
        $duplicateRequestIds = $duplicateRequestIdsMeta->merge($duplicateRequestIdsDedicatedDrivers)->unique();

        if ($duplicateRequestIds->isEmpty()) {
            $this->info('No duplicate requests affected to drivers found.');
            return;
        }

        // Retrieve detailed information about duplicate requests from request_places table
        $duplicateRequests = DB::table('request_places')
            ->whereIn('request_id', $duplicateRequestIds)
            ->select('request_id', 'pick_address', 'drop_address')
            ->get();

        // Display the details of duplicate requests
        $headers = ['Request ID', 'Pick-up Address', 'Drop-off Address'];
        $data = $duplicateRequests->map(function ($item) {
            return [
                'Request ID' => $item->request_id,
                'Pick-up Address' => $item->pick_address,
                'Drop-off Address' => $item->drop_address,
            ];
        });

        $this->table($headers, $data);
    }
}

