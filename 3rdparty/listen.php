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
  
  	//////pour debug !
  	//var_dump($data);
  	//var_dump(bin2hex($data));
  	//var_dump($zbData->param1);
  	//var_dump(bin2hex($zbData->param1));
  	//var_dump($zbData->param2);
  	//var_dump(bin2hex($zbData->param2));
  	//var_dump($zbData->param3);
 	//var_dump(bin2hex($zbData->param3));
  	//var_dump($zbData->param4);
 	//var_dump(bin2hex($zbData->param4));
    	//////
	
	//Rafraichi les infos Zibases
	jeebase::UpdateZibaseInfoToJeedom($zbData->zibaseId ,$remote_ip);
	
	if ( substr($zbData->reserved1, 0, 7) == "SLAMSIG" && $zbData->param1 == 0 && $zbData->param2 == 0 && $zbData->param3 == 1 && $zbData->param4 == 0 ) {
		echo  date('Y-m-d H:i:s') . ' : ' . $zbData->zibaseId . '(' . $remote_ip . ') => Fin de Boot Zibase, demande de ratachement !';
		$zibase->registerListener($arguments['b']);
	}elseif( substr($zbData->reserved1, 0, 7) == "SLAMSIG" && $zbData->param1 == 0 && $zbData->param2 == 0 && $zbData->param3 == 0 && $zbData->param4 == 0 ){
		echo  date('Y-m-d H:i:s') . ' : ' . $zbData->zibaseId . '(' . $remote_ip . ') => Boot Zibase';
	}else{
		if( config::byKey('log::level::jeebase')['100'] == 1) {
			echo  date('Y-m-d H:i:s') . ' : ' . $zbData->zibaseId . '(' . $remote_ip . ')' . ' => '. $zbData->message . PHP_EOL;
		}
	}

	if( preg_match_all('#Received radio ID \(.*<rf>(.*?)</rf>.*CMD\/INTER</dev>.*<id>(.*?)(_OFF)?</id>.*#',$zbData->message,$results,PREG_SET_ORDER)) { 
		echo 'receive type 1 :' . PHP_EOL;
		$data = getData($zbData->message);
		$etat = (isset($results[0][3])) ? '0' : '1';
		$data['id'] = $results[0][2];
		$data['etat'] = $etat;
		print_r($data);
		jeebase::setStateToJeedom($data);

	}elseif( preg_match_all('#Received radio ID.*#',$zbData->message,$results,PREG_SET_ORDER)) { 
		//echo 'receive type 2 :' . PHP_EOL;
		$data = getData($zbData->message);
		$exp = explode(": ", $zbData->message);
		if(preg_match_all( '#<id>(.*?)(_OFF|_ON)?</id>#', $exp[1] ,$id )) {
			$data['id'] = $id[1][0];
		};			
		//print_r($data);
		jeebase::setInfoToJeedom($data);
		
	}elseif(preg_match_all('#Sent radio ID \(.*Protocols=\'(.*?)\'.*: (([^_]+)(_OFF|_ON)?)#',$zbData->message,$results,PREG_SET_ORDER)) {
		echo 'Send :' . PHP_EOL;
		$data = getData($zbData->message);
		$data['id'] = $results[0][3];
		$etat = ($results[0][4] == '_OFF' ) ? '0' : '1';
		$data['etat'] = $etat;	
		print_r($data);
		jeebase::setStateToJeedom($data);				
	}
}
 
function getData($message) {
	if (preg_match_all( '#\([^\]]*\)#', $message, $m )) {
		$data = array();
		if(preg_match_all( '#<lev>(.*?)</lev>#', $m[0][0] ,$lev )) {
			$data['lev'] = $lev[1][0];
		};
		if(preg_match_all( '#<flag1>(.*?)</flag1>#', $m[0][0] ,$flag1 )) {
			$data['flag1'] = $flag1[1][0];
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
		if(preg_match_all( '#<sta>(.*?)</sta>#', $m[0][0] ,$sta)) {
			$data['sta'] = $sta[1][0];
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
		return($data);
	}
}
?>
