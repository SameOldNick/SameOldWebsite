<?php

namespace Database\Seeders\Setup\Countries;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SlimCountryStateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = require __DIR__ . '/countries.php';

        $slimmed = Arr::where($countries, function ($value) {
            $allowed = ['CAN', 'USA', 'GBR'];

            return in_array($value['code_alpha3'], $allowed);
        });

        foreach ($slimmed as $item) {
            $country = Country::create([
                'code' => $item['code_alpha3'],
                'code_alpha2' => $item['code_alpha2'],
                'country' => $item['country'],
            ]);

            foreach ($item['states'] as $state) {
                tap(new State([
                    'code' => $state['code'],
                    'state' => $state['name'],
                ]), function ($model) use ($country) {
                    $model->country()->associate($country);

                    $model->save();
                });
            }
        }
    }
}
