
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

 $('#bt_healthJeebase').on('click', function () {
    $('#md_modal').dialog({title: "{{Santé Jeebase}}"});
    $('#md_modal').load('index.php?v=d&plugin=jeebase&modal=health').dialog('open');
});

 $('#bt_activity').on('click', function () {
	 
	$.ajax({// fonction permettant de faire de l'ajax
		type: "POST", // methode de transmission des données au fichier php
		url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
		data: {
			action: "getActivity"
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
			if(data.result == 1) {
				$('#md_modal2').dialog({title: "{{Log du plugin}}"});
				$("#md_modal2").load('index.php?v=d&modal=log.display&log=jeebase_php').dialog('open');				
			} else {
				$('#div_alert').showAlert({message: 'Pour avoir le relevé d\'activité , mettre les logs du plugin en mode debug', level: 'warning'});			
			}
			
		}
	});		
	
	
});

$('.eqLogicAction[data-action=addEquipement]').on('click', function () {	
    bootbox.confirm("<form id='infos' class='form-horizontal'><fieldset>\
        <div class='form-group'>\
          <label >Nom de l'équipement</label>\
          <input type='text' class='form-control' id='name' placeholder='Nom' ></input>\
        </div>\
        <div class='form-group'>\
		<select id='sel_type' class='form-control'>\
		  <option  value='module'>{{Modules}}</option>\
		   <option  value='sonde'>{{Sondes}}</option>\
		  <option  value='sensor'>{{Détecteurs}}</option>\
		  <option  value='other'>{{Autres}}</option>\
		</select>\
		</div>\
      </fieldset></form>", 
	
	
	 function (result) {
		 if (result == false) {
			 return;
		 }
		if( !$('#name').val() ) {
			$('#div_alert').showAlert({message: '{{Il faut donner un nom à l\'équipement.}}', level: 'danger'});
			return;
		}

		jeedom.eqLogic.save({
			type: eqType,
			eqLogics: [{name: $('#name').val(),configuration: {'type':$('#sel_type').value()}}],
			error: function (error) {
				$('#div_alert').showAlert({message: error.message, level: 'danger'});
			},
			success: function (data) {
				modifyWithoutSave = false;
				var vars = getUrlVars();
				var url = 'index.php?';
				for (var i in vars) {
					if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
						url += i + '=' + vars[i].replace('#', '') + '&';
					}
				}
				url += 'id=' + data.id + '&saveSuccessFull=1';
				if (document.location.toString().match('#')) {
					url += '#' + document.location.toString().split('#')[1];
				} 
				loadPage(url);
				modifyWithoutSave = false;
			}
		});			  
    });
});


$("body").delegate(".listCmdAction", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function(html) {
            el.closest('.' + type).find('.actionOptions').html(html);
        });
    });
});

$("body").delegate(".listAction", 'click', function () {
  var type = $(this).attr('data-type');
  var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=cmd]');
  jeedom.getSelectActionModal({}, function (result) {
    el.value(result.human);
    jeedom.cmd.displayActionOption(el.value(), '', function (html) {
      el.closest('.' + type).find('.actionOptions').html(html);
  });
});
});

$("body").delegate(".listEquipement", 'click', function() {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=eqLogic]');
    jeedom.eqLogic.getSelectModal({}, function(result) {
        //console.log(result);
        el.value(result.human);
    });
});

$("body").delegate('.bt_removeAction', 'click', function() {
    var type = $(this).attr('data-type');
    $(this).closest('.' + type).remove();
});


$("body").delegate(".listCmdInfo", 'click', function() {
	var type = $(this).attr('data-type');	
	var el = $(this).closest('.' + type).find('.triggerAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subtype: 'binary'}}, function(result) {
        el.value(result.human);
    });
});


$('.addEvent').on('click', function() {
	var type = $(this).attr('data-action');
    addEvent({}, '{{Action}}',type);
});

$('.modeEquipement').on('click', function() {
	$.ajax({// fonction permettant de faire de l'ajax
		type: "POST", // methode de transmission des données au fichier php
		url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
		data: {
			action: "includeEquipment",
			id: $('.eqLogicAttr[data-l1key=configuration][data-l2key=id]').val(),
			protocol: $('.eqLogicAttr[data-l1key=configuration][data-l2key=protocole]').val(),
			mode: $(this).attr('data-action')
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
			$('#md_modal2').dialog({title: "{{Log du plugin}}"});
			$("#md_modal2").load('index.php?v=d&modal=log.display&log=jeebase_php').dialog('open');	
			$(".modeEquipement[data-action=ASSOC]").hide();
			$(".modeEquipement[data-action=UNASSOC]").hide();
		}
	});			
});


