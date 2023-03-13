<?php

namespace App\Http\Controllers\Taxi\Web\ZoneManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\boilerplate\Country;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Driver;
use App\Models\taxi\Zone;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\ZoneTypeSurgePrice;
use App\Models\taxi\ZoneSurgePrice;
use App\Models\taxi\PackageItem;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\OutstationPriceFixing;
use App\Models\taxi\Requests\Request as RequestModel;

use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;

use DB;

use Validator;

class ZoneController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:active-zone', ['only' => ['activeZone']]);
        $this->middleware('permission:add-zone', ['only' => ['addzone']]);
        $this->middleware('permission:edit-zone', [
            'only' => ['editZone', 'updateZone'],
        ]);
        $this->middleware('permission:delete-zone', ['only' => ['deleteZone']]);
        $this->middleware('permission:zone-surge-price', [
            'only' => ['getZoneSrugePriceSave', 'getZoneSrugePrice'],
        ]);
        $this->middleware('permission:view-map-zone', [
            'only' => ['viewMapZone'],
        ]);
    }

    public function index(Request $request)
    {
        $zone = Zone::get();
        return view('taxi.zone.index', compact(['zone']));
    }

    public function addzone(Request $request)
    {
        $zone = Zone::where('status', 1)
            ->where('zone_level', 'PRIMARY')
            ->get();
        $countries = Country::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        if (is_null($countries)) {
            abort('404');
        }

        $vehicleList = Vehicle::all();
        if (is_null($vehicleList)) {
            abort('404');
        }

        return view(
            'taxi.zone.create',
            compact(['countries', 'vehicleList', 'zone'])
        );
    }

    public function getCoordsByKeyword(Request $request)
    {
    }

    public function saveZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_level' => 'required',
            'driver_assign_method' => 'required',
            'zone_name' => 'required',
            'country' => 'required',
           // 'admin_commission_type' => 'required',
          //  'admin_commission' => 'required',
            'payment_type' => 'required',
            'unit' => 'required',
            'ridenow_base_price' => 'required',
            'ridenow_price_per_time' => 'required',
            'ridenow_base_distance' => 'required',
            'ridenow_price_per_distance' => 'required',
            'ridenow_free_waiting_time' => 'required',
            'ridenow_free_waiting_time_after_start' => 'required',
            'ridenow_waiting_charge' => 'required',
            'ridenow_cancellation_fee' => 'required',
            'ridelater_base_price' => 'required',
            'ridelater_price_per_time' => 'required',
            'ridelater_base_distance' => 'required',
            'ridelater_price_per_distance' => 'required',
            'ridelater_free_waiting_time' => 'required',
            'ridelater_free_waiting_time_after_start' => 'required',
            'ridelater_waiting_charge' => 'required',
            'ridelater_cancellation_fee' => 'required',
            'bounds' => 'required',
            'ridenow_admin_commission_type' => 'required',
            'ridenow_admin_commission' => 'required',
            'ridelater_admin_commission_type' => 'required',
            'ridelater_admin_commission' => 'required',
           // 'ridenow_booking_base_fare' => 'required',
           // 'ridenow_booking_base_per_kilometer' => 'required',
           // 'ridelater_booking_base_fare' => 'required',
           // 'ridelater_booking_base_per_kilometer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('zone/add')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        $val = json_decode($data['bounds']);
        // create zone to multipolygon
        $set = [];
        // foreach ($val as $key => $coordinates) {
        $points = [];
        $lineStrings = [];
        foreach ($val as $key => $coordinate) {
            $points[] = new Point($coordinate->lat, $coordinate->lng);
        }
        array_push($points, $points[0]);
        $lineStrings[] = new LineString($points);
        $polygon = new Polygon($lineStrings);

        $set[] = $polygon;
        // }

        $multi_polygon = new MultiPolygon($set);
        // dd($multi_polygon);

        if ($request->has('non_service_zone')) {
            $non_service_zone = 'Yes';
        } else {
            $non_service_zone = 'No';
        }

        $primary_zone = Zone::where('slug', $data['primary_zone'])->first();

        $zone = Zone::create([
            'zone_level' => $data['zone_level'],
            'primary_zone_id' => $primary_zone ? $primary_zone->id : '',
            'driver_assign_method' => $data['driver_assign_method'],
            'zone_name' => $data['zone_name'],
            'country' => $data['country'],
           // 'admin_commission_type' => $data['admin_commission_type'],
           // 'admin_commission' => $data['admin_commission'],
            'map_zone' => $multi_polygon,
            'payment_types' => implode(',', $data['payment_type']),
            'unit' => $data['unit'],
            'non_service_zone' => $non_service_zone,
            'map_cooder' => $data['bounds'],
            'status' => 1,
        ]);

        $types = '';

        for ($i = 0; $i < count($data['vehicle_type']); $i++) {
            $vehicle = Vehicle::where(
                'slug',
                $data['vehicle_type'][$i]
            )->first();
            $zone_price = ZonePrice::create([
                'zone_id' => $zone->id,
                'type_id' => $vehicle->id,
                'ridenow_base_price' => $data['ridenow_base_price'][$i],
                'ridenow_price_per_time' => $data['ridenow_price_per_time'][$i],
                'ridenow_base_distance' => $data['ridenow_base_distance'][$i],
                'ridenow_price_per_distance' =>
                    $data['ridenow_price_per_distance'][$i],
                'ridenow_free_waiting_time' =>
                    $data['ridenow_free_waiting_time'][$i],
                'ridenow_free_waiting_time_after_start' =>
                    $data['ridenow_free_waiting_time_after_start'][$i],
                'ridenow_waiting_charge' => $data['ridenow_waiting_charge'][$i],
                'ridenow_cancellation_fee' =>
                    $data['ridenow_cancellation_fee'][$i],
                'ridenow_admin_commission_type' =>
                    $data['ridenow_admin_commission_type'][$i],
                'ridenow_admin_commission' =>
                    $data['ridenow_admin_commission'][$i],
                // 'ridenow_booking_base_fare' =>
                //     $data['ridenow_booking_base_fare'][$i],
                // 'ridenow_booking_base_per_kilometer' =>
                //     $data['ridenow_booking_base_per_kilometer'][$i],
                'ridelater_base_price' => $data['ridelater_base_price'][$i],
                'ridelater_price_per_time' =>
                    $data['ridelater_price_per_time'][$i],
                'ridelater_base_distance' =>
                    $data['ridelater_base_distance'][$i],
                'ridelater_price_per_distance' =>
                    $data['ridelater_price_per_distance'][$i],
                'ridelater_free_waiting_time' =>
                    $data['ridelater_free_waiting_time'][$i],
                'ridelater_free_waiting_time_after_start' =>
                    $data['ridelater_free_waiting_time_after_start'][$i],
                'ridelater_waiting_charge' =>
                    $data['ridelater_waiting_charge'][$i],
                'ridelater_cancellation_fee' =>
                    $data['ridelater_cancellation_fee'][$i],
                'ridelater_admin_commission_type' =>
                    $data['ridelater_admin_commission_type'][$i],
                'ridelater_admin_commission' =>
                    $data['ridelater_admin_commission'][$i],
                // 'ridelater_booking_base_fare' =>
                //     $data['ridelater_booking_base_fare'][$i],
                // 'ridelater_booking_base_per_kilometer' =>
                //     $data['ridelater_booking_base_per_kilometer'][$i],

                'status' => 1,
                'slug' => Carbon::now()->timestamp,
            ]);

            $types .= $vehicle->id . ',';

            foreach ($data['sruge_price'][$i] as $key => $value) {
                if ($value) {
                    $zonetype = ZoneTypeSurgePrice::create([
                        'zone_type_id' => $zone_price->id,
                        'surge_price' => $value,
                        'surge_distance_price' =>
                            $data['surge_distance_price'][$i][$key],
                        'start_time' => $data['start_time'][$i][$key],
                        'end_time' => $data['end_time'][$i][$key],
                        'available_days' => implode(
                            ',',
                            $data['available_days'][$i][$key]
                        ),
                        'status' => 1,
                    ]);
                }
            }
        }
        $types = rtrim($types, ',');
        $zone->types_id = $types;
        $zone->save();

        return redirect()->route('zone');
    }

    public function editZone($id)
    {
        $zone = Zone::where('slug', $id)->first();
        $primary_zone = Zone::where('status', 1)
            ->where('zone_level', 'PRIMARY')
            ->get();
        if (is_null($zone)) {
            abort('404');
        }

        $countries = Country::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        if (is_null($countries)) {
            abort('404');
        }

        $vehicleList = Vehicle::all();
        if (is_null($vehicleList)) {
            abort('404');
        }

        return view(
            'taxi.zone.edit',
            compact(['countries', 'vehicleList', 'primary_zone', 'zone'])
        );
    }

    public function viewMapZone($id)
    {
        $zone = Zone::where('slug', $id)->first();
        if (is_null($zone)) {
            abort('404');
        }

        $countries = Country::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();
        if (is_null($countries)) {
            abort('404');
        }

        $vehicleList = Vehicle::all();
        if (is_null($vehicleList)) {
            abort('404');
        }

        return view(
            'taxi.zone.view_map',
            compact(['countries', 'vehicleList', 'zone'])
        );
    }

    public function mapView()
    {
        return view('taxi.zone.full_map_view');
    }
    public function deleteZone($id)
    {
        $zone = Zone::where('slug', $id)->first();
        $zoneprice = ZonePrice::where('zone_id', $zone->id)
            ->pluck('id')
            ->toArray();
        $zonetypesugeprice = ZoneTypeSurgePrice::whereIn(
            'zone_type_id',
            $zoneprice
        )->get();

        $request1 = Driver::where('service_location', $zone->id)->get();
        //dd($servicelocation);
        $request = RequestModel::whereIn('zone_type_id', $zoneprice)->get();

        if (count($request) > 0) {
            session()->flash(
                'message',
                'Sorry!. Already have a trip in this zone.'
            );
            session()->flash('status', false);
            return redirect()->route('zone');
        }

        if (count($request1) > 0) {
            session()->flash(
                'message',
                'Sorry!. Already have a trip in this zone1.'
            );
            session()->flash('status', false);
            return redirect()->route('zone');
        }

        Zone::where('slug', $id)->delete();

        ZonePrice::where('zone_id', $zone->id)->delete();

        ZoneTypeSurgePrice::whereIn('zone_type_id', $zoneprice)->delete();

        return redirect()->route('zone');
    }

    public function activeZone($id)
    {
        $zone = Zone::where('slug', $id)->first();
        $zoneprice = ZonePrice::where('zone_id', $zone->id)->first();

        if ($zone->status == 0) {
            Zone::where('slug', $id)->update(['status' => 1]);

            ZonePrice::where('zone_id', $zone->id)->update(['status' => 1]);

            ZoneTypeSurgePrice::where('zone_type_id', $zoneprice->id)->update([
                'status' => 1,
            ]);
        } else {
            Zone::where('slug', $id)->update(['status' => 0]);

            ZonePrice::where('zone_id', $zone->id)->update(['status' => 0]);

            ZoneTypeSurgePrice::where('zone_type_id', $zoneprice->id)->update([
                'status' => 0,
            ]);
        }
        return redirect()->route('zone');
    }

    public function updateZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_level' => 'required',
            'driver_assign_method' => 'required',
            'zone_name' => 'required',
            'country' => 'required',
            //'admin_commission_type' => 'required',
            //'admin_commission' => 'required',
            'payment_type' => 'required',
            'unit' => 'required',
            'ridenow_base_price' => 'required',
            'ridenow_price_per_time' => 'required',
            'ridenow_base_distance' => 'required',
            'ridenow_price_per_distance' => 'required',
            'ridenow_free_waiting_time' => 'required',
            'ridenow_free_waiting_time_after_start' => 'required',
            'ridenow_waiting_charge' => 'required',
            'ridenow_cancellation_fee' => 'required',
            'ridelater_base_price' => 'required',
            'ridelater_price_per_time' => 'required',
            'ridelater_base_distance' => 'required',
            'ridelater_price_per_distance' => 'required',
            'ridelater_free_waiting_time' => 'required',
            'ridelater_free_waiting_time_after_start' => 'required',
            'ridelater_waiting_charge' => 'required',
            'ridelater_cancellation_fee' => 'required',
            'bounds' => 'required',
            'ridenow_admin_commission_type' => 'required',
            'ridenow_admin_commission' => 'required',
            'ridelater_admin_commission_type' => 'required',
            'ridelater_admin_commission' => 'required',
            // 'ridenow_booking_base_fare' => 'required',
            // 'ridenow_booking_base_per_kilometer' => 'required',
            // 'ridelater_booking_base_fare' => 'required',
            // 'ridelater_booking_base_per_kilometer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $zone = Zone::where('id', $data['zone_id'])->first();
        $zone_prices = ZonePrice::where('zone_id', $zone->id)
            ->pluck('id')
            ->toarray();
        $vehicle = Vehicle::whereIn('slug', $data['vehicle_type'])
            ->pluck('id')
            ->toarray();

        $vehicle_ids = implode(',', $vehicle);
        // dd($zone_prices);
        if ($data['bounds'] != '') {
            $val = json_decode($data['bounds']);

            $set = [];
            // foreach ($val as $key => $coordinates) {
            $points = [];
            $lineStrings = [];
            foreach ($val as $key => $coordinate) {
                $points[] = new Point($coordinate->lat, $coordinate->lng);
            }
            array_push($points, $points[0]);
            $lineStrings[] = new LineString($points);
            $polygon = new Polygon($lineStrings);

            $set[] = $polygon;
            // }

            $multi_polygon = new MultiPolygon($set);
            // dd($multi_polygon);
            $zone->map_zone = $multi_polygon;
        }

        if ($request->has('non_service_zone')) {
            $non_service_zone = 'Yes';
        } else {
            $non_service_zone = 'No';
        }

        $primary_zone = Zone::where('slug', $data['primary_zone'])->first();

        $zone->zone_level = $data['zone_level'];
        $zone->primary_zone_id = $primary_zone ? $primary_zone->id : '';
        $zone->driver_assign_method = $data['driver_assign_method'];
        $zone->zone_name = $data['zone_name'];
        $zone->map_cooder = $data['bounds'];
        $zone->country = $data['country'];
       // $zone->admin_commission_type = $data['admin_commission_type'];
      //  $zone->admin_commission = $data['admin_commission'];
        $zone->payment_types = implode(',', $data['payment_type']);
        $zone->non_service_zone = $non_service_zone;
        $zone->unit = $data['unit'];
        $zonePrice = ZonePrice::where('zone_id', $zone->id)
            ->whereNotIn('type_id', $vehicle)
            ->pluck('id')
            ->toarray();

        // $requests = RequestModel::whereIn('zone_type_id', $zonePrice)->get();

        // if(count($requests) > 0){
        //     session()->flash('message',"Alerady start the trips in this zone and types!...");
        //     return back();
        // }
        // else{
        ZonePrice::where('zone_id',$zone->id)->whereNotIn('type_id',$vehicle)->delete();
        // }
        // dd($data);
        ZoneTypeSurgePrice::whereIn('zone_type_id',$zonePrice)->delete();

        for ($i = 0; $i < count($data['vehicle_type']); $i++) {
            $vehicle = Vehicle::where('slug', $data['type_id'][$i])->first();
            $zone_price = ZonePrice::where('zone_id', $zone->id)
                ->where('type_id', $vehicle->id)
                ->first();
            if ($zone_price) {
                $zone_price->ridenow_base_price = array_key_exists(
                    $i,
                    $data['ridenow_base_price']
                )
                    ? $data['ridenow_base_price'][$i]
                    : '';
                $zone_price->ridenow_price_per_time = array_key_exists(
                    $i,
                    $data['ridenow_price_per_time']
                )
                    ? $data['ridenow_price_per_time'][$i]
                    : '';
                $zone_price->ridenow_base_distance = array_key_exists(
                    $i,
                    $data['ridenow_base_distance']
                )
                    ? $data['ridenow_base_distance'][$i]
                    : '';
                $zone_price->ridenow_price_per_distance = array_key_exists(
                    $i,
                    $data['ridenow_price_per_distance']
                )
                    ? $data['ridenow_price_per_distance'][$i]
                    : '';
                $zone_price->ridenow_free_waiting_time = array_key_exists(
                    $i,
                    $data['ridenow_free_waiting_time']
                )
                    ? $data['ridenow_free_waiting_time'][$i]
                    : '';
                $zone_price->ridenow_free_waiting_time_after_start = array_key_exists(
                    $i,
                    $data['ridenow_free_waiting_time_after_start']
                )
                    ? $data['ridenow_free_waiting_time_after_start'][$i]
                    : '';
                $zone_price->ridenow_waiting_charge = array_key_exists(
                    $i,
                    $data['ridenow_waiting_charge']
                )
                    ? $data['ridenow_waiting_charge'][$i]
                    : '';
                $zone_price->ridenow_cancellation_fee = array_key_exists(
                    $i,
                    $data['ridenow_cancellation_fee']
                )
                    ? $data['ridenow_cancellation_fee'][$i]
                    : '';
                $zone_price->ridenow_admin_commission_type = array_key_exists(
                    $i,
                    $data['ridenow_admin_commission_type']
                )
                    ? $data['ridenow_admin_commission_type'][$i]
                    : '';
                $zone_price->ridenow_admin_commission = array_key_exists(
                    $i,
                    $data['ridenow_admin_commission']
                )
                    ? $data['ridenow_admin_commission'][$i]
                    : '';
                // $zone_price->ridenow_booking_base_fare = array_key_exists(
                //     $i,
                //     $data['ridenow_booking_base_fare']
                // )
                //     ? $data['ridenow_booking_base_fare'][$i]
                //     : '';
                // $zone_price->ridenow_booking_base_per_kilometer = array_key_exists(
                //     $i,
                //     $data['ridenow_booking_base_per_kilometer']
                // )
                //     ? $data['ridenow_booking_base_per_kilometer'][$i]
                //     : '';

                $zone_price->ridelater_base_price = array_key_exists(
                    $i,
                    $data['ridelater_base_price']
                )
                    ? $data['ridelater_base_price'][$i]
                    : '';
                $zone_price->ridelater_price_per_time = array_key_exists(
                    $i,
                    $data['ridelater_price_per_time']
                )
                    ? $data['ridelater_price_per_time'][$i]
                    : '';
                $zone_price->ridelater_base_distance = array_key_exists(
                    $i,
                    $data['ridelater_base_distance']
                )
                    ? $data['ridelater_base_distance'][$i]
                    : '';
                $zone_price->ridelater_price_per_distance = array_key_exists(
                    $i,
                    $data['ridelater_price_per_distance']
                )
                    ? $data['ridelater_price_per_distance'][$i]
                    : '';
                $zone_price->ridelater_free_waiting_time = array_key_exists(
                    $i,
                    $data['ridelater_free_waiting_time']
                )
                    ? $data['ridelater_free_waiting_time'][$i]
                    : '';
                $zone_price->ridelater_free_waiting_time_after_start = array_key_exists(
                    $i,
                    $data['ridelater_free_waiting_time_after_start']
                )
                    ? $data['ridelater_free_waiting_time_after_start'][$i]
                    : '';
                $zone_price->ridelater_waiting_charge = array_key_exists(
                    $i,
                    $data['ridelater_waiting_charge']
                )
                    ? $data['ridelater_waiting_charge'][$i]
                    : '';
                $zone_price->ridelater_cancellation_fee = array_key_exists(
                    $i,
                    $data['ridelater_cancellation_fee']
                )
                    ? $data['ridelater_cancellation_fee'][$i]
                    : '';
                $zone_price->ridelater_admin_commission_type = array_key_exists(
                    $i,
                    $data['ridelater_admin_commission_type']
                )
                    ? $data['ridelater_admin_commission_type'][$i]
                    : '';
                $zone_price->ridelater_admin_commission = array_key_exists(
                    $i,
                    $data['ridelater_admin_commission']
                )
                    ? $data['ridelater_admin_commission'][$i]
                    : '';
                // $zone_price->ridelater_booking_base_fare = array_key_exists(
                //     $i,
                //     $data['ridelater_booking_base_fare']
                // )
                //     ? $data['ridelater_booking_base_fare'][$i]
                //     : '';
                // $zone_price->ridelater_booking_base_per_kilometer = array_key_exists(
                //     $i,
                //     $data['ridelater_booking_base_per_kilometer']
                // )
                //     ? $data['ridelater_booking_base_per_kilometer'][$i]
                //     : '';

                $zone_price->save();
            } else {
                if (array_key_exists($i, $data['ridenow_base_price'])) {
                    $zone_price = ZonePrice::create([
                        'zone_id' => $zone->id,
                        'type_id' => $vehicle->id,
                        'ridenow_base_price' => array_key_exists(
                            $i,
                            $data['ridenow_base_price']
                        )
                            ? $data['ridenow_base_price'][$i]
                            : '',
                        'ridenow_price_per_time' => array_key_exists(
                            $i,
                            $data['ridenow_price_per_time']
                        )
                            ? $data['ridenow_price_per_time'][$i]
                            : '',
                        'ridenow_base_distance' => array_key_exists(
                            $i,
                            $data['ridenow_base_distance']
                        )
                            ? $data['ridenow_base_distance'][$i]
                            : '',
                        'ridenow_price_per_distance' => array_key_exists(
                            $i,
                            $data['ridenow_price_per_distance']
                        )
                            ? $data['ridenow_price_per_distance'][$i]
                            : '',
                        'ridenow_free_waiting_time' => array_key_exists(
                            $i,
                            $data['ridenow_free_waiting_time']
                        )
                            ? $data['ridenow_free_waiting_time'][$i]
                            : '',
                        'ridenow_free_waiting_time_after_start' => array_key_exists(
                            $i,
                            $data['ridenow_free_waiting_time_after_start']
                        )
                            ? $data['ridenow_free_waiting_time_after_start'][$i]
                            : '',
                        'ridenow_waiting_charge' => array_key_exists(
                            $i,
                            $data['ridenow_waiting_charge']
                        )
                            ? $data['ridenow_waiting_charge'][$i]
                            : '',
                        'ridenow_cancellation_fee' => array_key_exists(
                            $i,
                            $data['ridenow_cancellation_fee']
                        )
                            ? $data['ridenow_cancellation_fee'][$i]
                            : '',
                        'ridenow_admin_commission_type' => array_key_exists(
                            $i,
                            $data['ridenow_admin_commission_type']
                        )
                            ? $data['ridenow_admin_commission_type'][$i]
                            : '',
                        'ridenow_admin_commission' => array_key_exists(
                            $i,
                            $data['ridenow_admin_commission']
                        )
                            ? $data['ridenow_admin_commission'][$i]
                            : '',
                        // 'ridenow_booking_base_fare' => array_key_exists(
                        //     $i,
                        //     $data['ridenow_booking_base_fare']
                        // )
                        //     ? $data['ridenow_booking_base_fare'][$i]
                        //     : '',
                        // 'ridenow_booking_base_per_kilometer' => array_key_exists(
                        //     $i,
                        //     $data['ridenow_booking_base_per_kilometer']
                        // )
                        //     ? $data['ridenow_booking_base_per_kilometer'][$i]
                        //     : '',

                        'ridelater_base_price' => array_key_exists(
                            $i,
                            $data['ridelater_base_price']
                        )
                            ? $data['ridelater_base_price'][$i]
                            : '',
                        'ridelater_price_per_time' => array_key_exists(
                            $i,
                            $data['ridelater_price_per_time']
                        )
                            ? $data['ridelater_price_per_time'][$i]
                            : '',
                        'ridelater_base_distance' => array_key_exists(
                            $i,
                            $data['ridelater_base_distance']
                        )
                            ? $data['ridelater_base_distance'][$i]
                            : '',
                        'ridelater_price_per_distance' => array_key_exists(
                            $i,
                            $data['ridelater_price_per_distance']
                        )
                            ? $data['ridelater_price_per_distance'][$i]
                            : '',
                        'ridelater_free_waiting_time' => array_key_exists(
                            $i,
                            $data['ridelater_free_waiting_time']
                        )
                            ? $data['ridelater_free_waiting_time'][$i]
                            : '',
                        'ridelater_free_waiting_time_after_start' => array_key_exists(
                            $i,
                            $data['ridelater_free_waiting_time_after_start']
                        )
                            ? $data['ridelater_free_waiting_time_after_start'][
                                $i
                            ]
                            : '',
                        'ridelater_waiting_charge' => array_key_exists(
                            $i,
                            $data['ridelater_waiting_charge']
                        )
                            ? $data['ridelater_waiting_charge'][$i]
                            : '',
                        'ridelater_cancellation_fee' => array_key_exists(
                            $i,
                            $data['ridelater_cancellation_fee']
                        )
                            ? $data['ridelater_cancellation_fee'][$i]
                            : '',
                        'ridelater_admin_commission_type' => array_key_exists(
                            $i,
                            $data['ridelater_admin_commission_type']
                        )
                            ? $data['ridelater_admin_commission_type'][$i]
                            : '',
                        'ridelater_admin_commission' => array_key_exists(
                            $i,
                            $data['ridelater_admin_commission']
                        )
                            ? $data['ridelater_admin_commission'][$i]
                            : '',
                        'ridelater_booking_base_fare' => array_key_exists(
                            $i,
                            $data['ridelater_booking_base_fare']
                        )
                            ? $data['ridelater_booking_base_fare'][$i]
                            : '',
                        'ridelater_booking_base_per_kilometer' => array_key_exists(
                            $i,
                            $data['ridelater_booking_base_per_kilometer']
                        )
                            ? $data['ridelater_booking_base_per_kilometer'][$i]
                            : '',

                        'status' => 1,
                        'slug' => Carbon::now()->timestamp,
                    ]);
                }
            }

            foreach ($data['sruge_price_id'][$i] as $key => $value) {
                if ($value) {
                    if ($data['sruge_price'][$i][$key] != null) {
                        $zonetype = ZoneTypeSurgePrice::where(
                            'id',
                            $value
                        )->update([
                            'zone_type_id' => $zone_price->id,
                            'surge_price' => $data['sruge_price'][$i][$key],
                            'surge_distance_price' =>
                                $data['surge_distance_price'][$i][$key],
                            'start_time' => $data['start_time'][$i][$key],
                            'end_time' => $data['end_time'][$i][$key],
                            'available_days' => implode(
                                ',',
                                $data['available_days'][$i][$key]
                            ),
                        ]);
                    } else {
                        $zonetype = ZoneTypeSurgePrice::where(
                            'id',
                            $value
                        )->delete();
                    }
                } else {
                    if ($data['sruge_price'][$i][$key] != null) {
                        $zonetype = ZoneTypeSurgePrice::create([
                            'zone_type_id' => $zone_price->id,
                            'surge_price' => $data['sruge_price'][$i][$key],
                            'surge_distance_price' =>
                                $data['surge_distance_price'][$i][$key],
                            'start_time' => $data['start_time'][$i][$key],
                            'end_time' => $data['end_time'][$i][$key],
                            'available_days' => implode(
                                ',',
                                $data['available_days'][$i][$key]
                            ),
                            'status' => 1,
                        ]);
                    }
                }
            }
        }
        $zone->types_id = $vehicle_ids;
        $zone->save();
        session()->flash('message', 'Zone Updated Successfully!...');
        session()->flash('status', true);
        return redirect()->route('zone');
    }
    public function getTypePrices(Request $request)
    {
        if (is_array($request->type)) {
            $vehicle = Vehicle::whereIn('slug', $request->type)->get();
            return response()->json(['success' => true, 'vehicle' => $vehicle]);
        } else {
            $vehicle = Vehicle::where('slug', $request->type)->first();
            $zone_price = ZonePrice::with('getSurgePrice', 'getType')
                ->where('type_id', $vehicle->id)
                ->where('zone_id', $request->id)
                ->first();
            if (is_null($zone_price)) {
                return response()->json([
                    'success' => false,
                    'vehicle' => $vehicle,
                ]);
            }

            return response()->json([
                'success' => true,
                'datas' => $zone_price,
            ]);
        }
    }

    public function getZoneSrugePrice($slug)
    {
        $zone = Zone::where('slug', $slug)->first();
        if (is_null($zone)) {
            abort('404');
        }

        $zoneSurgePrice = ZoneSurgePrice::where('zone_id', $zone->id)->first();
        if ($zoneSurgePrice) {
            $zoneSurgePrice->available_days = explode(
                ',',
                $zoneSurgePrice->available_days
            );
        }
        return response()->json([
            'success' => true,
            'datas' => $zoneSurgePrice,
        ]);
    }

    public function getZoneDetails($slug)
    {
        $zone = Zone::where('slug', $slug)->first();
        if (is_null($zone)) {
            abort('404');
        }

        return response()->json(['success' => true, 'data' => $zone]);
    }

    public function getZoneSrugePriceSave(Request $request)
    {
        $data = $request->all();

        $zone = Zone::where('slug', $data['zone_id'])->first();
        if (is_null($zone)) {
            abort('404');
        }

        $zoneSurgePrice = ZoneSurgePrice::where('zone_id', $zone->id)->first();

        if ($zoneSurgePrice) {
            $zoneSurgePrice->surge_price = $data['surge_price'];
            $zoneSurgePrice->surge_distance_price =
                $data['surge_distance_price'];
            $zoneSurgePrice->start_time = $data['start_time'];
            $zoneSurgePrice->end_time = $data['end_time'];
            $zoneSurgePrice->available_days = implode(
                ',',
                $data['available_days']
            );
            $zoneSurgePrice->save();
        } else {
            $zoneSurgePrice = ZoneSurgePrice::create([
                'zone_id' => $zone->id,
                'surge_price' => $data['surge_price'],
                'surge_distance_price' => $data['surge_distance_price'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'available_days' => implode(',', $data['available_days']),
                'status' => 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'datas' => $zoneSurgePrice,
        ]);
    }

    public function viewFareAmount(Request $request)
    {
        $local = Zone::get();
        $rental = PackageMaster::where('status', 1)->get();
        $types = Vehicle::where('status', 1)->get();
        $outstation = OutstationPriceFixing::where('status', 1)->get();
        return view(
            'taxi.zone.FareAmountDetails',
            compact(['local', 'rental', 'outstation', 'types'])
        );
    }
}
