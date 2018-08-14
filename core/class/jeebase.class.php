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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
if (!class_exists('ZiBase')) {
	require_once dirname(__FILE__) . '/../../3rdparty/zibase.php';
}

class jeebase extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */
	
	public static $_widgetPossibility = array('custom' => true);
	
	public static function deamon_info() {
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction('jeebase', 'pull');
		if (is_object($cron) && $cron->running()) {
			$return['state'] = 'ok';
		}
		$return['launchable'] = 'ok';
		return $return;
	}

	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction('jeebase', 'pull');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->run();
	}

	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('jeebase', 'pull');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->halt();
	}

	public static function deamon_changeAutoMode($_mode) {
		$cron = cron::byClassAndFunction('jeebase', 'pull');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->setEnable($_mode);
		$cron->save();
	}	
	
	public static function cronDaily() {
		self::deamon_start();
	}	
	
	public static function pull($_eqLogic_id = null) {
		if(config::byKey('zibase_ip', 'jeebase') == "" || count(self::byType('jeebase')) == 0) {
			log::add('jeebase', 'debug',' Veuillez configurer les options. IP de la Zibase vide ou non correcte? Avez-vous synchroniser?');
			return;
		}
		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));
		foreach(self::byType('jeebase') as $eqLogic) {
			if ($eqLogic->getIsEnable() != 1) {
				continue;
			}
			switch ($eqLogic->getConfiguration('type')) {
				case 'module':
					$id = $eqLogic->getConfiguration('id');
					if (preg_match('#^Z[A-Z][0-9]*#',$id)) {
						$id = substr($id, 1);
						$etat = $zibase->getState($id,true);
					} else {
						$etat = $zibase->getState($id);	
					}				
					$eqLogic->checkAndUpdateCmd('etat',$etat);
					break;
				case 'sonde':
					$id = $eqLogic->getConfiguration('sonde_os');
					$info = $zibase->getSensorInfo($id);
					$eqLogic->checkAndUpdateCmd("time", $info[0]->format("d/m/Y H:i:s"));
					log::add('jeebase', 'debug',' Info sonde pour ' . $eqLogic->getName() . ' ' . print_r($info,true));
					if ($eqLogic->getConfiguration('type_sonde') == 'temperature') { 
						$eqLogic->checkAndUpdateCmd("temperature", $info[1]/10);
						$eqLogic->checkAndUpdateCmd("humidity", $info[2]); 						
					} elseif ($eqLogic->getConfiguration('type_sonde') == 'light') { 
						$eqLogic->checkAndUpdateCmd("luminosite", $info[1]);
					} elseif ($eqLogic->getConfiguration('type_sonde') == 'power') { 
						$eqLogic->checkAndUpdateCmd("powerTotal", $info[1]);
						$eqLogic->checkAndUpdateCmd("powerInstant", $info[2]);
					} elseif ($eqLogic->getConfiguration('type_sonde') == 'rain') { 
						$eqLogic->checkAndUpdateCmd("PluieInstant", $info[1]);
						$eqLogic->checkAndUpdateCmd("PluieTotale", $info[2]);
					} elseif ($eqLogic->getConfiguration('type_sonde') == 'wind') { 
						$eqLogic->checkAndUpdateCmd("vitesse", $info[1]);
						$eqLogic->checkAndUpdateCmd("orientation", $info[2]);
					}					
					break;
				case 'sensor':
					$id = $eqLogic->getConfiguration('id');
					if (preg_match('#^Z[A-Z][0-9]*#',$id)) {
						$info = $zibase->getSensorInfo($id);
						log::add('jeebase', 'debug',' Info sensor pour ' . $eqLogic->getName() . ' ' . print_r($info,true));
						$id = substr($id, 1);
						$etat = $zibase->getState($id,true);
					} else {
						$info = $zibase->getSensorInfo($id);
						log::add('jeebase', 'debug',' Info sensor pour ' . $eqLogic->getName() . ' ' . print_r($info,true));						
						$etat = $zibase->getState($id);	
					}
					$eqLogic->checkAndUpdateCmd('etat',$etat);		
					break;					
			}
		}
	}
	
	
