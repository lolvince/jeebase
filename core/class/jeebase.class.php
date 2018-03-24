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
	
	
	
	public static function cron() {
		$eqs = jeebase::byTypeAndSearhConfiguration( 'jeebase', 'custom');
		if(count($eqs) > 0){
			foreach ($eqs as $jeebase) {
				$autorefresh = $jeebase->getConfiguration('refresh');
				log::add('jeebase', 'debug',' Cron ');
				if ($jeebase->getIsEnable() == 1 && $jeebase->getConfiguration('type_eq') == 'custom') {
					try {
						$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
						if ($c->isDue()) {
							log::add('jeebase', 'debug',' launch refresh ');
							try {
								 $cmd = $jeebase->getCmd(null , 'off');
								 if (is_object($cmd)) {
									 $cmd->execCmd();
								 }							
								
							} catch (Exception $exc) {
								log::add('jeebase', 'error', __('Erreur pour ', __FILE__) . $networks->getHumanName() . ' : ' . $exc->getMessage());
							}
						}
					} catch (Exception $exc) {
						log::add('ioscloud', 'error', __('Expression cron non valide pour ', __FILE__) . $cloud->getHumanName() . ' : ' . $autorefresh);
					}
				}
			}
		}
	}	
	
	public function syncWithZibase($_options) {
        if( config::byKey('zibase_url', 'jeebase') != ''){
        	$url=config::byKey('zibase_url', 'jeebase');
        }else{
        	$url="https://zibase.net";
        }
			
		
		$parsed_json = json_decode(file_get_contents($url."/api/get/ZAPI.php?zibase=".config::byKey('zibase_id', 'jeebase')."&token=".config::byKey('zibase_token', 'jeebase')."&service=get&target=home"),true);
		$modules = $parsed_json['body']['actuators'];
		$sensors = $parsed_json['body']['sensors'];
		$sondes = $parsed_json['body']['probes'];
		
		foreach ($modules as $module) {
			if ($module['protocol'] == 6) {
				$id = 'Z' . $module['id'];
			} else {
				$id = $module['id'];
				
			}
			
			$jeebase = jeebase::byLogicalId( $id,  'jeebase') ;
			if ( !is_object($jeebase) ) {	
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->setName($module['name']);
				$eqLogic->setConfiguration('type','module');
				$eqLogic->setConfiguration('protocole', $module['protocol']);
				$eqLogic->setConfiguration('id', $module['id']);
				$eqLogic->setLogicalId($id);
				$eqLogic->save();
				$eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('ON', __FILE__));
				$jeebaseCmd->setLogicalId('on');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('id', $module['id']);
				$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
				$jeebaseCmd->setConfiguration('icon', $module['icon']);
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->save();
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('OFF', __FILE__));
				$jeebaseCmd->setLogicalId('off');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('id', $module['id']);
				$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
				$jeebaseCmd->setConfiguration('etat', $module['status']);
				$jeebaseCmd->setConfiguration('icon', $module['icon']);
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->save();	
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Etat', __FILE__));
				$jeebaseCmd->setLogicalId('etat');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('id', $module['id']);
				$jeebaseCmd->setConfiguration('protocole', $module['protocol']);
				$jeebaseCmd->setConfiguration('etat', $module['status']);
				$jeebaseCmd->setConfiguration('icon', $module['icon']);
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('binary');
				$jeebaseCmd->save();
				
				
				if ($module['slider'] == 1) { 
					$jeebaseCmd = new jeebaseCmd();
					$jeebaseCmd->setName(__('Slider', __FILE__));
					$jeebaseCmd->setLogicalId('slider');
					$jeebaseCmd->setEqLogic_id($eqLogic->getId());
					$jeebaseCmd->setType('action');
					$jeebaseCmd->setSubType('slider');
					$jeebaseCmd->save();
				}
			}
		}
		
		foreach ($sensors as $sensor) {
			if ($sensor['protocol'] == 6) {
				$id = 'Z' . $sensor['id'];
			} else {
				$id = $sensor['id'];
			}	
			$jeebase = jeebase::byLogicalId( $id,  'jeebase') ;
			if ( !is_object($jeebase) ) {
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->setName($sensor['name']);
				$eqLogic->setLogicalId($id);
				$eqLogic->setConfiguration('type','sensor');
				$eqLogic->setConfiguration('protocole', $sensor['protocol']);
				$eqLogic->setConfiguration('id', $sensor['id']);					
				$eqLogic->save();
				$eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Etat Sensor', __FILE__));
				$jeebaseCmd->setLogicalId('etat');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('id', $sensor['id']);
				$jeebaseCmd->setConfiguration('protocole', $sensor['protocol']);
				$jeebaseCmd->setConfiguration('icon', $sensor['icon']);
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('binary');
				$jeebaseCmd->save();	
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Batterie', __FILE__));
				$jeebaseCmd->setLogicalId('batterie');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setIsVisible(0);
				$jeebaseCmd->setConfiguration('id', $sensor['id']);
				$jeebaseCmd->setConfiguration('protocole', $sensor['protocol']);
				$jeebaseCmd->setConfiguration('etat', $sensor['status']);
				$jeebaseCmd->setConfiguration('icon', $sensor['icon']);
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('binary');
				$jeebaseCmd->save();
			}
		}
		
		foreach ($sondes as $sonde) {
			$jeebase = jeebase::byLogicalId( $sonde['id'],  'jeebase') ;
			if ( !is_object($jeebase) ) {
				if($sonde['type'] == "temperature") {
									
					  
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setName($sonde['name']);
				$eqLogic->setLogicalId($sonde['id']);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('sonde_os',$sonde['id']);
				$eqLogic->setConfiguration('type_sonde','temperature');			
				$eqLogic->setCategory('heating', 1);
				$eqLogic->save();
				$eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Température', __FILE__));
				$jeebaseCmd->setLogicalId('temperature');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('data', 'temp');
				$jeebaseCmd->setIsHistorized(1);
				$jeebaseCmd->setUnite('°C');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('numeric');
				$jeebaseCmd->save();
		
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Humidité', __FILE__));
				$jeebaseCmd->setLogicalId('humidity');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('data', 'humidity');
				$jeebaseCmd->setIsHistorized(0);
				$jeebaseCmd->setUnite('%');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('numeric');
				$jeebaseCmd->save();
	
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('data', 'time');
				$jeebaseCmd->setIsHistorized(0);
				$jeebaseCmd->setUnite('');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->save();
				
			    } elseif($sonde['type'] == "light") {
				$eqLogic = new eqLogic();
				
				$eqLogic->setEqType_name('jeebase');
				$eqLogic->setIsEnable(1);
				$eqLogic->setName($sonde['name']);
				$eqLogic->setLogicalId($sonde['id']);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('sonde_os',$sonde['id']);
				$eqLogic->setConfiguration('type_sonde','light');
				$eqLogic->setCategory('heating', 1);
				$eqLogic->save();
				$eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Luminosité', __FILE__));
				$jeebaseCmd->setLogicalId('luminosite');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('data', 'lum');
				$jeebaseCmd->setIsHistorized(0);
				$jeebaseCmd->setUnite('lux');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('numeric');
				$jeebaseCmd->save();	
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Time', __FILE__));
				$jeebaseCmd->setLogicalId('time');
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setConfiguration('data', 'time');
				$jeebaseCmd->setIsHistorized(0);
				$jeebaseCmd->setUnite('');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->save();				
						 
			    } elseif($sonde['type'] == "power") {
					
			    $eqLogic = new eqLogic();
	            $eqLogic->setEqType_name('jeebase');
	            $eqLogic->setIsEnable(1);
	            $eqLogic->setName($sonde['name']);
	            $eqLogic->setLogicalId($sonde['id']);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('sonde_os',$sonde['id']);
				$eqLogic->setConfiguration('type_sonde','power');
				$eqLogic->setCategory('heating', 1);
	            $eqLogic->save();
	            $eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Consommation Totale', __FILE__));
                $jeebaseCmd->setLogicalId('powerTotal');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'powerTotal');
		        $jeebaseCmd->setIsHistorized(1);
		        $jeebaseCmd->setUnite('KW');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('numeric');
		        $jeebaseCmd->save();	
				
				$jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Consommation Instantanée', __FILE__));
                $jeebaseCmd->setLogicalId('powerInstant');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'powerInstant');
		        $jeebaseCmd->setIsHistorized(0);
		        $jeebaseCmd->setUnite('kW');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('numeric');
		        $jeebaseCmd->save();	
				
		        $jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Time', __FILE__));
                $jeebaseCmd->setLogicalId('time');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'time');
		        $jeebaseCmd->setIsHistorized(0);
		        $jeebaseCmd->setUnite('');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('other');
		        $jeebaseCmd->save();				
						
			    } elseif($sonde['type'] == "rain") {
				 
			    $eqLogic = new eqLogic();
	            $eqLogic->setEqType_name('jeebase');
	            $eqLogic->setIsEnable(1);
	            $eqLogic->setName($sonde['name']);
	            $eqLogic->setLogicalId($sonde['id']);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','rain');
				$eqLogic->setConfiguration('sonde_os',$sonde['id']);
				$eqLogic->setCategory('heating', 1);
	            $eqLogic->save();
	            $eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Pluie', __FILE__));
                $jeebaseCmd->setLogicalId('PluieInstant');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'pluie');
		        $jeebaseCmd->setIsHistorized(0);
		        $jeebaseCmd->setUnite('mm');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('numeric');
		        $jeebaseCmd->save();	
				
				$jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Pluie Tot', __FILE__));
                $jeebaseCmd->setLogicalId('PluieTotale');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'PluieTotale');
		        $jeebaseCmd->setIsHistorized(1);
		        $jeebaseCmd->setUnite('mm/h');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('numeric');
		        $jeebaseCmd->save();
				
		        $jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Time', __FILE__));
                $jeebaseCmd->setLogicalId('time');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'time');
		        $jeebaseCmd->setIsHistorized(0);
		        $jeebaseCmd->setUnite('');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('other');
		        $jeebaseCmd->save();
				
											
			    } elseif($sonde['type'] == "wind") {
					
			    $eqLogic = new eqLogic();
	            $eqLogic->setEqType_name('jeebase');
	            $eqLogic->setIsEnable(1);
	            $eqLogic->setName($sonde['name']);
	            $eqLogic->setLogicalId($sonde['id']);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('sonde_os',$sonde['id']);
				$eqLogic->setConfiguration('type_sonde','wind');
				$eqLogic->setCategory('heating', 1);
				$eqLogic->setObject_id($id);
	            $eqLogic->save();
	            $eqLogic = self::byId($eqLogic->getId());
				
				$jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Vent', __FILE__));
                $jeebaseCmd->setLogicalId('vitesse');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'vent');
		        $jeebaseCmd->setIsHistorized(1);
		        $jeebaseCmd->setUnite('km/h');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('numeric');
		        $jeebaseCmd->save();
				
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Orientation', __FILE__));
				$jeebaseCmd->setEqLogic_id($eqLogic->getId());
				$jeebaseCmd->setLogicalId('orientation');
				$jeebaseCmd->setConfiguration('data', 'orientation');
				$jeebaseCmd->setIsHistorized(0);
				$jeebaseCmd->setUnite('');
				$jeebaseCmd->setType('info');
				$jeebaseCmd->setSubType('numeric');
				$jeebaseCmd->save();				
				
		        $jeebaseCmd = new jeebaseCmd();
		        $jeebaseCmd->setName(__('Time', __FILE__));
                $jeebaseCmd->setLogicalId('time');
		        $jeebaseCmd->setEqLogic_id($eqLogic->getId());
		        $jeebaseCmd->setConfiguration('data', 'time');
		        $jeebaseCmd->setIsHistorized(0);
		        $jeebaseCmd->setUnite('');
		        $jeebaseCmd->setType('info');
		        $jeebaseCmd->setSubType('other');
		        $jeebaseCmd->save();
				}
			} else {
				if($sonde['type'] == "power") {
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Consommation Totale');
					$cmd->setLogicalId('powerTotal');
					$cmd->save();
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Consommation Instantanée');
					$cmd->setLogicalId('powerInstant');
					$cmd->save();	

				} elseif($sonde['type'] == "rain") {
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Pluie');
					$cmd->setLogicalId('PluieInstant');
					$cmd->save();
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Pluie Tot');
					$cmd->setLogicalId('PluieTotale');
					$cmd->save();	
									
				} elseif($sonde['type'] == "wind") {
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Vent');
					$cmd->setLogicalId('vitesse');
					$cmd->save();
					$cmd = cmd::byEqLogicIdCmdName($jeebase->getId(), 'Orientation');
					$cmd->setLogicalId('orientation');
					$cmd->save();					

				}
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
		if($this->getConfiguration("type_eq") == "custom") {
			
//			foreach($this->getCmd() as $cmd) {
//				$cmd->remove();
//				
//			}

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
		}
	}
	
	public function deleteDataZibase() {
		$eqLogics = eqLogic::byType('jeebase');
		foreach ( $eqLogics as $eqLogic) {
			$eqLogic->remove();
			
		}
	}
	public function updateDataZibase() {
		$eqLogics = eqLogic::byType('jeebase');
		foreach ( $eqLogics as $eqLogic) {
			if ($eqLogic->getConfiguration('type') == 'temperature') {
				$logical = $eqLogic->getConfiguration('sonde_os') ;
				$eqLogic->setLogicalId($logical);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','temperature');	
				$eqLogic->save();
				$cmds =  $eqLogic->getCmd();
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('data') == 'temp') {
						$cmd->setLogicalId('temperature');
						$cmd->save();
						
					} elseif ($cmd->getConfiguration('data') == 'humidity') {
						$cmd->setLogicalId('humidity');
						$cmd->save();						
					}
				}
			} elseif ($eqLogic->getConfiguration('type') == 'light') {
				$logical = $eqLogic->getConfiguration('sonde_os') ;
				$eqLogic->setLogicalId($logical);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','light');	
				$eqLogic->save();	
				$cmds =  $eqLogic->getCmd();
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('data') == 'lum') {
						$cmd->setLogicalId('luminosite');
						$cmd->save();
					} 
				}				
			} elseif ($eqLogic->getConfiguration('type') == 'power') {
				$logical = $eqLogic->getConfiguration('sonde_os') ;
				$eqLogic->setLogicalId($logical);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','power');	
				$eqLogic->save();	
				$cmds =  $eqLogic->getCmd();
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('data') == 'powerTotal') {
						$cmd->setLogicalId('powerTotal');
						$cmd->save();
						
					} elseif ($cmd->getConfiguration('data') == 'powerInstant') {
						$cmd->setLogicalId('powerInstant');
						$cmd->save();						
					}
				}				
			} elseif ($eqLogic->getConfiguration('type') == 'rain') {
				$logical = $eqLogic->getConfiguration('sonde_os') ;
				$eqLogic->setLogicalId($logical);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','rain');	
				$eqLogic->save();
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('data') == 'pluie') {
						$cmd->setLogicalId('PluieInstant');
						$cmd->save();
						
					} elseif ($cmd->getConfiguration('data') == 'PluieTotale') {
						$cmd->setLogicalId('PluieTotale');
						$cmd->save();						
					}
				}								
				
				
			} elseif ($eqLogic->getConfiguration('type') == 'wind') {
				$logical = $eqLogic->getConfiguration('sonde_os') ;
				$eqLogic->setLogicalId($logical);
				$eqLogic->setConfiguration('type','sonde');
				$eqLogic->setConfiguration('type_sonde','wind');	
				$eqLogic->save();
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('data') == 'vent') {
						$cmd->setLogicalId('vitesse');
						$cmd->save();
						
					} elseif ($cmd->getConfiguration('data') == 'orientation') {
						$cmd->setLogicalId('orientation');
						$cmd->save();						
					}
				}						
			}		
		}
	}
	
	
    public function getShowOnChild() {
        return true;
    }
	

		
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'jeebase_php';
		$return['state'] = 'nok';
		$pid = trim( shell_exec ('ps ax | grep "jeebase/3rdparty/listen.php" | grep -v "grep" | wc -l') );
		if ($pid != '' && $pid != '0') {
		  $return['state'] = 'ok';
		} 
		$return['launchable'] = 'ok';
		if (config::byKey('locale_ip', 'jeebase') == '' || config::byKey('zibase_ip', 'jeebase') == '') {
		  $return['launchable'] = 'ko';
		}		
		return $return;		
	}
	
	public static function deamon_start() {	
		self::deamon_stop();
		$file_path = realpath(dirname(__FILE__) . '/../../3rdparty');	
		$ip_locale = config::byKey('locale_ip', 'jeebase');
		$ip_zibase = config::byKey('zibase_ip', 'jeebase');
		$cmd = 'php ' . $file_path . '/listen.php -a ' . $ip_zibase . ' -b ' . $ip_locale;
		$result = exec('sudo ' . $cmd . ' >> ' . log::getPathToLog('jeebase_php') . ' 2>&1 &');
		if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
		  log::add('jeebase', 'error', $result);
		  return false;
		}	
		$i = 0;
		
		while ($i < 30) {
		  $deamon_info = self::deamon_info();
		  if ($deamon_info['state'] == 'ok') {
			break;
		  }
		  sleep(1);
		  $i++;
		}
		if ($i >= 30) {
		  log::add('jeebase', 'error', 'Impossible de lancer le démon jeebase', 'unableStartDeamon');
		  return false;
		}
		message::removeAll('jeebase', 'unableStartDeamon');
		log::add('jeebase', 'info', 'Démon jeebase lancé');
		return true;

		
	}
	
	public static function deamon_stop() {
		exec('kill $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] == 'ok') {
		  sleep(1);
		  exec('kill -9 $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
		}
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] == 'ok') {
		  sleep(1);
		  exec('sudo kill -9 $(ps aux | grep "jeebase/3rdparty/listen.php" | awk \'{print $2}\')');
		}		
	}

	public static function launchAction($_options) {
		log::add('jeebase', 'debug', 'launchAction Cron');
		if ($_options['eq_id'] != '') { 
				$eq = jeebase::byId($_options['eq_id']);
		} else {
			log::add('jeebase', 'debug', 'Erreur pour Id');
		}
		
		 if (is_object($eq)) {
			 $cmd = $eq->getCmd(null , 'off');
			 if (is_object($cmd)) {
				 $cmd->execCmd();
			 }
			//$eq->checkAndUpdateCmd('etat',0);
		 } else {
			 log::add('jeebase', 'debug', 'Equipement n\'existe pas');
		 }
	}


	
    public function setInfoToJeedom($_options) {
		$jeebase = jeebase::byTypeAndSearhConfiguration( 'jeebase', $_options['id']);
		
		
		if ( is_array($jeebase)) {
			foreach ($jeebase as $eq) {
				if($eq->getConfiguration("type_eq") == "custom") {
					log::add('jeebase', 'debug', 'name ' . $eq->getName());
					$cmds = $eq->getCmd();
					foreach($cmds as $cmd) {
						
						if($cmd->getConfiguration('id') == $_options['id']) {
							log::add('jeebase', 'debug', 'name ' . $cmd->getName());
							$cmd->execCmd();
							log::add('jeebase', 'debug', 'Off: ' . $eq->getConfiguration('off'));
							log::add('jeebase', 'debug', 'RAZ: ' . $eq->getConfiguration('raz'));
							
							if ($eq->getConfiguration('off') == '' && $eq->getConfiguration('raz') != '') {
								log::add('jeebase', 'debug', 'Creation du cron');
								$eq->setConfiguration('refresh',cron::convertDateToCron(strtotime("now") + 60 * $eq->getConfiguration('raz')));
								$eq->save();
//								$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($eq->getId()))); 
//								if (!is_object($cron)) {
//									$cron = new cron();
//									$cron->setClass('jeebase');
//									$cron->setFunction('launchAction');
//									$cron->setOption(array('eq_id' => intval($eq->getId())));
//								}
//								$cron->setEnable(1);
//								$cron->setSchedule(cron::convertDateToCron(strtotime("now") + 60 * $eq->getConfiguration('raz') + 60));
//								$cron->setOnce(1);
//								$cron->save();
							}							
							return;
						}
					}
	//				
	//				$cmd = $jeebase[0]->getCmd(null , $_options['id']);
	//				if ( is_object($cmd)) {
	//					$cmd->execCmd();	
	//					if ($jeebase[0]->getConfiguration('off') == '' && $jeebase[0]->getConfiguration('raz') != '') {
	//						log::add('jeebase', 'debug', 'RAZ sera exécutée à '.strtotime("now") + 60 * $jeebase[0]->getConfiguration('raz'));
	//						$cron = cron::byClassAndFunction('jeebase', 'launchAction', array('eq_id' => intval($jeebase[0]->getId()))); 
	//						if (!is_object($cron)) {
	//							$cron = new cron();
	//							$cron->setClass('jeebase');
	//							$cron->setFunction('launchAction');
	//							$cron->setOption(array('eq_id' => intval($jeebase[0]->getId())));
	//						}
	//						$cron->setEnable(1);
	//						$cron->setSchedule(cron::convertDateToCron(strtotime("now") + 60 * $jeebase[0]->getConfiguration('raz')));
	//						$cron->setOnce(1);
	//						$cron->save();
	//					}					
	//					return;
	//				} else {
	//					log::add('jeebase', 'debug', 'no utu ' .$_options['id']);
	//				}
				}
			}
		}
		
		
		
		$jeebase = jeebase::byLogicalId( $_options['id'],  'jeebase') ;	
		$changed = false;	
		if ( is_object($jeebase) ) {
			$changed = $jeebase->checkAndUpdateCmd("time", date('Y-m-d H:i:s')) || $changed;	
//			if(isset($_options['dev'])) {
//				if($_options['dev'] == "XDD") {
//					if( strstr($_options['rf'], "868Mhz")) { 
//						$changed = $jeebase->checkAndUpdateCmd("etat", 1) || $changed;	
//					} else {
//						$changed = $jeebase->checkAndUpdateCmd("etat", 0) || $changed;	
//					}
//				}
//			}			
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
			log::add('jeebase', 'debug', 'on ' . $this->getConfiguration('id') . ' ' . ' ' .$this->getConfiguration('protocole') );
			
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::ON, $this->getConfiguration('protocole'));
		} elseif ($this->getName() == 'OFF') {
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::OFF, $this->getConfiguration('protocole'));
		} elseif ($this->getName() == 'Slider') {
			 $zibase->sendCommand($this->getConfiguration('id'), ZbAction::ON, $this->getConfiguration('protocole'), $_options['slider']);
		}		
    }

}

?>