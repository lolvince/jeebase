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

try {
    require_once __DIR__ . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
	
	if (!class_exists('ZiBase')) {
		require_once __DIR__ . '/../../3rdparty/zibase.php';
	}

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

	if (init('action') == 'syncWithZibase') {
		jeebase::syncWithZibase();
		ajax::success();
	} elseif (init('action') == 'refreshDataZibase') {
        jeebase::pull();
        ajax::success();
    } elseif (init('action') == 'deleteDataZibase') {
        $zibase = jeebase::deleteDataZibase();
        ajax::success($return);
    } elseif (init('action') == 'getSonde') {
        $sonde = jeebase::byId(init('id'));
		
        if (!is_object($sonde)) {
            throw new Exception(__('Sonde inconnue verifié l\'id', __FILE__));
        }
        $return = utils::o2a($sonde);
        $return['cmd'] = array();
            foreach ($sonde->getCmd() as $cmd) {
				log::add('jeebase','debug','cmd : ' . print_r($cmd,true));
                $cmd_info = utils::o2a($cmd);
				if ($cmd->getType() == 'info') {
					 $cmd_info['value'] = $cmd->execCmd(null, 0);
				}
                $return['cmd'][] = $cmd_info;
            }
        ajax::success($return);
    } elseif (init('action') == 'includeEquipment') {	
		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));
		(init('protocol') == 6) ? $id = 0 : $id = init('id');
		(init('mode') == "ASSOC") ? $zibase->sendCommand($id, ZbAction::ASSOC,init('protocol')): $zibase->sendCommand($id, ZbAction::UNASSOC,init('protocol'));	
		 ajax::success();
	} elseif (init('action') == 'getUrl') {
		$cmd = cmd::byId(init('id'));
		if (is_object($cmd)) {
			 ajax::success($cmd->getDirectUrlAccess());
		}
	} elseif (init('action') == 'getActivity') {
		if (config::byKey('log::level::jeebase')[100] == 1) {
			ajax::success(1);
		} else {
			ajax::success(0);
		}
	} 

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
