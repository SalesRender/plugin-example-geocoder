<?php
/**
 * Created for plugin-core-geocoder
 * Date: 1/20/22 12:50 AM
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Instance\Geocoder;

use Adbar\Dot;
use Dadata\DadataClient;
use Leadvertex\Components\Address\Address;
use Leadvertex\Components\Address\Location;
use Leadvertex\Plugin\Components\Settings\Settings;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\GeocoderInterface;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\GeocoderResult;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\Timezone;
use Throwable;

class Geocoder implements GeocoderInterface
{

    public function handle(string $typing, Address $address): array
    {
        $settings = Settings::find();

        $dadata = new DadataClient(
            $settings->getData()->get('main.token'),
            $settings->getData()->get('main.secret'),
        );

        $result = [];

        if (!empty(trim($typing))) {
            $suggestions = $dadata->suggest("address", $typing);
            foreach ($suggestions as $suggestion) {
                $suggest = new Dot($suggestion);

                $location = null;
                if ($suggest->get('data.geo_lat') && $suggest->get('data.geo_lon')) {
                    $location = new Location(
                        $suggest->get('data.geo_lat'),
                        $suggest->get('data.geo_lon')
                    );
                }


                $handledAddress = new Address(
                    (string) $suggest->get('data.region_with_type', ''),
                    (string) $suggest->get('data.city_with_type', ''),
                    $suggest->get('data.street_with_type', '') . ' ' . $suggest->get('data.house', ''),
                    $suggest->get('data.flat_type_full', '') . ' ' . $suggest->get('data.flat', ''),
                    $suggest->get('data.postal_code', ''),
                    $suggest->get('data.country_iso_code'),
                    $location
                );

                $timezone = null;
                if ($suggest->get('data.timezone')) {
                    try {
                        $timezone = new Timezone($suggest->get('data.timezone'));
                    } catch (Throwable $throwable) {}
                }

                $result[] = new GeocoderResult($handledAddress, $timezone);
            }

            return $result;
        }

        $handled = new Dot($dadata->clean('address', (string) $address));
        if (empty($handled->count())) {
            return [];
        }

        $location = null;
        if ($handled->get('geo_lat') && $handled->get('geo_lon')) {
            $location = new Location(
                $handled->get('geo_lat'),
                $handled->get('geo_lon')
            );
        }

        $handledAddress = new Address(
            (string) $handled->get('region_with_type', ''),
            (string) $handled->get('city_with_type', ''),
            $handled->get('street_with_type', '') . ' ' . $handled->get('house', ''),
            $handled->get('flat_type_full', '') . ' ' . $handled->get('flat', ''),
            $handled->get('postal_code', ''),
            $handled->get('country_iso_code'),
            $location
        );

        $timezone = null;
        if ($handled->get('timezone')) {
            try {
                $timezone = new Timezone($handled->get('timezone'));
            } catch (Throwable $throwable) {}
        }

        return [new GeocoderResult($handledAddress, $timezone)];
    }
}