<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\boilerplate\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $file = file_get_contents(public_path('/CountryCodes.json'));

        $file = \json_decode($file);

        // Country::whereNotNull('name')->delete();

        foreach($file as $key => $country) {
          
          $temp = (array)  $country;

          // Country::create($temp);
          Country::updateOrInsert([
            'name'=> $temp['name']
          ],$temp);

        }  

       

    }
}
