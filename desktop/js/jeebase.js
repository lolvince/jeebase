
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


$('.eqLogicAction[data-action=addEquipement]').on('click', function () {	
    bootbox.confirm("<form id='infos' class='form-horizontal'><fieldset>\
        <div class='form-group'>\
          <label >Nom de l'équipement</label>\
          <input type='text' class='form-control' id='name' placeholder='Nom' ></input>\
        </div>\
        <div class='form-group'>\
		<select id='sel_type' class='form-control'>\
		  <option  value='sensor'>{{Sensors}}</option>\
		</select>\
		</div>\
      </fieldset></form>", 
	
	
	 function (result) {
		 if (result == false) {
			 return;
		 }
		if( !$('#name').val() ) {
			return;
		}
			console.log($('#sel_type').value())
			console.log($('#name').val())
			jeedom.eqLogic.save({
				type: eqType,
				
				eqLogics: [{name: $('#name').val(),configuration: {'type':$('#sel_type').value(),'type_eq':'custom'}}],
				error: function (error) {
					$('#div_alert').showAlert({message: error.message, level: 'danger'});
				},
				success: function (_data) {
					  console.log(_data)
					var vars = getUrlVars();
					var url = 'index.php?';
					for (var i in vars) {
						if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
							url += i + '=' + vars[i].replace('#', '') + '&';
						}
					}
					modifyWithoutSave = false;
					window.location.href = url;;
				}
			});			  
    });
});



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
			
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
			tr += '<td>';
			tr += '<span class="cmdAttr" data-l1key="id" ></span>';
			tr += '</td>';
			tr += '<td>' + _cmd.name + '</td>'; 
			tr += '<td>';
			tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" /> {{Historiser}}<br/></span>';
				
			
			tr += '</td>';
			tr += '<td>';
			tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
			tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" /> {{Afficher}}<br/></span>';
				
			
			tr += '</td>';
			tr += '</tr>';
			$('#table_Z1bas3 tbody').append(tr);
			$('#table_Z1bas3 tbody tr:last').setValues(_cmd, '.cmdAttr');
    }
	if (_cmd.name == 'ON' || _cmd.name == 'OFF' || _cmd.name == 'Etat' || _cmd.name == 'Slider' || _cmd.name == 'Etat Sensor') {
			
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
		tr += '<td>';
		tr += '<span><input type="checkbox" class="cmdAttr " data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
		tr += '<span class="expertModeVisible"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary" /> {{Inverser}}<br/></span>';	
		tr += '<span><input type="checkbox" class="cmdAttr " data-l1key="isHistorized" /> {{Historiser}}<br/></span>';	
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
    $('.ident').hide();
	
	
	if (!isset(_eqLogic)) {
		var _eqLogic = {configuration: {}};
	}
	
	if (!isset(_eqLogic.configuration)) {
	   _eqLogic.configuration = {};
	}
	
	if (_eqLogic.configuration.type == 'module') {
			$('#table_Z1base').hide();
			$('#table_Z1bas3').hide();
			$('#div_Z1bas3').hide();
			$('#table_cmd').show();
			$('#table_sonde').hide();	
			$('#custom').hide();
	}
	
	if (_eqLogic.configuration.type == 'sensor') {
			$('#table_Z1base').hide();
			$('#table_Z1bas3').hide();
			$('#div_Z1bas3').hide();
			$('#table_cmd').show();
			$('#table_sonde').hide();	
			$('#custom').hide();
			if (_eqLogic.configuration.type_eq == "custom") {
				$('#custom').show();
				
			} else {
				$('#custom').hide();
			}
	}	
	
	
	
	
	if (_eqLogic.configuration.type == 'sonde') {
			$('#table_Z1base').show();
			$('#table_Z1bas3').show();
			$('#table_sonde').show();
			$('#div_Z1bas3').show();
			$('#table_cmd').hide();	
			$('#custom').hide();	
	}
			
	
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


