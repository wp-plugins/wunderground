<?php
require_once("enum.inc");
$GLOBALS['TSCALE'] = new Enum('CELSIUS', 'FAHRENHEIT');   // Temp scale choices
$GLOBALS['MSCALE'] = new Enum('METRIC', 'ENGLISH');       // Other measurements
$GLOBALS['ORIENT'] = new Enum('VERT', 'HORIZ');           // Orientation (vertical / horitzontal
$GLOBALS['ALIGN']  = new Enum('LEFT', 'RIGHT', 'CENTER'); // Alignment (left / right / center)
$GLOBALS['CONDITION'] = new Enum('SUNNY', 'PARTCLOUD', 'CLOUDY', 'SNOW', 'RAIN');
$GLOBALS['CONDDATA'] = new DefinedEnum(Array(
     $GLOBALS['CONDITION']->SUNNY     => Array('slug' =>'sunny',     'text' => 'Sunny')
   , $GLOBALS['CONDITION']->PARTCLOUD => Array('slug' =>'partcloud', 'text' => 'Partly Cloudy')
   , $GLOBALS['CONDITION']->CLOUDY    => Array('slug' =>'cloudy',    'text' => 'Cloudy')
   , $GLOBALS['CONDITION']->SNOW      => Array('slug' =>'snow',      'text' => 'Snow')
   , $GLOBALS['CONDITION']->RAIN      => Array('slug' =>'rain',      'text' => 'Rain')
));

class wpw_weatherData {
	function wpw_weatherData($high, $condition, $date, $low = 0, $windspeed = -1) {
		$this->setHigh($high);
		$this->setLow($low);
		$this->setWindspeed($windspeed);
		$this->setCondition($condition);
		$this->setDate($date);
	}
	function setHigh($high) {
		$this->high = $high;
		return $this->high;
	}
	function getHigh($displayFormat) {
		switch($displayFormat) {
			case $GLOBALS['TSCALE']->FAHRENHEIT:
				$rval = ((9/5) * $this->high) + 32;
				break;
			default:
				$rval = $this->high;
				break;
		}
			
		return ceil($rval);
	}
	function setLow($low) {
		$this->low = $low;
		return $this->low;
	}
	function getLow($displayFormat) {
		global $TSCALE;
		switch($displayFormat) {
			case $GLOBALS['TSCALE']->FAHRENHEIT:
				$rval = ((9/5) * $this->low) + 32;
				break;
			default:
				$rval = $this->low;
				break;
		}
			
		return ceil($rval);
	}
	function setWindspeed($windspeed) {
		$this->windspeed = $windspeed;
		return $this->windspeed;
	}
	function getWindspeed($displayFormat) {
		return $this->windspeed;
	}
	function setCondition($condition) {
		$this->condition = $condition;
		return $this->condition;
	}
	function getCondition() {
		return $this->condition;
	}
	function getConditionSlug() {
		return $GLOBALS['CONDDATA']->{$this->condition}['slug'];
	}
	function getConditionText() {
		return $GLOBALS['CONDDATA']->{$this->condition}['text'];
	}
	function setDate($date) {
		$this->date = $date;
		return $this->date;
	}
	function getDate() {
		return $this->date;
	}
}
