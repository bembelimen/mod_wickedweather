<?php
/**
 * @package     Wicked Software
 * @subpackage  mod_wickedweather
 *
 * @copyright   Copyright (C) 2019 Wicked Software Benjamin Trenkle. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * Helper for mod_wickedweather
 *
 * @since  1.5
 */
class ModWickedWeatherHelper
{
	static protected $apiurl = 'https://api.openweathermap.org/data/2.5/forecast?id={CITYID}&APPID={APIKEY}';

	static protected $iconpath = 'media/mod_wickedweather/icons/dist/icons/{color}/svg/';

	static protected $icons = [
		2 => 'tstorms',
		210 => 'chancetstorms',
		3 => 'chancerain',
		5 => 'rain',
		511 => 'sleet',
		6 => 'snow',
		600 => 'chanceflurries',
		602 => 'flurries',
		61 => 'sleet',
		612 => 'chancesleet',
		615 => 'chancesleet',
		620 => 'chancesleet',
		62 => 'sleet',
		7 => 'fog',
		800 => 'clear',
		801 => 'mostlysunny',
		802 => 'mostlycloudy',
		803 => 'cloudy',
		804 => 'cloudy'
	];

	static public function getWeather($params)
	{
		if (!$params->get('apikey'))
		{
			return false;
		}

		$options = [
			'lifetime' => 60,
			'storage' => 'file',
			'defaultgroup' => 'wickedweather',
			'caching' => true
		];

		$cache = Cache::getInstance('callback', $options);

		$weather = $cache->get('ModWickedWeatherHelper::loadWeatherInformation', [$params]);

		// Delete cache if loading didn't work
		if ($weather === false)
		{
			$cache->clean();
		}

		return $weather;
	}

	static public function loadWeatherInformation($params)
	{
		$http = HttpFactory::getHttp();

		$city_id = $params->get('city_id');
		$apikey = $params->get('apikey');

		if (empty($city_id) || empty($apikey))
		{
			return false;
		}

		$url = str_replace(['{CITYID}', '{APIKEY}'], [$city_id, $apikey], static::$apiurl);

		switch ($params->get('unit'))
		{
			case 'imperial':
			case 'metric':
				$url .= '&units=' . $params->get('unit');
				break;
		}

		$result = $http->get($url);

		if ($result->code != 200)
		{
			return false;
		}

		$content = new Registry($result->body);

		$forcasts = $content->get('list', []);

		if (empty($forcasts) || !is_array($forcasts))
		{
			return false;
		}

		$forcast = reset($forcasts);

		if (empty($forcast->weather) || !is_array($forcast->weather) || !isset($forcast->main->temp))
		{
			return false;
		}

		$weather = reset($forcast->weather);

		$result = new stdClass;

		$iconprefix = !empty($forcast->sys->pod) && $forcast->sys->pod == 'n' ? 'nt_' : '';

		$icon = 'unknown.svg';

		if (isset(static::$icons[$weather->id]))
		{
			$icon = static::$icons[$weather->id] . '.svg';
		}
		elseif (isset(static::$icons[(int) ($weather->id / 10)]))
		{
			$icon = static::$icons[(int) ($weather->id / 10)] . '.svg';
		}
		elseif (isset(static::$icons[(int) ($weather->id / 100)]))
		{
			$icon = static::$icons[(int) ($weather->id / 100)] . '.svg';
		}

		$result->icon = HTMLHelper::_('image', str_replace('{color}', $params->get('color', 'solid-black'), static::$iconpath) . $iconprefix . $icon, '', null, false);

		$shortunit = 'KELVIN';

		switch ($params->get('unit'))
		{
			case 'imperial':
				$shortunit = 'FAHRENHEIT';
				break;
			case 'metric':
				$shortunit = 'CELCIUS';
				break;
		}

		$result->temperature = Text::sprintf('MOD_WICKEDWEATHER_PARAM_UNIT_' . $shortunit . '_SHORT', number_format((float) $forcast->main->temp, 2, Text::_('DECIMALS_SEPARATOR'), Text::_('THOUSANDS_SEPARATOR')));

		return $result;
	}
}
