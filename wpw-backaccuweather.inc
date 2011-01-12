<?php

require_once("wpw-backbase.inc");

class wpw_backAccuWeather extends wpw_backBase {
	function __construct() {
		parent::__construct();
		// URL to the data feed
		$this->furl = 'http://forecastfox.accuweather.com/adcbin/forecastfox/weather_data.asp?metric=1&location=';
		// URL to the forecast for a given day
		$this->fcurl = "http://www.accuweather.com/us/in/knox/46534/forecast-details.asp?fday=";
	}
	/*
	 * Translates AccuWeather Condition code to the internal code
	 */
	function awCond($icon) {
		switch($icon) {
			case 1:
			case 2:
				$condition = $GLOBALS['CONDITION']->CLEAR;
				break;
			case 3:
			case 4:
				$condition = $GLOBALS['CONDITION']->PARTCLOUD;
				break;
			case 5:
				$condition = $GLOBALS['CONDITION']->HAZY;
				break;
			case 6:
			case 7:
			case 8:
				$condition = $GLOBALS['CONDITION']->CLOUDY;
				break;
			case 11:
				$condition = $GLOBALS['CONDITION']->FOG;
				break;
			case 12:
			case 13:
			case 14:
			case 15:
			case 16:
			case 17:
			case 18:
				$condition = $GLOBALS['CONDITION']->RAIN;
				break;
			case 19:
			case 20:
			case 21:
			case 22:
			case 23:
			case 24:
			case 25:
			case 26:
			case 29:
				$condition = $GLOBALS['CONDITION']->SNOW;
				break;
			case 30:
				$condition = $GLOBALS['CONDITION']->HOT;
				break;
			case 31:
				$condition = $GLOBALS['CONDITION']->COLD;
				break;
			case 32:
				$condition = $GLOBALS['CONDITION']->WINDY;
				break;
			case 33:
			case 34:
				$condition = $GLOBALS['CONDITION']->NCLEAR;
				break;
			case 35:
				$condition = $GLOBALS['CONDITION']->NPARTCLOUD;
				break;
			case 36:
			case 38:
				$condition = $GLOBALS['CONDITION']->NCLOUDY;
				break;
			case 37:
				$condition = $GLOBALS['CONDITION']->NHAZY;
				break;
			case 39:
			case 40:
			case 41:
			case 42:
				$condition = $GLOBALS['CONDITION']->NRAIN;
				break;
			case 43:
			case 44:
				$condition = $GLOBALS['CONDITION']->NSNOW;
				break;
			default:
				$condition = $GLOBALS['CONDITION']->UNKNOWN;
				break;
		}
		return $condition;
	}
	function doGetFeed() {
		try {
			$xmlStr = wp_remote_fopen(trim($this->furl . urlencode($this->getLocation())));
                	$xml = new SimpleXMLElement($xmlStr);
/*
		if(is_wp_error($xmlStr) || !$xml=simplexml_load_string($xmlStr)) {
			return '<!-- WPW-AccuWeather Error : Error reading XML file at '.$this->furl.$this->location.' -->'.$content;
		} elseif(empty($xml->simpleforecast->forecastday)) {
			return '<!-- WPW-AccuWeather Error : Weather feed was empty from '.$this->furl.$this->location.' -->'.$content;
		}
*/
		} catch (Exception $e) {
			return '<!-- WPW-AccuWeather Error : Error reading XML file at '.$this->furl.$this->getLocation().' -->'.$content;
		}
		$i = 0;
		foreach($xml->forecast->day as $data) {
			if($i < $this->getDays()) {
				list($month, $day, $year) = explode('/', $data->obsdate);
				$date = mktime(1, 0, 0, $month, $day, $year);
				$high = (int)$data->daytime->hightemperature;
				$low  = (int)$data->nighttime->lowtemperature;
				$wind = (int)$data->daytime->windspeed;
				$condition = $this->awCond($data->daytime->weathericon);
				switch($condition) {
					case $GLOBALS['CONDITION']->SNOW:
					case $GLOBALS['CONDITION']->NSNOW:
						$precip = $data->daytime->snowamount + $data->nighttime->snowamount;
						break;
					case $GLOBALS['CONDITION']->RAIN:
					case $GLOBALS['CONDITION']->NRAIN:
						$precip = $data->daytime->rainamount + $data->nighttime->rainamount;
						break;
					default:
						$precip = 0;
						break;
				}
				$newData = new wpw_weatherData($high, $condition, $date, $low, $wind);
				$newData->setPrecip($precip);
				$this->weather[] = $newData;
			}
			$i++;
		}
	}
	function getForecastLink($day) {
		$opts = '';
		if($this->getMScale() == $GLOBALS['MSCALE']->METRIC)
			$opts .= "&metric=1";
		else if($this->getMScale() == $GLOBALS['MSCALE']->ENGLISH)
			$opts .= "&metric=0";
		return($this->fcurl . $day . $opts);
	}
}
