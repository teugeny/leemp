<?php

include_once (LEVORIUM_VENDORS . DIRECTORY_SEPARATOR . 'GeoIP' . DIRECTORY_SEPARATOR . 'geoip.inc');

/**
 * Class UserIP
 */
class UserIP
{
    /**
     * @var mixed
     */
    private static $config;

    /**
     * @return UserIP
     */
    public function getInstance()
    {
        return new self();
    }

    /**
     * @return int|mixed
     */
    public static function getIP()
    {
        return self::getInstance()->getUserIP();
    }

    /**
     * @param $ip
     * @return bool
     */
    public static function getCodeByIP($ip)
    {
        return self::getInstance()->getUserCountryCode($ip);
    }
    /**
     * @return bool
     */
    public static function getCode()
    {
        return self::getInstance()->getUserCountryCode();
    }

    /**
     * @return int|mixed
     */
    private function getUserIP()
    {
        return self::$config['useCDN']
            ? $this->getUserIPTrowCDN()
            : $this->getRealUserIP();
    }

    /**
     * @param null $userIP
     * @return bool
     */
    private function getUserCountryCode($userIP = null)
    {
        $ip = $userIP == null
            ? $ip = $this->getIP()
            : $userIP;

        if((strpos($ip, ":") === false)) {
            $gi = geoip_open("{$this->getExtensionPath()}GeoIP.dat",GEOIP_STANDARD);
            $country = geoip_country_code_by_addr($gi, $ip);
        }
        else {
            $gi = geoip_open("{$this->getExtensionPath()}GeoIPv6.dat",GEOIP_STANDARD);
            $country = geoip_country_code_by_addr_v6($gi, $ip);
        }
        return $country;
    }

    /**
     * @return mixed
     */
    private function getRealUserIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return mixed
     */
    private function getUserIPTrowCDN()
    {
        $headers = Env::me()->get('Env') == 'develop'
            ? GeoIPCDNHeadersTest::me()->get()
            : getallheaders();

        return current(explode(",",preg_replace("/( )/","",$headers[self::$config['header']])));
    }

    /**
     * @return string
     */
    private function getExtensionPath()
    {
        return LEVORIUM_VENDORS . DIRECTORY_SEPARATOR . 'GeoIP' . DIRECTORY_SEPARATOR;
    }

    /**
     * UserIP constructor.
     */
    private function __construct()
    {
        self::$config = Env::me()->get("userIP");
    }

}