function addEvent(_action, _name, _type, _el) {
	
    if (!isset(_action)) {
        _action = {};
    }
    if (!isset(_action.options)) {
        _action.options = {};
    }

    var div = '<div class="action_' + _type + '">';
    div += '<div class="form-group ">';
    div += '<label class="col-sm-1 control-label">' + _name + '</label>';
	
    div += '<div class="col-sm-4 has-success">';
	
    div += '<div class="input-group">';
    div += '<span class="input-group-btn">';
    div += '<a class="btn btn-default bt_removeAction btn-sm" data-type="action_' + _type + '"><i class="fa fa-minus-circle"></i></a>';
    div += '</span>';
    div += '<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd" data-type="action_' + _type + '" />';
    div += '<span class="input-group-btn">';
    div += '<a class="btn btn-success btn-sm listAction" data-type="action_' + _type + '" title="{{Sélectionner un mot-clé}}"><i class="fa fa-tasks"></i></a>';
    div += '<a class="btn btn-success btn-sm listCmdAction" data-type="action_' + _type + '"><i class="fa fa-list-alt"></i></a>';
    div += '</span>';
    div += '</div>';
    div += '</div>';
    div += '<div class="col-lg-6 actionOptions">';
    div += jeedom.cmd.displayActionOption(init(_action.cmd, ''), _action.options);
    div += '</div>';
    div += '</div>';
    if (isset(_el)) {
		console.log('ttut')
        _el.find('.div_action_' + _type ).append(div);
        _el.find('.action_' + _type + ':last').setValues(_action, '.expressionAttr');
    } else {
        $('#div_action_' + _type).append(div);
        $('#div_action_' + _type + ' .action_' + _type + ':last').setValues(_action, '.expressionAttr');
    }

}





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
    var _cmd = {configuration: {}};
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  tr += '<td>';
  tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> {{Icône}}</a>';
  tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left:10px;"></span>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="margin-left:10px; margin-bottom:2px; width:185px; float:right">';
  tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display : none;" title="{{La valeur de la commande vaut par défaut la commande}}">';
  tr += '<option value="">Aucune</option>';
  tr += '</select>';
  tr += '</td>';
  tr += '<td>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
  tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
  tr += '</td>';
  tr += '<td>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="returnStateValue" placeholder="{{Valeur retour d\'état}}" style="width:48%;display:inline-block;">';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="returnStateTime" placeholder="{{Durée avant retour d\'état (min)}}" style="width:48%;display:inline-block;margin-left:2px;">';
  tr += '</td>';
  tr += '<td>';
  tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
  tr += ' <input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;display:inline-block;margin-left:2px;">';
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
  tr += '</td>';
  tr += '<td style="width:125px">';
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
  }
  tr += ' <i class="fas fa-minus-circle cmdAction cursor" data-action="remove"></i></td>';
  tr += '</tr>';
  $('#table_cmd tbody').append(tr);
  var tr = $('#table_cmd tbody tr').last();
  jeedom.eqLogic.builSelectCmd({
    id: $('.eqLogicAttr[data-l1key=id]').value(),
    filter: {type: 'info'},
    error: function (error) {
      $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function (result) {
		console.log('result')
		console.log(result)
      tr.find('.cmdAttr[data-l1key=value]').append(result);
      tr.setValues(_cmd, '.cmdAttr');
      jeedom.cmd.changeType(tr, init(_cmd.subType));
    }
  });
}

function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {};
    }
	
	_eqLogic.configuration.action_on = $('#div_action_on .action_on').getValues('.expressionAttr');
	_eqLogic.configuration.action_off = $('#div_action_off .action_off').getValues('.expressionAttr');
	_eqLogic.configuration.action_state = $('#div_action_state .action_state').getValues('.expressionAttr');

	return _eqLogic;
}




function printEqLogic(_eqLogic)  {
	$('#div_action_on').empty();
	$('#div_action_off').empty();	
	$('.includeEquipement').hide();	
	if (!isset(_eqLogic)) {
		var _eqLogic = {configuration: {}};
	}
	$(".checkInclude").click(function() {
		// this will contain a reference to the checkbox   
		if ($(this).is(':checked')) {
			$('.includeEquipement').show();
		} else {
			$('.includeEquipement').hide();
		}
	});		
	
	if (!isset(_eqLogic.configuration)) {
	   _eqLogic.configuration = {};
	}
	
    if (isset(_eqLogic.configuration) && isset(_eqLogic.configuration.type) && _eqLogic.configuration.type != '') {
        $('.item-conf').load('index.php?v=d&plugin=jeebase&modal=' + _eqLogic.configuration.type + '.configuration', function () {
            $('body').setValues(_eqLogic, '.eqLogicAttr');
			if (isset(_eqLogic.configuration.action_on)) {
				for (var i in _eqLogic.configuration.action_on) {
					//console.log(_eqLogic.configuration.action_alarm[i]);
					addEvent(_eqLogic.configuration.action_on[i], '{{Action}}','on');
				}
			}
			if (isset(_eqLogic.configuration.action_off)) {
				for (var i in _eqLogic.configuration.action_off) {
					addEvent(_eqLogic.configuration.action_off[i], '{{Action}}','off');
				}
			}
			if (isset(_eqLogic.configuration.action_state)) {
				for (var i in _eqLogic.configuration.action_state) {
					//console.log(_eqLogic.configuration.action_alarm[i]);
					addEvent(_eqLogic.configuration.action_state[i], '{{Action}}','state');
				}
			}					   
		   
            initCheckBox();
            modifyWithoutSave = false;
        });		
		
    } else {
        $('.item-conf').empty();
        $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change', function () {
            $('.item-conf').load('index.php?v=d&plugin=jeebase&modal=' + $(this).val() + '.configuration', function() {
                initCheckBox();
            });
        });
    }
  
  	switch (_eqLogic.configuration.type) {
	   case "module":
	   case "other": 
		   $('#action').show();
		   $('.collapse').collapse();	   
		   break;
		case "sensor":
		   $('#action').hide();	
			break;
	    case "sonde": 
		   $('#action').hide();
		   break;		  
	}
	
			
}


