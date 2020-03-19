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
require_once __DIR__ . '/../../../../core/php/core.inc.php';
if (!class_exists('ZiBase')) {
	require_once __DIR__ . '/../../3rdparty/zibase.php';
}

class jeebase extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */
	
	public static $_widgetPossibility = array('custom' => true);
	
		
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'jeebase_php';
		$return['state'] = 'nok';
		$pid = trim( shell_exec ('ps ax | grep "jeebase/3rdparty/listen.php" | grep -v "grep" | wc -l') );
		if ($pid != '' && $pid != '0') {
		  $return['state'] = 'ok';
		  
		} else {
		}
		$return['launchable'] = 'ok';
		if (config::byKey('locale_ip', 'jeebase') == '' || config::byKey('zibase_ip', 'jeebase') == '') {
		  $return['launchable'] = 'nok';
		  $return['launchable_message'] = __('Erreur de configuration', __FILE__);
		}		
		return $return;		
	}
	
	public static function deamon_start() {
	    self::deamon_stop();
		$file_path = realpath(__DIR__ . '/../../3rdparty');	
		$ip_locale = config::byKey('locale_ip', 'jeebase');
		$ip_zibase = config::byKey('zibase_ip', 'jeebase');
		$cmd = 'php ' . $file_path . '/listen.php -a ' . $ip_zibase . ' -b ' . $ip_locale;
		$result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('jeebase_php') . ' 2>&1 &');
				
		$deamon_info = self::deamon_info();	
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
		  log::add('jeebase', 'error', 'Impossible de lancer le démon de jeebase');
		  return false;
		}
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
		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));
		$zibase->deregisterListener(config::byKey('locale_ip', 'jeebase'));	
	}	
	
    public function setInfoToJeedom($_options) {
		$eqLogic = jeebase::byLogicalId( $_options['id'],  'jeebase') ;	
		if ( is_object($eqLogic) ) {
			if (isset($_options['flag1']) && $_options['flag1'] == "Alarm") {
				$eqLogic->checkAndUpdateCmd('etat',1);
			}
			
			if ($eqLogic->getConfiguration('type_sonde') == 'temperature') { 
				$eqLogic->checkAndUpdateCmd("temperature", $_options['tem']);
				$eqLogic->checkAndUpdateCmd("humidity", $_options['hum']); 
			} elseif ($eqLogic->getConfiguration('type_sonde') == 'light') { 
				$eqLogic->checkAndUpdateCmd("luminosite", $_options['uvl']);
			} elseif ($eqLogic->getConfiguration('type_sonde') == 'power') { 
				$eqLogic->checkAndUpdateCmd("powerTotal", $_options['kwh']);
				$eqLogic->checkAndUpdateCmd("powerInstant", $_options['w']);
			} elseif ($eqLogic->getConfiguration('type_sonde') == 'rain') { 
				$eqLogic->checkAndUpdateCmd("PluieInstant", $_options['cra']);
				$eqLogic->checkAndUpdateCmd("PluieTotale", $_options['tra']);
			} elseif ($eqLogic->getConfiguration('type_sonde') == 'wind') { 
				$eqLogic->checkAndUpdateCmd("vitesse", $_options['kwh']);
				$eqLogic->checkAndUpdateCmd("orientation", $_options['drt']);
			}	
			log::add('jeebase', 'debug',' Info time ' . $eqLogic->getName() . ' ' . date('d/m/y H:i:s'));
			$eqLogic->checkAndUpdateCmd("time", date('d/m/y H:i:s'));
			(isset( $_options['bat']) && $_options['bat'] == "Low") ?   $eqLogic->batteryStatus(config::byKey('battery', 'jeebase'),date('Y-m-d H:i:s')) : $eqLogic->batteryStatus(100,date('Y-m-d H:i:s'));
			if (isset( $_options['bat'])) $eqLogic->checkAndCreateCommand('bat',$_options['bat'],'info','string');
			if (isset( $_options['rf'])) $eqLogic->checkAndCreateCommand('frequence',$_options['rf'],'info','string');
			if (isset( $_options['noise'])) $eqLogic->checkAndCreateCommand('noise',$_options['noise'],'info','numeric');
			if (isset( $_options['lev']))  $eqLogic->checkAndCreateCommand('level',$_options['lev'],'info','numeric');
			$eqLogic->setConfiguration('last_seen',date('Y-m-d H:i:s'));
			$eqLogic->save(true);				
		} else {
			$jeebase = jeebase::byTypeAndSearhConfiguration( 'jeebase', $_options['id']);
			if ( count($jeebase) > 0) {
				foreach ($jeebase as $eq) {
					if($eq->getConfiguration("type") == "other") {
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
								if ($cmd->getLogicalId() == 'on') $events = $eq->getConfiguration('action_on'); 
								if ($cmd->getLogicalId() == 'off') $events = $eq->getConfiguration('action_off');
								if (!empty($events)) {
									foreach ($events as $event) {
										try {
										  $options = array();
										  if (isset($event['options'])) {
											  $options = $event['options'];
										  }					  
											scenarioExpression::createAndExec('action', $event['cmd'], $options);
										} catch (Exception $e) {
											log::add('jeebase', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $event['cmd'] . __('. Détails : ', __FILE__) . $e->getMessage());
										}
									}
								}								
							}
						}
						if (isset( $_options['noise'])) $eq->checkAndCreateCommand('noise',$_options['noise'],'info','numeric');
						if (isset( $_options['lev']))  $eq->checkAndCreateCommand('level',$_options['lev'],'info','numeric'); 
						if (isset( $_options['bat'])) $eq->checkAndCreateCommand('bat',$_options['bat'],'string','other');
						if (isset( $_options['rf'])) $eq->checkAndCreateCommand('frequence',$_options['rf'],'info','string');
						(isset( $_options['bat']) && $_options['bat'] == "Low") ?   $eq->batteryStatus(config::byKey('battery', 'jeebase'),date('Y-m-d H:i:s')) : $eq->batteryStatus(100,date('Y-m-d H:i:s'));
						$eq->setConfiguration('last_seen',date('Y-m-d H:i:s'));
						$eq->save(true);		
						
					}
				}
			}
		}
	}

	public function setStateToJeedom($_options) {
		$jeebase = jeebase::byLogicalId( $_options['id'],  'jeebase') ;
	 	if ( is_object($jeebase) ) {
			$changed = false;
			$changed = $jeebase->checkAndUpdateCmd('etat',$_options['etat']) || $changed;
			if($changed && $jeebase->getConfiguration('pushState') != "") {
				$jeebase->pushCmdUrl($jeebase->getConfiguration('pushState'));
			}
			if ($_options['etat'] == 1) {
				 $events = $jeebase->getConfiguration('action_on'); 
				if($jeebase->getConfiguration('pushOn') != "") {
					$jeebase->pushCmdUrl($jeebase->getConfiguration('pushOn'));
				}				 
			} else {
				$events = $jeebase->getConfiguration('action_off');
				if($jeebase->getConfiguration('pushOff') != "") {
					$jeebase->pushCmdUrl($jeebase->getConfiguration('pushOff'));
				}				
			}
			if (!empty($events)) {
				foreach ($events as $event) {
					try {
					  $options = array();
					  if (isset($event['options'])) {
						  $options = $event['options'];
					  }					  
					  scenarioExpression::createAndExec('action', $event['cmd'], $options);
					} catch (Exception $e) {
						log::add('jeebase', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $event['cmd'] . __('. Détails : ', __FILE__) . $e->getMessage());
					}
				}
			}
			(isset( $_options['bat']) && $_options['bat'] == "Low") ?   $jeebase->batteryStatus(config::byKey('battery', 'jeebase'),date('Y-m-d H:i:s')) : $jeebase->batteryStatus(100,date('Y-m-d H:i:s'));			
			if (isset( $_options['noise'])) $jeebase->checkAndCreateCommand('noise',$_options['noise'],'info','numeric');
			if (isset( $_options['lev']) && is_numeric( $_options['lev']))  $jeebase->checkAndCreateCommand('level',$_options['lev'],'info','numeric');
			if (isset( $_options['bat'])) $jeebase->checkAndCreateCommand('bat',$_options['bat'],'info','string');
			if (isset( $_options['rf'])) $jeebase->checkAndCreateCommand('frequence',$_options['rf'],'info','string');
			$jeebase->setConfiguration('last_seen',date('Y-m-d H:i:s'));
			$jeebase->save(true);			
		} else { 
			$jeebase = jeebase::byTypeAndSearhConfiguration( 'jeebase', $_options['id']);
			if ( count($jeebase) > 0) {
				foreach ($jeebase as $eq) {
					if($eq->getConfiguration("type") == "other") {
						$changed = false;
						$changed = $eq->checkAndUpdateCmd('etat',$_options['etat']) || $changed;
						if($changed && $eq->getConfiguration('pushState') != "") $eq->pushCmdUrl($eq->getConfiguration('pushState'));
						if ($_options['etat'] == 1 && $eq->getConfiguration('pushOn') != "") $eq->pushCmdUrl($eq->getConfiguration('pushOn'));
						if ($_options['etat'] == 0 && $eq->getConfiguration('pushOff') != "") $eq->pushCmdUrl($eq->getConfiguration('pushOff'));
						$cmds = $eq->getCmd();
						foreach($cmds as $cmd) {
							if($cmd->getConfiguration('id') == $_options['id']) {
								if ($_options['etat'] == 1) {
									$events = $eq->getConfiguration('action_on');
									
								} else {
									$events = $eq->getConfiguration('action_off');																		
								}								
								if ($eq->getConfiguration('off') == '' && $eq->getConfiguration('raz') != '') {
									log::add('jeebase', 'debug', 'Creation du cron');
									$eq->setConfiguration('refresh',cron::convertDateToCron(strtotime("now") + 60 * $eq->getConfiguration('raz') +60));
									$eq->save();
								}
								if (!empty($events)) {
									foreach ($events as $event) {
										try {
										  $options = array();
										  if (isset($event['options'])) {
											  $options = $event['options'];
										  }					  
											scenarioExpression::createAndExec('action', $event['cmd'], $options);
										} catch (Exception $e) {
											log::add('jeebase', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $event['cmd'] . __('. Détails : ', __FILE__) . $e->getMessage());
										}
									}
								}
							}
						}
						if (isset( $_options['noise'])) $eq->checkAndCreateCommand('noise',$_options['noise'],'info','numeric');
						if (isset( $_options['lev']))  $eq->checkAndCreateCommand('level',$_options['lev'],'info','numeric'); 
						if (isset( $_options['bat'])) $eq->checkAndCreateCommand('bat',$_options['bat'],'info','string');
						if (isset( $_options['rf'])) $eq->checkAndCreateCommand('frequence',$_options['rf'],'info','string');
						(isset( $_options['bat']) && $_options['bat'] == "Low") ?   $eq->batteryStatus(config::byKey('battery', 'jeebase'),date('Y-m-d H:i:s')) : $eq->batteryStatus(100,date('Y-m-d H:i:s'));
						$eq->setConfiguration('last_seen',date('Y-m-d H:i:s'));
						$eq->save(true);	
					}
				}
			}	
		}
	}	
	
	public  function checkAndcreateCommand($_logical,$_value,$_type,$_subtype) {
		$jeebaseCmd = $this->getCmd(null, $_logical);	
		if ( !is_object($jeebaseCmd) ) {
			$jeebaseCmd = new jeebaseCmd();
			$jeebaseCmd->setName(__($_logical, __FILE__));
			$jeebaseCmd->setLogicalId($_logical);
			$jeebaseCmd->setEqLogic_id($this->getId());
			$jeebaseCmd->setType($_type);
			$jeebaseCmd->setSubType($_subtype);			
			$jeebaseCmd->save();				
		}
		$this->checkAndUpdateCmd($_logical, $_value);
		if($this->getConfiguration($_logical) != $_value) {
			$this->setConfiguration($_logical,$_value);
			$this->save(true);
		}
	}
	
	public  function pushCmdUrl($_url) {
		try {
			// create a new cURL resource
			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $_url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			// grab URL and pass it to the browser
			curl_exec($ch);
			// close cURL resource, and free up system resources
			curl_close($ch);
		} catch (Exception $e) {
			log::add('jeebase', 'error', __('Erreur lors de l\'éxecution de ' . $_url . ' ' . $e->getMessage()));
		}		
	}
	
