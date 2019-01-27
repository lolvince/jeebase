<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
require_once __DIR__ . '/../../../core/php/core.inc.php';

function jeebase_update() {
    $cron = cron::byClassAndFunction('jeebase', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
	$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($this->getId())));
	if (is_object($cron)) {
		$cron->remove();
	}
	
	foreach (jeebase::byType('jeebase', true) as $jeebase) {
		try {
			$jeebase->save();
		} catch (Exception $e) {

		}
	}	
}

function jeebase_remove() {
    $cron = cron::byClassAndFunction('jeebase', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
	$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($this->getId())));
	if (is_object($cron)) {
		$cron->remove();
	}		
}


?>
