<?php

namespace Database\Seeders;

use App\Models\Gis\City;
use App\Models\Gis\State;
use App\Models\Gis\District;
use App\Models\Gis\GisBoundarySubType;
use App\Models\Gis\GisBoundaryType;
use Illuminate\Database\Seeder;

class BoundaryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->loadLocations();
        $this->loadBoundaryType();
    }

    private function loadBoundaryType()
    {
        $data = $this->getData();
        foreach ($data['boundary_type'] as $boundaryType => $boundarySubTypes) {
            $gisBoundaryType = GisBoundaryType::firstOrCreate([
                'name' => $boundaryType,
            ]);

            foreach ($boundarySubTypes as $boundarySubType) {
                GisBoundarySubType::firstOrCreate([
                    'name' => $boundarySubType,
                    'gis_boundary_type_id' => $gisBoundaryType->id
                ]);
            }
        }
    }

    private function loadLocations()
    {
        $data = $this->getData();
        foreach ($data['states'] as $state => $districts) {
            $state = State::firstOrCreate([
                'name' => $state,
            ]);

            foreach ($districts as $district => $cities) {
                $district = District::firstOrCreate([
                    'name' => $district,
                    'state_id' => $state->id
                ]);

                foreach ($cities as $city) {
                    $city = City::firstOrCreate([
                        'name' => $city,
                        'district_id' => $district->id,
                        'state_id' => $state->id
                    ]);
                }
            }
        }
    }

    private function getData()
    {
        return  [
            'states' => [
                'state 1' => [
                    'district 1' => [
                        'city 1',
                        'city 2',
                        'city 3',
                    ],

                    'district 2' => [
                        'city 4',
                        'city 5',
                        'city 6',
                    ]
                ],

                'state 2' => [
                    'district 3' => [
                        'city 7',
                        'city 8',
                        'city 9',
                    ],

                    'district 4' => [
                        'city 10',
                        'city 11',
                        'city 12',
                    ],
                ]
            ],

            'boundary_type' => [
                'Political' => [
                    'Ward',
                    'Grama Panchayath',
                    'Assembly Constituency',
                    'Taluk Panchayath',
                    'Booth',
                    'Zilla Panchayath',
                    'LokSabha Constituency'
                ],

                'Administrative' => [
                    'Civil Police',
                    'Traffic Police',
                    'Waste Services',
                    'Water Services',
                    'ESCOM',
                    'Sanitation services',
                    'Rural Administration',
                    'Health Services - PHC',
                    'Health Services - CHC',
                    'District Hospital',

                ]
            ]
        ];
    }
}
