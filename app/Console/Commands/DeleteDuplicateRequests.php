<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateRequests extends Command
{
    protected $signature = 'delete:duplicate-requests';

    protected $description = 'Delete duplicate requests affected to drivers';

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

        // Delete rows with duplicate request_ids from request_meta table
        DB::table('request_meta')
            ->whereIn('request_id', $duplicateRequestIdsMeta)
            ->delete();

        // Retrieve request_id values with multiple driver_ids from request_dedicated_drivers table
        $duplicateRequestIdsDedicatedDrivers = DB::table('request_dedicated_drivers')
            ->select('request_id')
            ->groupBy('request_id')
            ->havingRaw('COUNT(driver_id) > 1')
            ->get()
            ->pluck('request_id');

        // Delete rows with duplicate request_ids from request_dedicated_drivers table
        DB::table('request_dedicated_drivers')
            ->whereIn('request_id', $duplicateRequestIdsDedicatedDrivers)
            ->delete();

        $this->info('Duplicate requests affected to drivers deleted successfully.');
    }
}

