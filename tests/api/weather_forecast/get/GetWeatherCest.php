<?php

use Codeception\Util\HttpCode;

class GetWeatherCest
{
    private const APP_ID = '7deb821f74ecf5d9f3e655f3ec423f0c';

    public function try_to_get_the_weather_for_tomorrow (ApiTester $I): void
    {
        $I->am('API user');
        $I->wantTo('check the weather for tomorrow in New York');
        $I->lookForwardTo('failure of the test when the temperature is above 10 degree Celsius');

        $city = 'New York';
        $units = 'metric';

        $today_date = new DateTime('now');
        $tomorrow = ($today_date->modify('+ 1 day')->format('Y-m-d') . ' 00:00:00');

        codecept_debug($tomorrow);

        $I->sendGET('/data/2.5/forecast?q='.$city.'&APPID='.self::APP_ID.'&units='.$units.'&cnt=16');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $response = $I->grabResponse();
        $weatherResponse = json_decode($response, true);

        foreach ($weatherResponse['list'] as $day => $value) {
            if ($value['dt_txt'] === $tomorrow) {
                codecept_debug(
                    'Max temperature for tomorrow '.$value['dt_txt'].' will be '.$value['main'] ['temp_max'].''
                );
            }
        }

        $max_temperature = $value['main']['temp_max'];

        $I->assertLessThan(10, $max_temperature, 'Fail: Temperature is above 10 degree Celsius');

        }
}
