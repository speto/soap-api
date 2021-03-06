<?php
/**
 * Ip2GeoInputNormalisingClient.php
 *
 * @author dbojdo - Daniel Bojdo <daniel@bojdo.eu>
 * Created on 12 17, 2015, 16:42
 */

namespace Webit\SoapApi\Features\Ip2Geo;

use Webit\SoapApi\Executor\SoapApiExecutor;

class Ip2GeoInputNormalisingClient
{
    /**
     * @var SoapApiExecutor
     */
    private $executor;

    public function __construct(SoapApiExecutor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Ip $ip
     * @return GeoLocation
     */
    public function getGeoLocation(Ip $ip)
    {
        $result = $this->executor->executeSoapFunction('ResolveIP', $ip);

        return $this->hydrateToGeoLocation($result);
    }

    /**
     * @param $result
     * @return null|GeoLocation
     */
    private function hydrateToGeoLocation($result)
    {
        $result = isset($result->ResolveIPResult) ? $result->ResolveIPResult : null;
        if (! $result) {
            return null;
        }

        return new GeoLocation(
            $result->City,
            $result->StateProvince,
            $result->Country,
            $result->Organization,
            $result->Latitude,
            $result->Longitude,
            $result->AreaCode,
            $result->TimeZone,
            $result->HasDaylightSavings,
            $result->Certainty,
            $result->RegionName,
            $result->CountryCode
        );
    }
}
