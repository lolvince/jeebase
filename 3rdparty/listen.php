<?php


if (!class_exists('ZiBase')) {
	require_once __DIR__ . '/zibase.php';
}

require_once __DIR__ . '/../../../core/php/core.inc.php';

$arguments = getopt("a:b:c:");

$zibase = new ZiBase($arguments['a']);

 # Récupération en temps réel des messages reçus par la ZiBase
 $zibase->registerListener($arguments['b']);
 $socket = socket_create(AF_INET, SOCK_DGRAM, 0);

socket_bind($socket, "0.0.0.0" , 49999);

 
 

while (true) {
        socket_recvfrom($socket, $data, 512, 0, $remote_ip, $remote_port);
        $zbData = new ZbResponse($data);
		log::add('jeebase','debug', 'message :' .  $zbData->message);

		if( preg_match_all('#Received radio ID \(.*<rf>(.*?)</rf>.*CMD\/INTER</dev>.*<id>(.*?)(_OFF)?</id>.*#',$zbData->message,$results,PREG_SET_ORDER)) { 
			$data = array();
				$etat = (isset($results[0][3])) ? '0' : '1';
				$data['id'] = $results[0][2];
				$data['etat'] = $etat;
				jeebase::setStateToJeedom($data);
				
			
		}
		//Received radio ID (<rf>433Mhz Oregon</rf> Noise=<noise>2074</noise> Level=<lev>1.4</lev>/5 <dev>Temp-Hygro</dev> Ch=<ch>2</ch> T=<tem>+22.2</tem>C (+71.9F) Humidity=<hum>63</hum>%  Batt=<bat>Ok</bat>): <id>OS439179778</id>

		elseif ( preg_match_all('#Received radio ID.*#',$zbData->message,$results,PREG_SET_ORDER)) { 
			//print_r($results);
			if (preg_match_all( '#\([^\]]*\)#', $zbData->message, $m )) {
				$data = array();
				if(preg_match_all( '#<lev>(.*?)</lev>#', $m[0][0] ,$lev )) {
					$data['lev'] = $lev[1][0];
					
				};
				if(preg_match_all( '#<noise>(.*?)</noise>#', $m[0][0] ,$noise )) {
					$data['noise'] = $noise[1][0];
				};
				if(preg_match_all( '#<bat>(.*?)</bat>#', $m[0][0] ,$bat )) {
					$data['bat'] = $bat[1][0];
				};
				if(preg_match_all( '#<tem>(.*?)</tem>#', $m[0][0] ,$tem )) {
					$data['tem'] = $tem[1][0];
				};
				if(preg_match_all( '#<hum>(.*?)</hum>#', $m[0][0] ,$hum )) {
					$data['hum'] = $hum[1][0];
				};
				if(preg_match_all( '#<uvl>(.*?)</uvl>#', $m[0][0] ,$uvl )) {
					$data['uvl'] = $uvl[1][0];
				};
				if(preg_match_all( '#<kwh>(.*?)</kwh>#', $m[0][0] ,$kwh )) {
					$data['kwh'] = $kwh[1][0];
				};
				if(preg_match_all( '#<w>(.*?)</w>#', $m[0][0] ,$w )) {
					$data['w'] = $w[1][0];
				};
				if(preg_match_all( '#<awi>(.*?)</awi>#', $m[0][0] ,$awi)) {
					$data['awi'] = $awi[1][0];
				};	
				if(preg_match_all( '#<drt>(.*?)</drt>#', $m[0][0] ,$drt )) {
					$data['drt'] = $drt[1][0];
				};
				if(preg_match_all( '#<tra>(.*?)</tra>#', $m[0][0] ,$tra )) {
					$data['tra'] = $tra[1][0];
				};	
				if(preg_match_all( '#<cra>(.*?)</cra>#', $m[0][0] ,$cra )) {
					$data['cra'] = $cra[1][0];
				};
				if(preg_match_all( '#<dev>(.*?)</dev>#', $m[0][0] ,$dev )) {
					$data['dev'] = $dev[1][0];
				};	
				if(preg_match_all( '#<rf>(.*?)</rf>#', $m[0][0] ,$rf )) {
					$data['rf'] = $rf[1][0];
				};																						
								
				$exp = explode(": ", $zbData->message);
				if(preg_match_all( '#<id>(.*?)(_OFF|_ON)?</id>#', $exp[1] ,$id )) {
					$data['id'] = $id[1][0];
				};

				jeebase::setInfoToJeedom($data);
								
			}
			//Sent radio ID (1 Burst(s), Protocols='Domia' ): O8_ON
		} elseif(preg_match_all('#Sent radio ID \(.*Protocols=\'(.*?)\'.*: (([^_]+)(_OFF|_ON)?)#',$zbData->message,$results,PREG_SET_ORDER)) {
					$data= array();
					$data['id'] = $results[0][3];
					$etat = ($results[0][4] == '_OFF' ) ? '0' : '1';
					$data['etat'] = $etat;	
					jeebase::setStateToJeedom($data);				

		}
		

 }





?>