//	public static function deregislistener() {
//		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));
//		$zibase->deregisterListener(config::byKey('locale_ip', 'jeebase'));			
//	}
	
	public static function pull($_eqLogic_id = null) {
		if(config::byKey('zibase_ip', 'jeebase') == "" || count(self::byType('jeebase')) == 0) {
			log::add('jeebase', 'debug',' Veuillez configurer les options. IP de la Zibase vide ou non correcte? Avez-vous synchronisé?');
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
						log::add('jeebase', 'debug',' Info module pour ' . $eqLogic->getName() . ' ' . $etat);
					}				
					$eqLogic->checkAndUpdateCmd('etat',$etat);
					break;
				case 'sonde':
					$id = $eqLogic->getConfiguration('sonde_os');
					$info = $zibase->getSensorInfo($id);
					if(is_numeric($info[0]) && (int)$info[0] == $info[0]) {
						$eqLogic->checkAndUpdateCmd("time", $info[0]->format("d/m/Y H:i:s"));
					}
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
	
	
	public static function cron() {
		
		$eqs = jeebase::byTypeAndSearhConfiguration( 'jeebase', 'other');
		if(count($eqs) > 0){
			foreach ($eqs as $jeebase) {
				$autorefresh = $jeebase->getConfiguration('refresh');
				if ($jeebase->getIsEnable() == 1 && $autorefresh != "" && $jeebase->getConfiguration('type') == "other" ) {
					try {
						$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
						if ($c->isDue()) {
							try {
								 $cmd = $jeebase->getCmd(null , 'off');
								 if (is_object($cmd)) {
									 $cmd->execCmd();
								 }							
								
							} catch (Exception $exc) {
								log::add('jeebase', 'error', __('Erreur pour ', __FILE__) . $jeebase->getHumanName() . ' : ' . $exc->getMessage());
							}
						} 
					} catch (Exception $exc) {
						log::add('jeebase', 'error', __('Expression cron non valide pour ', __FILE__) . $jeebase->getHumanName() . ' : ' . $autorefresh);
					}
				}
			}
		}
	}	
	
	

    /*     * *********************Methode d'instance************************* */

	  
	  
	public function loadCmdFromConf($type,$data = false) {
	
		if (!is_file(__DIR__ . '/../config/devices/' . $type . '.json')) {
			log::add('jeebase','debug', 'no file' . $type);
			return;
		}
		$content = file_get_contents(__DIR__ . '/../config/devices/' . $type . '.json');
		
		if (!is_json($content)) {
			log::add('jeebase','debug', 'no json content');
			return;
		}
		$device = json_decode($content, true);
		if (!is_array($device) || !isset($device['commands'])) {
			return true;
		}
		$link_cmds = array();
		foreach ($device['commands'] as $command) {
			$jeebaseCmd = $this->getCmd(null, $command['logicalId']);
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__($command['name'], __FILE__));
				$jeebaseCmd->setLogicalId($command['logicalId']);
				$jeebaseCmd->setEqLogic_id($this->getId());
				$jeebaseCmd->setType($command['type']);
				$jeebaseCmd->setSubtype($command['subtype']);
			}
			if($data) {
				$command['configuration'] = $data;
			}
			if($jeebaseCmd->getGeneric_type() == '') {
				$jeebaseCmd->setGeneric_type($command['generic_type']);
			}

			$jeebaseCmd->save();
			if (isset($command['value'])) {
				$link_cmds[$jeebaseCmd->getId()] = $command['value'];
			}			
		}
		if (count($link_cmds) > 0) {
			foreach ($this->getCmd() as $eqLogic_cmd) {
				foreach ($link_cmds as $cmd_id => $link_cmd) {
					if ($link_cmd == $eqLogic_cmd->getName()) {
						$cmd = cmd::byId($cmd_id);
						if (is_object($cmd)) {
							log::add('jeebase','debug', 'is_object');
							$cmd->setValue($eqLogic_cmd->getId());
							$cmd->save();
							log::add('jeebase','debug', 'cmd save ' . $cmd->getName());
						}
					}
				}
			}
		}		
	}	  
	  
	  
	 public function postSave() {

		if($this->getConfiguration("type") == "sensor") {
			$data = array();
			$data = array("id"=> $this->getConfiguration("id"),"protocole"=> $this->getConfiguration("protocole"));
			$this->loadCmdFromConf($this->getConfiguration('type'),$data);
		 
		 }
		 if($this->getConfiguration("type") == "module") {
			$data = array();
			if ($this->getConfiguration("protocole") == 6) {
				$id = substr($this->getConfiguration("id"), 1);
			} else {
				$id = $this->getConfiguration("id");
			}				
			
			
			$data = array("id"=> $id,"protocole"=> $this->getConfiguration("protocole"));	
			$this->loadCmdFromConf($this->getConfiguration('type'),$data);		 
			if ($this->getConfiguration('dim') == 1) { 
				$jeebaseCmd = $this->getCmd(null, 'slider');
				if ( !is_object($jeebaseCmd) ) {
					$jeebaseCmd = new jeebaseCmd();
					$jeebaseCmd->setName(__('Slider', __FILE__));
					$jeebaseCmd->setLogicalId('slider');
					$jeebaseCmd->setEqLogic_id($this->getId());					
				}
				$jeebaseCmd->setConfiguration("id",$id);
				$jeebaseCmd->setConfiguration("protocole",$this->getConfiguration("protocole"));
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('slider');
				$jeebaseCmd->save();
			}
			if ($this->getConfiguration('somfy') == 1) { 
				$jeebaseCmd = $this->getCmd(null, 'somfy');
				if ( !is_object($jeebaseCmd) ) {
					$jeebaseCmd = new jeebaseCmd();
					$jeebaseCmd->setName(__('My', __FILE__));
					$jeebaseCmd->setLogicalId('somfy');
					$jeebaseCmd->setEqLogic_id($this->getId());					
				}			
				$jeebaseCmd->setType('action');
				$jeebaseCmd->setSubType('other');
				$jeebaseCmd->save();
			}	
//			if ($this->getConfiguration('x2d_pilot') == 1) { 
//				$cmds = array('Off','Confort','Eco','Horsgel', 'Auto');
//				$jeebaseCmd = $this->getCmd(null, 'somfy');
//				if ( !is_object($jeebaseCmd) ) {
//					$jeebaseCmd = new jeebaseCmd();
//					$jeebaseCmd->setName(__('My', __FILE__));
//					$jeebaseCmd->setLogicalId('somfy');
//					$jeebaseCmd->setEqLogic_id($this->getId());					
//				}			
//				$jeebaseCmd->setType('action');
//				$jeebaseCmd->setSubType('other');
//				$jeebaseCmd->save();
//			}			
			
			
					
		 }
		 
		if($this->getConfiguration("type") == "sonde") {
			$this->loadCmdFromConf($this->getConfiguration('type_sonde'));
		}
		
		if($this->getConfiguration("type") == "other") {
			$jeebaseCmd = $this->getCmd(null, 'etat');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Etat', __FILE__));
				$jeebaseCmd->setLogicalId('etat');
				$jeebaseCmd->setEqLogic_id($this->getId());					
			}			
			$jeebaseCmd->setType('info');
			$jeebaseCmd->setSubType('binary');
			$jeebaseCmd->save();
			$id = $jeebaseCmd->getId();
			
			
			
			$jeebaseCmd = $this->getCmd(null, 'on');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('On', __FILE__));
				$jeebaseCmd->setLogicalId('on');
				$jeebaseCmd->setEqLogic_id($this->getId());					
			}			
			$jeebaseCmd->setConfiguration('id', $this->getConfiguration('on'));
			$jeebaseCmd->setType('action');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->setValue($id);
			$jeebaseCmd->save();
			
			$jeebaseCmd = $this->getCmd(null, 'off');
			if ( !is_object($jeebaseCmd) ) {
				$jeebaseCmd = new jeebaseCmd();
				$jeebaseCmd->setName(__('Off', __FILE__));
				$jeebaseCmd->setLogicalId('off');
				$jeebaseCmd->setEqLogic_id($this->getId());					
			}			
			$jeebaseCmd->setConfiguration('id', $this->getConfiguration('off'));
			$jeebaseCmd->setType('action');
			$jeebaseCmd->setSubType('other');
			$jeebaseCmd->setValue($id);
			$jeebaseCmd->save();
		}		
		
		
	}
	 
	
	public function postUpdate() {
		
		switch ($this->getConfiguration("type")) {
			case "sensor":$this->setLogicalId($this->getConfiguration("id"));
				break;
			case "module":
				$this->setLogicalId($this->getConfiguration("id"));
				break;
			case "sonde":
				$this->setLogicalId($this->getConfiguration("sonde_os"));
				break;
			
		}
		$this->save(true);		
		if($this->getConfiguration("type") == "other" ) {
			$jeebaseCmd = $this->getCmd(null, 'on');
			if (is_object($jeebaseCmd)) {
				$jeebaseCmd->setConfiguration('id',$this->getConfiguration("on"));
				$jeebaseCmd->save();
				
			}
			$jeebaseCmd = $this->getCmd(null, 'off');
			if (is_object($jeebaseCmd)) {
				$jeebaseCmd->setConfiguration('id',$this->getConfiguration("off"));
				$jeebaseCmd->save();
				
			}			
		}
	}
	
	public function deleteDataZibase() {
		$eqLogics = eqLogic::byType('jeebase');
		foreach ( $eqLogics as $eqLogic) {
			$eqLogic->remove();
			
		}
	}
}

