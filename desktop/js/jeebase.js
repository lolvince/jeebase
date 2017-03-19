
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






$('.eqLogicAction[data-action=updateDataZibase]').on('click', function () {	
     bootbox.confirm('Etes-vous sûr de vouloir mettre à jour toutes les donnèes?', function (result) {
		 if (result) {
			$.ajax({// fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
				data: {
					action: "updateDataZibase",
				},
				dataType: 'json',
				error: function (request, status, error) {
					handleAjaxError(request, status, error);
				},
				success: function (data) { // si l'appel a bien fonctionné
					if (data.state != 'ok') {
						$('#div_alert').showAlert({message: data.result, level: 'danger'});
						return;
					}
					$('#div_alert').showAlert({message: '{{Update terminé}}', level: 'success'});
				}
			});
		 }
     });			
});



$('.eqLogicAction[data-action=deleteDataZibase]').on('click', function () {		
     bootbox.confirm('Etes-vous sûr de vouloir supprimer toutes les donnèes?', function (result) {
		 if (result) {	
			$.ajax({// fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
				data: {
					action: "deleteDataZibase",
				},
				dataType: 'json',
				error: function (request, status, error) {
					handleAjaxError(request, status, error);
				},
				success: function (data) { // si l'appel a bien fonctionné
					if (data.state != 'ok') {
						$('#div_alert').showAlert({message: data.result, level: 'danger'});
						return;
					}
					$('#div_alert').showAlert({message: '{{Opération  terminée}}', level: 'success'});
				}
			});
		 }
     });		
});






function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {};
    }
     if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }           

            
	if (_cmd.logicalId == 'humidity' || _cmd.name == 'Température' || _cmd.name == 'Vent' || _cmd.name == 'Pluie Tot' || _cmd.name == 'Pluie' || _cmd.name == 'Consommation Instantanée' || _cmd.name == 'Consommation Totale' || _cmd.name == 'Luminosité' ) {
			$('#table_Z1base').show();
			$('#table_Z1bas3').show();
			$('#table_sonde').show();
			$('#div_Z1bas3').show();
			$('#table_cmd').hide();
			
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
			tr += '<td>';
			tr += '<span class="cmdAttr" data-l1key="id" ></span>';
			tr += '</td>';
			tr += '<td>' + _cmd.name + '</td>'; 
			tr += '<td>';
		
			tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isHistorized" /> {{Historiser}}<br/></span>';
				
			
			tr += '</td>';
			tr += '<td>';
		
			tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" /> {{Afficher}}<br/></span>';
				
			
			tr += '</td>';
			tr += '</tr>';
			$('#table_Z1bas3 tbody').append(tr);
			$('#table_Z1bas3 tbody tr:last').setValues(_cmd, '.cmdAttr');
    }
	if (_cmd.name == 'ON' || _cmd.name == 'OFF' || _cmd.name == 'Etat' || _cmd.name == 'Slider' || _cmd.name == 'Etat Sensor') {
			$('#table_Z1base').hide();
			$('#table_Z1bas3').hide();
			$('#div_Z1bas3').hide();
			$('#table_cmd').show();
			$('#table_sonde').hide();
			
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td class="name">';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
			tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;margin-right : 10px;" title="{{La valeur de la commande vaut par defaut la commande}}">';
			tr += '<option value="">Etat</option>';
			tr += '</select>';	
		tr += '</td>';
		tr += '<td class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType();
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span></td>';
		tr += '<td ><input style="margin-bottom : 5px;width : 70%; display : inline-block;" placeholder="Nom information" data-l2key="infoName" data-l1key="configuration" class="cmdAttr form-control input-sm">';
		tr += '<input style="margin-bottom : 5px;width : 50%; display : inline-block;" placeholder="Valeur" data-l2key="value" data-l1key="configuration" class="cmdAttr form-control input-sm">';
		tr += '</td>';
		tr += '<td>';
		tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
		tr += '<span class="expertModeVisible"><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="display" data-l2key="invertBinary" /> {{Inverser}}<br/></span>';	
		tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isHistorized" /> {{Historiser}}<br/></span>';	
		tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
		}
		tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
		tr += '</tr>';
		$('#table_cmd tbody').append(tr);
		$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
		jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
	}	
            
}

function printEqLogic(_eqLogic)  {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
        data: {
            action: "getSonde",
            id: _eqLogic.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné

            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#table_Z1base tbody').empty();
            $('#div_Z1base').empty();
            for (var i in data.result.cmd) {
                var tr = '<tr>';
                tr += '<td>' + data.result.cmd[i].name + '</td>';
                tr += '<td>' + data.result.cmd[i].value;
                if (data.result.cmd[i].unite != null) {
                    tr += ' ' + data.result.cmd[i].unite;
                }
                tr += '</td>';  			
				tr += '</tr>';
                $('#table_Z1base tbody').append(tr);
            }

        }
    });
}



