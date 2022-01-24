<?php
/**
 * Created for plugin-core-geocoder
 * Date: 1/20/22 12:50 AM
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Instance\Geocoder;

use Leadvertex\Components\Address\Address;
use Leadvertex\Components\Address\Location;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\GeocoderInterface;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\GeocoderResult;
use Leadvertex\Plugin\Core\Geocoder\Components\Geocoder\Timezone;

class Geocoder implements GeocoderInterface
{

    public function handle(string $typing, Address $address): array
    {
        if (!empty(trim($typing))) {
            $parts = explode(' ', $typing);
            $addressParts = [];

            $countryCode = null;
            $postalCode = null;
            foreach ($parts as $part) {
                if (preg_match('~^[A-Z]{2}$~', $part)) {
                    $countryCode = $part;
                    continue;
                }

                if (preg_match('~^\d{5,7}$~', $part)) {
                    $postalCode = $part;
                    continue;
                }

                $addressParts[] = $part;
            }

            $location_1 = null;
            $location_2 = null;

            if (count($addressParts) > 2 && $countryCode) {
                $location_1 = new Location(
                    $this->randomFloat(-90, 90),
                    $this->randomFloat(-180, 180),
                );

                $location_2 = new Location(
                    $this->randomFloat(-90, 90),
                    $this->randomFloat(-180, 180),
                );
            }

            $address_1 = new Address(
                (string) $addressParts[0] ?? '',
                (string) $addressParts[1] ?? '',
                (string) $addressParts[2] ?? '',
                (string) $addressParts[3] ?? '',
                $postalCode,
                $countryCode,
                $location_1
            );

            $address_2 = new Address(
                (string) $addressParts[3] ?? '',
                (string) $addressParts[2] ?? '',
                (string) $addressParts[1] ?? '',
                (string) $addressParts[0] ?? '',
                $postalCode,
                $countryCode,
                $location_2
            );

            $address_3 = new Address(
                (string) $parts[0] ?? '',
                (string) $parts[1] ?? '',
                (string) $parts[2] ?? '',
                (string) $parts[3] ?? '',
                (string) $parts[4] ?? ''
            );

           return [
               new GeocoderResult($address_1, new Timezone('Europe/Moscow')),
               new GeocoderResult($address_2, new Timezone('UTC+03:00')),
               new GeocoderResult($address_3, null),
           ];
        }

        $handledAddress = new Address(
            strtoupper($address->getRegion()),
            strtoupper($address->getCity()),
            strtoupper($address->getAddress_1()),
            strtoupper($address->getAddress_2()),
            strtoupper($address->getPostcode()),
            $address->getCountryCode(),
            $address->getLocation()
        );

        $timezone = null;
        if ($address->getCountryCode() && !empty($address->getRegion())) {
            $timezone = new Timezone('UTC+03:00');
        }

        return [new GeocoderResult($handledAddress, $timezone)];
    }

    private function randomFloat(float $min, float $max): float
    {
        return ($min+lcg_value()*(abs($max-$min)));
    }
}