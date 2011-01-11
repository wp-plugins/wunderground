<?php
include("enum.inc");
$test = new DefinedEnum(Array("test" => 'test', 'foo' => 'bar'));
echo $test->test;
echo $test->foo;
/*
$wpwtest = new wpw_backBase();
printf("%s\n", $wpwtest->getIconUrl());
$wpwtest->setTScale($TSCALE->FAHRENHEIT);
$wpwtest->getFeed();
$wpwtest->displayFeed();
*/
