<?php
/**
 * @package     Wicked Software
 * @subpackage  mod_wickedweather
 *
 * @copyright   Copyright (C) 2019 Wicked Software Benjamin Trenkle. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'mod_wickedweather/wickedweather.css', ['relative' => true]);

?>
<div id="mod_wickedweather-<?php echo (int) $module->id; ?>" class="mod_wickedweather">
	<ul>
		<li><?php echo $weather->temperature; ?></li>
		<li><?php echo $weather->icon; ?></li>
	</ul>
</div>