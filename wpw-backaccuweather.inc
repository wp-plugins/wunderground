<?php

require_once("wpw-backbase.inc");

class wpw_backAccuWeather extends wpw_backBase {
	function __construct() {
		parent::__construct();
		$this->furl = 'http://forecastfox.accuweather.com/adcbin/forecastfox/weather_data.asp?metric=1&location=';
	}
	function awCond($icon) {
		switch($icon) {
			case 1:
			case 2:
				$condition = $GLOBALS['CONDITION']->SUNNY;
				break;
			case 3:
			case 4:
				$condition = $GLOBALS['CONDITION']->PARTCLOUD;
				break;
			case 6:
			case 7:
			case 8:
				$condition = $GLOBALS['CONDITION']->CLOUDY;
				break;
			case 19:
			case 20:
			case 21:
			case 22:
			case 23:
			case 29:
				$condition = $GLOBALS['CONDITION']->SNOW;
				break;
			default:
				$condition = $GLOBALS['CONDITION']->SUNNY;
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
				$this->weather[] = new wpw_weatherData($high, $condition, $date, $low, $wind);
			}
			$i++;
		}
	}
}