class jeebaseCmd extends cmd {
	
    public function dontRemoveCmd() {
        return true;
    }
	
	public function imperihomeGenerate($ISSStructure) {
		$eqLogic = $this->getEqLogic();
		$object = $eqLogic->getObject();
		if( $eqLogic->getConfiguration('type') == 'sensor') {
			$type = 'DevMotion';
		} elseif ( $this->getLogicalId() == 'etat') {
			$type = 'DevSwitch';
		} elseif($this->getLogicalId() == 'slider') {
			$type = 'DevDimmer';
		} elseif($this->getLogicalId() == 'humidity') {
			$type = 'DevHygrometry';
		} elseif($this->getLogicalId() == 'temperature') {
			$type = 'DevTemperature';
		} elseif($this->getLogicalId() == 'luminosite') {
			$type = 'DevLuminosity';
		} elseif($this->getLogicalId() == 'powerTotal' || $this->getLogicalId() == 'powerInstant') {
			$type = 'DevElectricity';
		} elseif($this->getLogicalId() == 'PluieTotale' || $this->getLogicalId() == 'powerInstant') {
			$type = 'DevRain';
		} elseif($this->getLogicalId() == 'vitesse') {
			$type = 'DevRain';
		}
		else {
			return;
		}
		$info_device = array(
			'id' => $this->getId(),
			'name' => $eqLogic->getName(),
			'room' => (is_object($object)) ? $object->getId() : 99999,
			'type' => $type,
			'params' => array(),
		);
		if ($this->getType() == "info") {
				$info_device['params'] = $ISSStructure[$info_device['type']]['params'];
				$info_device['params'][0]['value'] = $this->execCmd();
	
		}
		return $info_device;
	}
	