//	public static function cron() {
//		$eqs = jeebase::byTypeAndSearhConfiguration( 'jeebase', 'custom');
//		if(count($eqs) > 0){
//			foreach ($eqs as $jeebase) {
//				$autorefresh = $jeebase->getConfiguration('refresh');
//				log::add('jeebase', 'debug',' Cron pour ' . $jeebase->getName());
//				if ($jeebase->getIsEnable() == 1 && $jeebase->getConfiguration('type_eq') == 'custom') {
//					try {
//						$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
//						if ($c->isDue()) {
//							log::add('jeebase', 'debug',' launch refresh ');
//							try {
//								 $cmd = $jeebase->getCmd(null , 'off');
//								 if (is_object($cmd)) {
//									 $cmd->execCmd();
//								 }							
//								
//							} catch (Exception $exc) {
//								log::add('jeebase', 'error', __('Erreur pour ', __FILE__) . $jeebase->getHumanName() . ' : ' . $exc->getMessage());
//							}
//						}
//					} catch (Exception $exc) {
//						log::add('jeebase', 'error', __('Expression cron non valide pour ', __FILE__) . $jeebase->getHumanName() . ' : ' . $autorefresh);
//					}
//				}
//			}
//		}
//	}	
	
	public function syncWithZibase($_options = false) {
        if( config::byKey('zibase_url', 'jeebase') != ''){
        	$url=config::byKey('zibase_url', 'jeebase');
        }else{
        	$url="https://zibase.net";
        }
			
		
		$parsed_json = json_decode(file_get_contents($url."/api/get/ZAPI.php?zibase=".config::byKey('zibase_id', 'jeebase')."&token=".config::byKey('zibase_token', 'jeebase')."&service=get&target=home"),true);
		$modules = $parsed_json['body']['actuators'];
		$sensors = $parsed_json['body']['sensors'];
		$sondes = $parsed_json['body']['probes'];
		$scenarios = $parsed_json['body']['scenarios'];
		
		foreach ($scenarios as $scenario) {
			$id = $scenario['id'];
			$eqLogic = jeebase::byLogicalId( 'scenario_' . $id,  'jeebase');
			if ( !is_object($eqLogic) ) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setLogicalId('scenario_' . $id);
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);				
			}
			$eqLogic->setName($scenario['name']);
			$eqLogic->setConfiguration('type','scenario');
			$eqLogic->setConfiguration('id', $scenario['id']);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'launch');
			if (!is_object($jeebaseCmd)) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setLogicalId('launch');
				$jeebaseCmd->setName(__('Lancer', __FILE__));
			}		
			$jeebaseCmd->setEqLogic_id($eqLogic->id);
			$jeebaseCmd->setType('action');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();				
		}
		
		foreach ($modules as $module) {
			if ($module['protocol'] == 6) {
				$id = 'Z' . $module['id'];
			} else {
				$id = $module['id'];
				
			}
			
			$eqLogic = jeebase::byLogicalId( $id,  'jeebase') ;
			if ( !is_object($eqLogic) ) {	
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
			}
			$eqLogic->setName($module['name']);
			$eqLogic->setConfiguration('type','module');
			$eqLogic->setConfiguration('protocole', $module['protocol']);
			$eqLogic->setConfiguration('id', $module['id']);
			$eqLogic->setLogicalId($id);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'on');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('ON', __FILE__));
				$jeebaseCmd->setLogicalId('on');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('id', $module['id']);
			$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
			$jeebaseCmd->setConfiguration('icon', $module['icon']);
			$jeebaseCmd->setType('action');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'off');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('OFF', __FILE__));
				$jeebaseCmd->setLogicalId('off');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}			
			$jeebaseCmd->setConfiguration('id', $module['id']);
			$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
			$jeebaseCmd->setConfiguration('etat', $module['status']);
			$jeebaseCmd->setConfiguration('icon', $module['icon']);
			$jeebaseCmd->setType('action');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'etat');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Etat', __FILE__));
				$jeebaseCmd->setLogicalId('etat');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('id', $module['id']);
			$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
			$jeebaseCmd->setConfiguration('etat', $module['status']);
			$jeebaseCmd->setConfiguration('icon', $module['icon']);
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('binary');
			$jeebaseCmd->save();
				
			if ($module['slider'] == 1) { 
				$jeebaseCmd = $eqLogic->getCmd(null, 'slider');
				if ( !is_object($jeebaseCmd) ) {
					$jeebaseCmd = new jeebaseCmd();
					$jeebaseCmd->setName(__('Slider', __FILE__));
					$jeebaseCmd->setLogicalId('slider');
					$jeebaseCmd->setEqLogic_id($eqLogic->getId());					
				}
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('slider');
				$jeebaseCmd->save();
			}
		}
		
		foreach ($sensors as $sensor) {
			if ($sensor['protocol'] == 6) {
				$id = 'Z' . $sensor['id'];
			} else {
				$id = $sensor['id'];
			}	
			$eqLogic = jeebase::byLogicalId( $id,  'jeebase') ;
			if ( !is_object($eqLogic) ) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->setLogicalId($id);				
			}
			$eqLogic->setName($sensor['name']);
			$eqLogic->setConfiguration('type','sensor');
			$eqLogic->setConfiguration('protocole', $sensor['protocol']);
			$eqLogic->setConfiguration('id', $sensor['id']);					
			$eqLogic->save();

			$jeebaseCmd = $eqLogic->getCmd(null, 'etat');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Etat Sensor', __FILE__));
				$jeebaseCmd->setLogicalId('etat');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('id', $sensor['id']);
			$jeebaseCmd->setConfiguration('protocole', $sensor['protocol']);
			$jeebaseCmd->setConfiguration('icon', $sensor['icon']);
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('binary');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'batterie');
			if ( is_object($jeebaseCmd) ) {
				$jeebaseCmd->remove();			
				
			}
			
		}
		
		foreach ($sondes as $sonde) {
			$eqLogic = jeebase::byLogicalId( $sonde['id'],  'jeebase') ;
			if ( !is_object($eqLogic) ) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setName($sonde['name']);
				$eqLogic->setLogicalId($sonde['id']);
			}
			if($sonde['type'] == "temperature") {
								
			$eqLogic->setConfiguration('type','sonde');
			$eqLogic->setConfiguration('sonde_os',$sonde['id']);
			$eqLogic->setConfiguration('type_sonde','temperature');			
			$eqLogic->setCategory('heating', 1);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'temperature');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Température', __FILE__));
				$jeebaseCmd->setLogicalId('temperature');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'temp');
			$jeebaseCmd->setIsHistorized(1);
			$jeebaseCmd->setUnite('°C');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'humidity');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Humidité', __FILE__));
				$jeebaseCmd->setLogicalId('humidity');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
				
			}
			$jeebaseCmd->setConfiguration('data', 'humidity');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('%');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'time');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'time');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();
			
			} elseif($sonde['type'] == "light") {
			$eqLogic->setConfiguration('type','sonde');
			$eqLogic->setConfiguration('sonde_os',$sonde['id']);
			$eqLogic->setConfiguration('type_sonde','light');
			$eqLogic->setCategory('heating', 1);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'luminosite');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Luminosité', __FILE__));
				$jeebaseCmd->setLogicalId('luminosite');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
				
			}
			$jeebaseCmd->setConfiguration('data', 'lum');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('lux');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'time');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}

			$jeebaseCmd->setConfiguration('data', 'time');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();				
					 
			} elseif($sonde['type'] == "power") {
			$eqLogic->setConfiguration('type','sonde');
			$eqLogic->setConfiguration('sonde_os',$sonde['id']);
			$eqLogic->setConfiguration('type_sonde','power');
			$eqLogic->setCategory('heating', 1);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'powerTotal');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Consommation Totale', __FILE__));
				$jeebaseCmd->setLogicalId('powerTotal');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'powerTotal');
			$jeebaseCmd->setIsHistorized(1);
			$jeebaseCmd->setUnite('KW');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'powerInstant');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Consommation Instantanée', __FILE__));
				$jeebaseCmd->setLogicalId('powerInstant');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'powerInstant');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('kW');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();	
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'time');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'time');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();				
					
			} elseif($sonde['type'] == "rain") {
			$eqLogic->setConfiguration('type','sonde');
			$eqLogic->setConfiguration('type_sonde','rain');
			$eqLogic->setConfiguration('sonde_os',$sonde['id']);
			$eqLogic->setCategory('heating', 1);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'PluieInstant');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Pluie', __FILE__));
				$jeebaseCmd->setLogicalId('PluieInstant');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'pluie');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('mm');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'PluieTotale');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Pluie Tot', __FILE__));
				$jeebaseCmd->setLogicalId('PluieTotale');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'PluieTotale');
			$jeebaseCmd->setIsHistorized(1);
			$jeebaseCmd->setUnite('mm/h');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'time');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'time');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();
			
										
			} elseif($sonde['type'] == "wind") {
				
			$eqLogic->setConfiguration('type','sonde');
			$eqLogic->setConfiguration('sonde_os',$sonde['id']);
			$eqLogic->setConfiguration('type_sonde','wind');
			$eqLogic->setCategory('heating', 1);
			$eqLogic->setObject_id($id);
			$eqLogic->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'vitesse');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Vent', __FILE__));
				$jeebaseCmd->setLogicalId('vitesse');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'vent');
			$jeebaseCmd->setIsHistorized(1);
			$jeebaseCmd->setUnite('km/h');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'orientation');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Orientation', __FILE__));
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setLogicalId('orientation');				
			}
			$jeebaseCmd->setConfiguration('data', 'orientation');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('numeric');
			$jeebaseCmd->save();
			
			$jeebaseCmd = $eqLogic->getCmd(null, 'time');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());				
			}
			$jeebaseCmd->setConfiguration('data', 'time');
			$jeebaseCmd->setIsHistorized(0);
			$jeebaseCmd->setUnite('');
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->save();
			}
		}	
	}
	

    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {

	}
	
	 public function preRemove() {
		$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($this->getId())));
		if (is_object($cron)) {
			$cron->remove();
		}	
	 }	
	
	public function postUpdate() {
		if($this->getConfiguration("type_eq") == "custom" ) {
			if($this->getIsEnable() == 1) {
				$jeebaseCmd = $this->getCmd(null, 'etat');
				if (!is_object($jeebaseCmd)) {
					$jeebaseCmd = new jeebaseCmd();
				}
				$jeebaseCmd->setEqLogic_id($this->id);
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('binary');
				$jeebaseCmd->setName(__('Etat', __FILE__));
				$jeebaseCmd->setLogicalId('etat');	
				$jeebaseCmd->setIsVisible(1);			
				$jeebaseCmd->save();
				
				
				$jeebaseCmd = $this->getCmd(null, 'on');
				if (!is_object($jeebaseCmd)) {
					$jeebaseCmd = new jeebaseCmd();
				}
				$jeebaseCmd->setEqLogic_id($this->id);
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->setName(__('On', __FILE__));
				$jeebaseCmd->setLogicalId('on');
				$jeebaseCmd->setConfiguration('id',$this->getConfiguration("on"));	
				$jeebaseCmd->setIsVisible(1);			
				$jeebaseCmd->save();
				
				$jeebaseCmd = $this->getCmd(null,'off');
				if (!is_object($jeebaseCmd)) {
					$jeebaseCmd = new jeebaseCmd();
				}
				$jeebaseCmd->setEqLogic_id($this->id);
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->setName(__('Off', __FILE__));
				$jeebaseCmd->setLogicalId('off');	
				$jeebaseCmd->setConfiguration('id',$this->getConfiguration("off"));
				$jeebaseCmd->setIsVisible(1);			
				$jeebaseCmd->save();
			} else {
				$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($this->getId())));
				if (is_object($cron)) {
					$cron->remove();
				}				
			}
		}
	}
	
	public function deleteDataZibase() {
		$eqLogics = eqLogic::byType('jeebase');
		foreach ( $eqLogics as $eqLogic) {
			$eqLogic->remove();
			
		}
	}

	
    public function setInfoToJeedom($_options) {
		$jeebase = jeebase::byTypeAndSearhConfiguration( 'jeebase', $_options['id']);
		if ( count($jeebase) > 0) {
			foreach ($jeebase as $eq) {
				if($eq->getConfiguration("type_eq") == "custom") {
					log::add('jeebase', 'debug', 'name ' . $eq->getName());
					$cmds = $eq->getCmd();
					foreach($cmds as $cmd) {
						if($cmd->getConfiguration('id') == $_options['id']) {
							$cmd->execCmd();
							log::add('jeebase', 'debug', 'Cmd Name ' . $cmd->getName() . ' lancee. Off: ' . $eq->getConfiguration('off') . ' RAZ: ' . $eq->getConfiguration('raz'));							
							if ($eq->getConfiguration('off') == '' && $eq->getConfiguration('raz') != '') {
								log::add('jeebase', 'debug', 'Creation du cron');
								$eq->setConfiguration('refresh',cron::convertDateToCron(strtotime("now") + 60 * $eq->getConfiguration('raz') +60));
								$eq->save();
							}							
							return;
						}
					}
				}
			}
		}
		
		
		
		$jeebase = jeebase::byLogicalId( $_options['id'],  'jeebase') ;	
		$changed = false;	
		if ( is_object($jeebase) ) {
			$changed = $jeebase->checkAndUpdateCmd("time", date('Y-m-d H:i:s')) || $changed;			
			foreach ($_options as $key => $val) {
				switch ($key) {
					case "tem": $changed = $jeebase->checkAndUpdateCmd("temperature", $val) || $changed; break;
					case "hum": $changed = $jeebase->checkAndUpdateCmd("humidity", $val) || $changed; break;
					case "uvl": $changed = $jeebase->checkAndUpdateCmd("luminosite", $val) || $changed; break;
					case "kwh": $changed = $jeebase->checkAndUpdateCmd("powerTotal", $val) || $changed; break;
					case "w":   $changed = $jeebase->checkAndUpdateCmd("powerInstant", $val) || $changed; break;
					case "awi": $changed = $jeebase->checkAndUpdateCmd("vitesse", $val) || $changed; break; 
					case "drt": $changed = $jeebase->checkAndUpdateCmd("orientation", $val) || $changed; break;
					case "cra": $changed = $jeebase->checkAndUpdateCmd("PluieInstant", $val) || $changed; break;
					case "tra": $changed = $jeebase->checkAndUpdateCmd("PluieTotale", $val) || $changed; break; 				
				}
			}
		} 
		if ($changed) {
			$jeebase->refreshWidget();
		}
	}

	public function setStateToJeedom($_options) {
		$jeebase = jeebase::byLogicalId( $_options['id'],  'jeebase') ;
		$changed = false;
	 	if ( is_object($jeebase) ) {
			$changed = $jeebase->checkAndUpdateCmd('etat', $_options['etat']) || $changed;
		}
		if ($changed) {
			$jeebase->refreshWidget();
		}	
	}

	
}

class jeebaseCmd extends cmd {
	
    public function dontRemoveCmd() {
        return true;
    }
	
	public function execute($_options = array()) {
		log::add('jeebase', 'debug', 'execute');
		$eqLogic = $this->getEqLogic();
		if($eqLogic->getConfiguration("type_eq") == "custom") {
			switch ($this->getName()) {
				case 'On' : 
					$eqLogic->checkAndUpdateCmd('etat',1);
					break;
				case 'Off' : 
					$eqLogic->checkAndUpdateCmd('etat',0);
					break;
			}
			return;			
			
		}
		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));
		if ($this->getName() == 'ON') {
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::ON, $this->getConfiguration('protocole'));
		} elseif ($this->getName() == 'OFF') {
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::OFF, $this->getConfiguration('protocole'));
		} elseif ($this->getName() == 'Slider') {
			 $zibase->sendCommand($this->getConfiguration('id'), ZbAction::DIM_BRIGHT, $this->getConfiguration('protocole'), $_options['slider']);
		}		
    }

}

?>