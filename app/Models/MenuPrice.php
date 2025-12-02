<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPrice extends Model
{
    protected $fillable = ['type', 'meal_time', 'price'];

    public static function getPriceMap()
    {
        $prices = self::all()->keyBy(function ($item) {
            return $item->type . '_' . $item->meal_time;
        })->map(function ($item) {
            return $item->price;
        });

        $priceMap = [
            'standard' => [],
            'special' => [],
        ];

        foreach (['standard', 'special'] as $type) {
            foreach (['breakfast', 'am_snacks', 'lunch', 'pm_snacks', 'dinner'] as $meal) {
                $key = $type . '_' . $meal;
                $priceMap[$type][$meal] = $prices->get($key, self::getDefaultPrice($type, $meal));
            }
        }

        return $priceMap;
    }

    private static function getDefaultPrice($type, $meal)
    {
        $defaults = [
            'standard' => ['breakfast' => 150, 'am_snacks' => 150, 'lunch' => 300, 'pm_snacks' => 100, 'dinner' => 300],
            'special'  => ['breakfast' => 170, 'am_snacks' => 100, 'lunch' => 350, 'pm_snacks' => 150, 'dinner' => 350],
        ];

        return $defaults[$type][$meal] ?? 0;
    }
}
