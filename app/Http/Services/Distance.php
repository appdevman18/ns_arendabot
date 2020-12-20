<?php


namespace App\Http\Services;


use Illuminate\Support\Collection;
use Location\Coordinate;
use Location\Distance\Vincenty;

class Distance
{
//    protected $locationUs = ['latitude' => 51.115451, 'longitude' => 71.439056];
    const LATITUDE = 51.115451;
    const LONGITUDE = 51.115451;

    public static function getDistanceForPrice(float $latitude, float $longitude): int
    {
//        $coordinate_us = new Coordinate($this->locationUs['latitude'], $this->locationUs['longitude']);
        $coordinate_us = new Coordinate(self::LATITUDE, self::LONGITUDE);
        $coordinate_client = new Coordinate($latitude, $longitude);
        $calculator = new Vincenty();
        $distance = $calculator->getDistance($coordinate_us, $coordinate_client);

        return intval($distance);
    }

    private function removeKeyboard()
    {
        $this->bot->reply(
            '/menu',
            [
                'reply_markup' => json_encode(
                    Collection::make(
                        [
                            'remove_keyboard' => true,
                        ]
                    )->filter()
                ),
            ]
        );
    }
}

