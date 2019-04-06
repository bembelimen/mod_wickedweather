<?php
/**
 * @package     Wicked Software
 * @subpackage  mod_wickedweather
 *
 * @copyright   Copyright (C) 2019 Wicked Software Benjamin Trenkle. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the breadcrumbs functions only once
JLoader::register('ModWickedWeatherHelper', __DIR__ . '/helper.php');

// Get the breadcrumbs
$weather  = ModWickedWeatherHelper::getWeather($params);

if (!$weather)
{
	return;
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_wickedweather', $params->get('layout', 'default'));
