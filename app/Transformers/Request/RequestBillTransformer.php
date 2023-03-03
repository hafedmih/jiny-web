<?php

namespace App\Transformers\Request;

use App\Models\taxi\Requests\Request;
use App\Models\taxi\Requests\RequestBill;
use League\Fractal\TransformerAbstract;

class RequestBillTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(RequestBill $bill)
    {
        $data =[
            'id'                        => $bill->id,
            'base_price'                => $bill->base_price,
            'base_distance'             => $bill->base_distance,
            'price_per_distance'        => $bill->price_per_distance,
            'distance_price'            => $bill->distance_price,
            'price_per_time'            => $bill->price_per_time,
            'time_price'                => $bill->time_price,
            'waiting_charge'            => $bill->waiting_charge,
            'cancellation_fee'          => $bill->cancellation_fee,
            'admin_commision'           => $bill->admin_commision,
            'driver_commision'          => $bill->driver_commision,
            'total_amount'              => $bill->total_amount,
            'total_distance'            => $bill->total_distance,
            'total_time'                => $bill->total_time,
            'promo_discount'            => $bill->promo_discount,
            'requested_currency_code'   => $bill->requested_currency_code,
            'requested_currency_symbol' => $bill->requested_currency_symbol,
            'total_without_promo'       => $bill->sub_total,
            'out_of_zone_price'         => $bill->out_of_zone_price,
            'service_tax'               => $bill->service_tax,
            'service_tax_percentage'    => $bill->service_tax_percentage,
            'booking_fees'              => $bill->booking_fees,
            'hill_station_price'        => $bill->hill_station_price,
            'package_hours'             => $bill->package_hours,
            'package_km'                => $bill->package_km,
            'pending_km'                => $bill->pending_km,
            
        ];

        return $data;
    }
}
