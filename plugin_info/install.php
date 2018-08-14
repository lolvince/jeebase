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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function jeebase_install() {
	$cron = cron::byClassAndFunction('jeebase', 'pull');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('jeebase');
		$cron->setFunction('pull');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setDeamonSleepTime(1);
		$cron->setSchedule('* * * * *');
		$cron->setTimeout(1440);
		$cron->save();
	}
}


function jeebase_update() {
	$cron = cron::byClassAndFunction('jeebase', 'pull');
	if (!is_object($cron)) {
		$cron = new cron();
	}
	$cron->setClass('hueScheduler');
	$cron->setFunction('pull');
	$cron->setEnable(1);
	$cron->setDeamon(1);
	$cron->setDeamonSleepTime(1);
	$cron->setTimeout(1440);
	$cron->setSchedule('* * * * *');
	$cron->save();
	$cron->stop();
	deamon_stop('log');
}

function jeebase_remove() {
	$cron = cron::byClassAndFunction('hueScheduler', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}
	deamon_stop();
}

function deamon_info() {

	$return = array();
	$return['state'] = 'nok';
	$pid = trim( shell_exec ('ps ax | grep "jeebase/3rdparty/listen.php" | grep -v "grep" | wc -l') );
	if ($pid != '' && $pid != '0') {
	  $return['state'] = 'ok';
	} 	
	return $return;		
}

function deamon_stop($log = false) {
	exec('kill $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
	$deamon_info = deamon_info();
	if ($deamon_info['state'] == 'ok') {
	  sleep(1);
	  exec('kill -9 $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
	}
	$deamon_info = deamon_info();
	if ($deamon_info['state'] == 'ok') {
	  sleep(1);
	  exec('sudo kill -9 $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
	}
	$deamon_info = deamon_info();
		if($log) {
		if ($deamon_info['state'] == 'nok') {
			log::add('jeebase','error','mise à jour effectuée avec succés');
		} else {
			log::add('jeebase','error','Problème lors de la mise à jour');
		}
	}
}




?>
