<?php

namespace ASamir\InDbPerformanceMonitor;

class IPInfo {

    /**
     * Return IP info using https://ipinfo.io/{ip}/json 
     * and https://restcountries.eu/rest/v2/alpha/{country_code}?fields=name
     * web services
     * @param string $ip 
     * @param bool $work 
     * @return array of structure
      [
      'ip' => $ip,
      'city' => '',
      'region' => '',
      'country' => '',
      'country_name' => '',
      'hostname' => '',
      'loc' => '',
      'org' => '',
      'is_finished' => 0
      ]
     */
    public static function getIPInfo($ip, $work = true) {
        // Case not work
        if (!$work)
            return self::getEmptyArray($ip);

        $info = [];
        try {
            // Get IP Data
            $url = 'https://ipinfo.io/' . $ip . '/json';
            $info = json_decode(file_get_contents($url), true);
            // Get Country name
            $url = 'https://restcountries.eu/rest/v2/alpha/' . $info['country'] . '?fields=name';
            $c = json_decode(file_get_contents($url), true);
            $info['country_name'] = $c['name'];
            $info['is_finished'] = 1;
        } catch (\Exception $e) {
            $info = [];
        }
        // Initialize missing data
        $info = array_merge(self::getEmptyArray($ip), $info);

        return $info;
    }

    /**
     * Return empty info array
     * @param type $ip
     * @return type
     */
    public static function getEmptyArray($ip) {
        return [
            'ip' => $ip,
            'city' => '',
            'region' => '',
            'country' => '',
            'country_name' => '',
            'hostname' => '',
            'loc' => '',
            'org' => '',
            'is_finished' => 0];
    }

}
