<?php
require_once("enum.inc");
$GLOBALS['TSCALE'] = new Enum('CELSIUS', 'FAHRENHEIT');   // Temp scale choices
$GLOBALS['MSCALE'] = new Enum('METRIC', 'ENGLISH');       // Other measurements
$GLOBALS['ORIENT'] = new Enum('VERT', 'HORIZ');           // Orientation (vertical / horitzontal
$GLOBALS['ALIGN']  = new Enum('LEFT', 'RIGHT', 'CENTER'); // Alignment (left / right / center)
$GLOBALS['CONDITION'] = new Enum(  'CLEAR',  'PARTCLOUD',  'CLOUDY', 'SNOW', 'RAIN', 'HAZY'
                                 , 'NCLEAR', 'NPARTCLOUD', 'NCLOUDY', 'NSNOW', 'NRAIN', 'NHAZY'
                                 , 'FOG', 'HOT', 'COLD', 'WINDY' , 'WARNING', 'UNKNOWN'
                                 );
$GLOBALS['CONDDATA'] = new DefinedEnum(Array(
     $GLOBALS['CONDITION']->CLEAR     => Array('slug' =>'clear',     'text' => 'Clear')
   , $GLOBALS['CONDITION']->PARTCLOUD => Array('slug' =>'partcloud', 'text' => 'Partly Cloudy')
   , $GLOBALS['CONDITION']->CLOUDY    => Array('slug' =>'cloudy',    'text' => 'Cloudy')
   , $GLOBALS['CONDITION']->SNOW      => Array('slug' =>'snow',      'text' => 'Snow')
   , $GLOBALS['CONDITION']->RAIN      => Array('slug' =>'rain',      'text' => 'Rain')
   , $GLOBALS['CONDITION']->HAZY      => Array('slug' =>'hazy',      'text' => 'Hazy')
   , $GLOBALS['CONDITION']->NCLEAR    => Array('slug' =>'nclear',    'text' => 'Clear')
   , $GLOBALS['CONDITION']->NPARTCLOUD=> Array('slug' =>'npartcloud','text' => 'Partly Cloudy')
   , $GLOBALS['CONDITION']->NCLOUDY   => Array('slug' =>'ncloudy',   'text' => 'Clear')
   , $GLOBALS['CONDITION']->NSNOW     => Array('slug' =>'nsnow',     'text' => 'Snow')
   , $GLOBALS['CONDITION']->NRAIN     => Array('slug' =>'nrain',     'text' => 'Rain')
   , $GLOBALS['CONDITION']->NHAZY     => Array('slug' =>'nhazy',     'text' => 'Hazy')
   , $GLOBALS['CONDITION']->FOG       => Array('slug' =>'fog',       'text' => 'Fog')
   , $GLOBALS['CONDITION']->HOT       => Array('slug' =>'hot',       'text' => 'Hot')
   , $GLOBALS['CONDITION']->COLD      => Array('slug' =>'cold',      'text' => 'Cold')
   , $GLOBALS['CONDITION']->WINDY     => Array('slug' =>'windy',     'text' => 'Windy')
   , $GLOBALS['CONDITION']->WARNING   => Array('slug' =>'warning',   'text' => 'Warning')
   , $GLOBALS['CONDITION']->UNKNOWN   => Array('slug' =>'unknown',   'text' => 'Unknown')
));

class wpw_weatherData {
	function wpw_weatherData($high, $condition, $date, $low = 0, $windspeed = -1) {
		$this->setHigh($high);
		$this->setLow($low);
		$this->setWindspeed($windspeed);
		$this->setCondition($condition);
		$this->setDate($date);
		$this->setPrecip(0);
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
	function setSnow($snow) {
		$this->snow = $snow;
		return $this->snow;
	}
	function getSnow($displayFormat) {
		return $this->snow;
	}
	function displaySnow($displayFormat) {
		global $MSCALE;
		if($this->snow < 1) {
			return '';
		}
		switch($displayFormat) {
			case $GLOBALS['MSCALE']->ENGLISH:
				$rval = sprintf("%d in", ceil($this->snow / 2.54));
				break;
			default:
				$rval = sprintf("%d cm", round($this->snow));
				break;
		}
			
		return $rval;
	}
	function setPrecip($precip) {
		$this->precip = $precip;
		return $this->precip;
	}
	function getPrecip($displayFormat) {
		global $MSCALE;
		if($this->precip < 1) {
			return '';
		}
		switch($displayFormat) {
			case $GLOBALS['MSCALE']->ENGLISH:
				$rval = sprintf("%d\"", round($this->precip / 2.54));
				break;
			default:
				$rval = sprintf("%dcm", round($this->precip));
				break;
		}
			
		return $rval;
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