	public function imperihomeAction($_action, $_value) {
		$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'etat') {
		    if ($_value == '0') {
				$eqLogic->getCmd(null, 'off')->execCmd();
		    } else {
				$eqLogic->getCmd(null, 'on')->execCmd();
		    }
		}
		
		if($this->getLogicalId() == "slider") {
			$cmd->execCmd(array('slider' => $_value));
		} else {
			$cmd->execCmd();
		}
		return;
	}
	
	public function imperihomeCmd() {
		return true;
	}
	
	public function execute($_options = array()) {
		if ($this->getType() != 'action') {
			return;
		}	
			
		$eqLogic = $this->getEqLogic();
		if($eqLogic->getConfiguration("type") == "other") {
			switch ($this->getLogicalId()) {
				case 'on' : 
					$eqLogic->checkAndUpdateCmd('etat',1);
					break;
				case 'off' : 
					$eqLogic->checkAndUpdateCmd('etat',0);
					break;
			}
			return;			
		}
		
		$zibase = new ZiBase(config::byKey('zibase_ip', 'jeebase'));	
		if ($this->getLogicalId() == 'on') {
			log::add('jeebase','debug', 'message :' .  $this->getConfiguration('id') . ' ZbAction::ON ' . ' ' . $this->getConfiguration('protocole'));
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::ON, $this->getConfiguration('protocole'));
		} elseif ($this->getLogicalId() == 'off') {
			$zibase->sendCommand($this->getConfiguration('id'), ZbAction::OFF, $this->getConfiguration('protocole'));
		} elseif ($this->getLogicalId() == 'slider') {
			 $zibase->sendCommand($this->getConfiguration('id'), ZbAction::DIM_BRIGHT, $this->getConfiguration('protocole'), $_options['slider']);
			 
		} elseif ($this->getLogicalId() == 'somfy') {
			 $zibase->sendCommand($eqLogic->getConfiguration('id'), ZbAction::DIM_BRIGHT, $eqLogic->getConfiguration('protocole'), 50);
			 $eqLogic->checkAndUpdateCmd('etat',1);
		}
    }
}

?>