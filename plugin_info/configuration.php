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

require_once __DIR__ . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">

    <fieldset>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{ip locale (Jeedom)}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="locale_ip" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Ip de la zibase}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="zibase_ip" />
            </div>
        </div>
<!--        <div class="form-group">
            <label class="col-lg-4 control-label">{{Id de la zibase}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="zibase_id" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Token de la zibase}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="zibase_token" />
            </div>
        </div>  --> 
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Niveau Batterie}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="battery" placeholder="Lire doc" />
            </div>
        </div>         
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Rafraîchir}}</label>
            <div class="col-lg-2">
            <a class="btn btn-warning" id="bt_refresh"><i class='fas fa-sync'></i> {{MAJ}}</a>
            </div>
            <span>{{ Permet de mettre à jour toutes les informations des équipements (utile en cas de panne du démon) }}</span>
        </div>         
    </fieldset>
  </form>

</fieldset>
</form>

<script>
    $('#bt_refresh').on('click', function () {
		bootbox.confirm('{{ Êtes-vous sûr d\'effectuer cette opération }}', function (result) {
		 if (result) {		
			$.ajax({// fonction permettant de faire de l'ajax
				type: "POST", // methode de transmission des données au fichier php
				url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
				data: {
					action: "refreshDataZibase",
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
					$('#div_alert').showAlert({message: '{{Mise à jour réussie}}', level: 'success'});
				}
			});
		 }
		});
    });
	
	
    $('#bt_syncWithZibase').on('click', function () {

        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/jeebase/core/ajax/jeebase.ajax.php", // url du fichier php
            data: {
                action: "syncWithZibase",
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
                $('#div_alert').showAlert({message: '{{Synchronisation réussie}}', level: 'success'});
            }
        });
    });	
</script>
