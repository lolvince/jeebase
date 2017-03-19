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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">

    <fieldset>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{ip locale}}</label>
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
        <div class="form-group">
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
        </div>   
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Url}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="zibase_url" placeholder="https://zibase.net" />
            </div>
        </div> 
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Synchroniser}}</label>
            <div class="col-lg-2">
            <a class="btn btn-warning" id="bt_syncWithZibase"><i class='fa fa-refresh'></i> {{Synchroniser mes équipements}}</a>
            </div>
        </div>        
        
                     
    </fieldset>
  </form>

</fieldset>
</form>

<script>
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
