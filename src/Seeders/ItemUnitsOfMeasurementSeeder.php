<?php

namespace Rutatiina\Item\Seeders;

use Illuminate\Database\Seeder;
use Rutatiina\Item\Models\ItemUnitOfMeasurement;
use Rutatiina\Tenant\Models\Tenant;

//php artisan db:seed --class=\\Rutatiina\\Item\\Seeders\\ItemUnitsOfMeasurementSeeder

class ItemUnitsOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ItemUnitOfMeasurement::truncate(); //truncate the table 
        
        $unitsOfMeasurement = collect([
            [
              'name' => 'Inch',
              'type' => 'Length',
              'symbol' => 'in',
            ],
            [
              'name' => 'Foot',
              'type' => 'Length',
              'symbol' => 'ft',
            ],
            [
              'name' => 'Yard',
              'type' => 'Length',
              'symbol' => 'yd',
            ],
            [
              'name' => 'Chain',
              'type' => 'Length',
              'symbol' => 'ch',
            ],
            [
              'name' => 'Furlong',
              'type' => 'Length',
              'symbol' => 'fur',
            ],
            [
              'name' => 'Mile',
              'type' => 'Length',
              'symbol' => 'mi',
            ],
            [
              'name' => 'League',
              'type' => 'Length',
              'symbol' => 'lea',
            ],
          //   [
          //     'name' => 'Inch of Mercury',
          //     'type' => 'Pressure',
          //     'symbol' => 'inHg',
          //   ],
          //   [
          //     'name' => 'Pound per square inch',
          //     'type' => 'Pressure',
          //     'symbol' => 'psi',
          //   ],
          //   [
          //     'name' => 'Miles per Hour',
          //     'type' => 'Speed',
          //     'symbol' => 'mph',
          //   ],
          //   [
          //     'name' => 'Fahrenheit',
          //     'type' => 'Temperature',
          //     'symbol' => '°F',
          //   ],
            [
              'name' => 'Gallon (US)',
              'type' => 'Volume',
              'symbol' => 'gal',
            ],
          //   [
          //     'name' => 'Gallon (US) per minute',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'gal/min',
          //   ],
          //   [
          //     'name' => 'Metre per Second squared',
          //     'type' => 'Acceleration',
          //     'symbol' => 'm/s²',
          //   ],
          //   [
          //     'name' => 'Standard Gravity',
          //     'type' => 'Acceleration',
          //     'symbol' => 'ɡₙ',
          //   ],
          //   [
          //     'name' => 'Mole',
          //     'type' => 'AmountOfSubstance',
          //     'symbol' => 'mol',
          //   ],
          //   [
          //     'name' => 'Deutscher Härtegrad',
          //     'type' => 'AmountOfSubstance',
          //     'symbol' => '°dH',
          //   ],
          //   [
          //     'name' => 'Radian',
          //     'type' => 'Angle',
          //     'symbol' => 'rad',
          //   ],
          //   [
          //     'name' => 'Degree',
          //     'type' => 'Angle',
          //     'symbol' => '°',
          //   ],
          //   [
          //     'name' => 'Minute Angle',
          //     'type' => 'Angle',
          //     'symbol' => '',
          //   ],
          //   [
          //     'name' => 'Second Angle',
          //     'type' => 'Angle',
          //     'symbol' => '\'',
          //   ],
            [
              'name' => 'Square Metre',
              'type' => 'Area',
              'symbol' => 'm²',
            ],
          //   [
          //     'name' => 'Dobson Unit',
          //     'type' => 'ArealDensity',
          //     'symbol' => 'DU',
          //   ],
          //   [
          //     'name' => 'Katal',
          //     'type' => 'CatalyticActivity',
          //     'symbol' => 'kat',
          //   ],
          //   [
          //     'name' => 'Bit',
          //     'type' => 'DataAmount',
          //     'symbol' => 'bit',
          //   ],
          //   [
          //     'name' => 'Byte',
          //     'type' => 'DataAmount',
          //     'symbol' => 'B',
          //   ],
          //   [
          //     'name' => 'Octet',
          //     'type' => 'DataAmount',
          //     'symbol' => 'o',
          //   ],
          //   [
          //     'name' => 'Bit per Second',
          //     'type' => 'DataTransferRate',
          //     'symbol' => 'bit/s',
          //   ],
          //   [
          //     'name' => 'Gram per cubic Metre',
          //     'type' => 'Density',
          //     'symbol' => 'g/m³',
          //   ],
          //   [
          //     'name' => 'Percent',
          //     'type' => 'Dimensionless',
          //     'symbol' => '%',
          //   ],
          //   [
          //     'name' => 'Parts per Million',
          //     'type' => 'Dimensionless',
          //     'symbol' => 'ppm',
          //   ],
          //   [
          //     'name' => 'Decibel',
          //     'type' => 'Dimensionless',
          //     'symbol' => 'dB',
          //   ],
          //   [
          //     'name' => 'Volt',
          //     'type' => 'ElectricPotential',
          //     'symbol' => 'V',
          //   ],
          //   [
          //     'name' => 'Farad',
          //     'type' => 'ElectricCapacitance',
          //     'symbol' => 'F',
          //   ],
          //   [
          //     'name' => 'Coulomb',
          //     'type' => 'ElectricCharge',
          //     'symbol' => 'C',
          //   ],
          //   [
          //     'name' => 'Ampere Hour',
          //     'type' => 'ElectricCharge',
          //     'symbol' => 'Ah',
          //   ],
          //   [
          //     'name' => 'Siemens',
          //     'type' => 'ElectricConductance',
          //     'symbol' => 'S',
          //   ],
          //   [
          //     'name' => 'Siemens per Metre',
          //     'type' => 'ElectricConductivity',
          //     'symbol' => 'S/m',
          //   ],
          //   [
          //     'name' => 'Ampere',
          //     'type' => 'ElectricCurrent',
          //     'symbol' => 'A',
          //   ],
          //   [
          //     'name' => 'Henry',
          //     'type' => 'ElectricInductance',
          //     'symbol' => 'H',
          //   ],
          //   [
          //     'name' => 'Ohm',
          //     'type' => 'ElectricResistance',
          //     'symbol' => 'Ω',
          //   ],
          //   [
          //     'name' => 'Joule',
          //     'type' => 'Energy',
          //     'symbol' => 'J',
          //   ],
          //   [
          //     'name' => 'Watt Second',
          //     'type' => 'Energy',
          //     'symbol' => 'Ws',
          //   ],
          //   [
          //     'name' => 'Watt Hour',
          //     'type' => 'Energy',
          //     'symbol' => 'Wh',
          //   ],
          //   [
          //     'name' => 'Volt-Ampere Hour',
          //     'type' => 'Energy',
          //     'symbol' => 'VAh',
          //   ],
          //   [
          //     'name' => 'Volt-Ampere Reactive Hour',
          //     'type' => 'Energy',
          //     'symbol' => 'varh',
          //   ],
          //   [
          //     'name' => 'Newton',
          //     'type' => 'Force',
          //     'symbol' => 'N',
          //   ],
          //   [
          //     'name' => 'Hertz',
          //     'type' => 'Frequency',
          //     'symbol' => 'Hz',
          //   ],
          //   [
          //     'name' => 'Lux',
          //     'type' => 'Illuminance',
          //     'symbol' => 'lx',
          //   ],
          //   [
          //     'name' => 'Irradiance',
          //     'type' => 'Intensity',
          //     'symbol' => 'W/m²',
          //   ],
          //   [
          //     'name' => 'Microwatt per square Centimeter',
          //     'type' => 'Intensity',
          //     'symbol' => 'µW/cm²',
          //   ],
            [
              'name' => 'Metre',
              'type' => 'Length',
              'symbol' => 'm',
            ],
          //   [
          //     'name' => 'Lumen',
          //     'type' => 'LuminousFlux',
          //     'symbol' => 'lm',
          //   ],
          //   [
          //     'name' => 'Candela',
          //     'type' => 'LuminousIntensity',
          //     'symbol' => 'cd',
          //   ],
          //   [
          //     'name' => 'Weber',
          //     'type' => 'MagneticFlux',
          //     'symbol' => 'Wb',
          //   ],
          //   [
          //     'name' => 'Tesla',
          //     'type' => 'MagneticFluxDensity',
          //     'symbol' => 'T',
          //   ],
            [
              'name' => 'Gram',
              'type' => 'Mass',
              'symbol' => 'g',
            ],
          //   [
          //     'name' => 'Watt',
          //     'type' => 'Power',
          //     'symbol' => 'W',
          //   ],
          //   [
          //     'name' => 'Volt-Ampere',
          //     'type' => 'Power',
          //     'symbol' => 'VA',
          //   ],
          //   [
          //     'name' => 'Volt-Ampere Reactive',
          //     'type' => 'Power',
          //     'symbol' => 'var',
          //   ],
          //   [
          //     'name' => 'Decibel-Milliwatts',
          //     'type' => 'Power',
          //     'symbol' => 'dBm',
          //   ],
          //   [
          //     'name' => 'Pascal',
          //     'type' => 'Pressure',
          //     'symbol' => 'Pa',
          //   ],
          //   [
          //     'name' => 'Hectopascal',
          //     'type' => 'Pressure',
          //     'symbol' => 'hPa',
          //   ],
          //   [
          //     'name' => 'Millimetre of Mercury',
          //     'type' => 'Pressure',
          //     'symbol' => 'mmHg',
          //   ],
          //   [
          //     'name' => 'Bar',
          //     'type' => 'Pressure',
          //     'symbol' => 'bar',
          //   ],
          //   [
          //     'name' => 'Becquerel',
          //     'type' => 'Radioactivity',
          //     'symbol' => 'Bq',
          //   ],
          //   [
          //     'name' => 'Gray',
          //     'type' => 'RadiationDoseAbsorbed',
          //     'symbol' => 'Gy',
          //   ],
          //   [
          //     'name' => 'Sievert',
          //     'type' => 'RadiationDoseEffective',
          //     'symbol' => 'Sv',
          //   ],
          //   [
          //     'name' => 'Steradian',
          //     'type' => 'SolidAngle',
          //     'symbol' => 'sr',
          //   ],
          //   [
          //     'name' => 'Metre per Second',
          //     'type' => 'Speed',
          //     'symbol' => 'm/s',
          //   ],
          //   [
          //     'name' => 'Knot',
          //     'type' => 'Speed',
          //     'symbol' => 'kn',
          //   ],
          //   [
          //     'name' => 'Kelvin',
          //     'type' => 'Temperature',
          //     'symbol' => 'K',
          //   ],
          //   [
          //     'name' => 'Celsius',
          //     'type' => 'Temperature',
          //     'symbol' => '°C',
          //   ],
          //   [
          //     'name' => 'Second',
          //     'type' => 'Time',
          //     'symbol' => 's',
          //   ],
          //   [
          //     'name' => 'Minute',
          //     'type' => 'Time',
          //     'symbol' => 'min',
          //   ],
          //   [
          //     'name' => 'Hour',
          //     'type' => 'Time',
          //     'symbol' => 'h',
          //   ],
          //   [
          //     'name' => 'Day',
          //     'type' => 'Time',
          //     'symbol' => 'd',
          //   ],
          //   [
          //     'name' => 'Week',
          //     'type' => 'Time',
          //     'symbol' => 'week',
          //   ],
          //   [
          //     'name' => 'Year',
          //     'type' => 'Time',
          //     'symbol' => 'y',
          //   ],
            [
              'name' => 'Litre',
              'type' => 'Volume',
              'symbol' => 'l',
            ],
            [
              'name' => 'Cubic Metre',
              'type' => 'Volume',
              'symbol' => 'm³',
            ],
          //   [
          //     'name' => 'Litre per Minute',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'l/min',
          //   ],
          //   [
          //     'name' => 'Cubic Metre per Second',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'm³/s',
          //   ],
          //   [
          //     'name' => 'Cubic Metre per Minute',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'm³/min',
          //   ],
          //   [
          //     'name' => 'Cubic Metre per Hour',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'm³/h',
          //   ],
          //   [
          //     'name' => 'Cubic Metre per Day',
          //     'type' => 'VolumetricFlowRate',
          //     'symbol' => 'm³/d',
          //   ],
          ]);

        
        Tenant::chunk(200, function ($tenants) use ($unitsOfMeasurement) {
            foreach ($tenants as $tenant) {
                $__uoms = $unitsOfMeasurement->map(function($uom) use ($tenant) { 
                    $uom['tenant_id'] = $tenant->id; 
                    return $uom; 
                });

                ItemUnitOfMeasurement::insert(
                    $__uoms->all()
                );
            }
        });

        $this->command->line('Tenant Units of Measurements created.');
        
    }
}
