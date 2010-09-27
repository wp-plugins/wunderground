=== Weather Forecast - WP Wunderground ===
Tags: weather, weather.com, wunderground, weatherbug, forecast, widget, shortcode, Yahoo weather, Yahoo! Weather, wp-weather, wp weather, local weather, weather man, weather widget, cool weather, accuweather, get weather
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: trunk
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=WP%20Wunderground%20for%20WordPress&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.

== Description ==

<h3>Wunderground is the best weather site.</h3>

Wunderground.com has the most accurate and in-depth weather information. They're also not evil corporate giants, and are weather geeks, which is nice. 

This plugin uses the Wunderground API to gather its accurate forcasts.


<h3>If you want a great-looking weather forecast, use this plugin.</h3>
__This is the best-looking weather forecast plugin for WordPress.__ It looks great on many different templates out of the box, including the default WP theme, TwentyTen.

The WP Wunderground plugin uses 10 great-looking icon sets from Wunderground.com, including: 

* <a href="http://icons-ecast.wxug.com/i/c/a/clear.gif" target="_blank" rel="nofollow">Default</a>
* <a href="http://icons-ecast.wxug.com/i/c/b/clear.gif" target="_blank" rel="nofollow">Smiley</a>
* <a href="http://icons-ecast.wxug.com/i/c/c/clear.gif" target="_blank" rel="nofollow">Generic</a>
* <a href="http://icons-ecast.wxug.com/i/c/d/clear.gif" target="_blank" rel="nofollow">Old School</a>
* <a href="http://icons-ecast.wxug.com/i/c/e/clear.gif" target="_blank" rel="nofollow">Cartoon</a>
* <a href="http://icons-ecast.wxug.com/i/c/f/clear.gif" target="_blank" rel="nofollow">Mobile</a>
* <a href="http://icons-ecast.wxug.com/i/c/g/clear.gif" target="_blank" rel="nofollow">Simple</a>
* <a href="http://icons-ecast.wxug.com/i/c/h/clear.gif" target="_blank" rel="nofollow">Contemporary</a>
* <a href="http://icons-ecast.wxug.com/i/c/i/clear.gif" target="_blank" rel="nofollow">Helen</a>
* <a href="http://icons-ecast.wxug.com/i/c/k/clear.gif" target="_blank" rel="nofollow">Incredible</a>

Check out the Screenshots section for pictures.

<h3>Using the WP Wunderground Weather Plugin</h3>
The plugin can be configured in two ways: 

1. Configure the plugin in the admin's WP Wunderground settings page, then add `[forecast]` to your content where you want the forecast to appear.
2. Go crazy with the `[forecast]` shortcode.

<h4>Using the `[forecast]` Shortcode</h4>
If you're a maniac for shortcodes, and you want all control all the time, this is a good way to use it.

`[forecast location="Tokyo, Japan" caption="Weather for Tokyo" measurement='F' todaylabel="Today" datelabel="date('m/d/Y')" highlow='%%high%%&deg;/%%low%%&deg;' numdays="3" iconset="Cartoon" class="css_table_class"]`

<strong>The shortcode supports the following settings:</strong>

* `location="Tokyo, Japan"` - Use any city/state combo or US/Canada ZIP code
* `caption="Weather for Tokyo"` - Add a caption to your table (it's like a title) 
* `measurement='F'` - Choose Fahrenheit or Celsius by using "F" or "C"
* `datelabel="date('m/d/Y')"` - Format the way the days display ("9/30/2012" in this example)
* `todaylabel="Today"` - Format how today's date appears ("Today" in this example)
* `highlow='%%high%%&deg;/%%low%%&deg;'` - Format how the highs & low temps display ("85&deg;/35&deg;" in this example)
* `numdays=3` - Change the number of days displayed in the forecast. Up to 6 day forecast.
* `iconset="Cartoon"` - Choose your iconset from one of 10 great options
* `class="css_table_class"` - Change the CSS class of the generated forecast table

<h4>Learn more on the <a href="http://www.seodenver.com/wunderground/">official plugin page</a></h4>

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. Go to the plugin settings page (under Settings > WP Wunderground)
1. Configure the settings on the page. (Instructions for some setting configurations are on the box to the right)
1. Click Save Changes.
1. When editing posts, use the `[forecast]` "shortcode" as described on this plugin's Description tab

== Screenshots ==

1. Embedded in content in the twentyten theme
1. Embedded in content in the twentyten theme, alternate settings
1. In the sidebar of the Motion theme
1. In the sidebar of the ChocoTheme theme
1. In the sidebar of the Piano Black theme
1. In the sidebar of the twentyten theme
1. Plugin configuration page

== Frequently Asked Questions == 

= I want to modify the forecast output. How do I do that? =

<pre>
function replace_weather($content) {
	$content = str_replace('Rain', 'RAYNNNNN!', $content);
	$content = str_replace('Snow', 'SNNNOOOO!', $content);
	return $content;
}
add_filter('wp_wunderground_forecast', 'replace_weather');
</pre>

= What is the plugin license? =

* This plugin is released under a GPL license.

= Do I need a Wunderground account? =
Nope, no account needed.

= This plugin slows down my site. =
It is recommended to use a caching plugin (such as WP Super Cache) with this plugin; that way the forecast isn't re-loaded every page load.

= Why would I want this plugin? =
This plugin is a perfect compliment for regional websites (about a city or town), personal websites where you want to share what the weather is like where you're blogging from, travel sites (show the weather at each stop!), <a href="http://www.denversnowremovalservice.com">snow removal</a> websites, yard services websites, and more.

== Changelog ==

= 1.0 =
* Initial launch

== Upgrade Notice ==

= 1.0 = 
* Blastoff!