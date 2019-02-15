<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'jeebase');
$eqLogics = eqLogic::byType('jeebase');
$plugin = plugin::byId('jeebase');


?>

<div class="row row-overflow">
    <div class="col-md-2">
    	<a class="btn btn-danger eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="deleteDataZibase"><i class="fa fa-minus-circle"></i> {{Effacer tous les équipements}}</a>                
        <div class="bs-sidebar">
        	
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('jeebase') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    
   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
	<div class="eqLogicThumbnailContainer">  
     <!--	       <div class="cursor  IncludeState" style="background-color : #8000FF; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
            <center>
            <i class="fa fa-sign-in fa-rotate-90" style="font-size : 6em;color:#94ca02;"></i>
            </center>
            <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>{{Inclusion}}</center></span>
            </div>     -->   
        
        
            <div class="cursor eqLogicAction" data-action="addEquipement" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
              <i class="fa fa-plus-circle" style="font-size : 5em;color:#00A9EC;"></i>
              <br>
              <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00A9EC">{{Ajouter}}</span>
            </div>        
              <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
              <i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
            <br>
            <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
          </div> 
          <div class="cursor" id="bt_healthJeebase" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
            <center>
              <i class="fa fa-medkit" style="font-size : 5em;color:#767676;"></i>
            </center>
            <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
          </div> 
          <div class="cursor" id="bt_activity" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
            <center>
              <i class="fa fa-info-circle" style="font-size : 5em;color:#767676;"></i>
            </center>
            <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Activité}}</center></span>
          </div>           
                                                  
            
		</div>  
        <legend>{{Equipements}}</legend>
        <input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
        
        <?php
					echo "<legend>{{Actionneurs}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'module') {
                            $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                            echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="display:inline-block;text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                            echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
                            echo "<br>";
                            echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
                            echo '</div>';
						}
					}
					echo '</div>';
      
               
       

					echo "<legend>{{Sondes}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
						if($eqLogic->getConfiguration('type') == 'sonde') {
							$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="display:inline-block;text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "<br>";
							echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
							echo '</div>';
						}
					}
					echo '</div>';
					
					echo "<legend>{{Sensors}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'sensor') {
							$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="display:inline-block;text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "<br>";
							echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
							echo '</div>';
							}
					}
					echo '</div>';
					
					echo "<legend>{{Autres}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'other') {
							$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="display:inline-block;text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "<br>";
							echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
							echo '</div>';
							}
					}
					echo '</div>';	
			?>
            
            

            
        </div> 
        
        <div class="col-md-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
        
         <ul class="nav nav-tabs" role="tablist">
          <li role="presentation"><a href="" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
          <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
          <li role="presentation" ><a href="#infotab" aria-controls="tab" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Informations}}</a></li>
          <li role="presentation"><a href="#cmdtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>    
        
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
		<div role="tabpanel" class="tab-pane active" id="eqlogictab"> 
        <br/> 
        <div id="modal_task" ></div>        
         
        <form class="form-horizontal">
            <fieldset>
                <div class="form-group">
                    <label class="col-md-2 control-label">{{Nom}}</label>
                    <div class="col-md-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >{{Objet parent}}</label>
                    <div class="col-md-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">{{Catégorie}}</label>
                    <div class="col-md-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>                 
                <div class="form-group">
                  <label class="col-md-2 control-label" >{{Activer}}</label>
                  <div class="col-md-1">
                    <input type="checkbox" class="eqLogicAttr checkbox-inline checkbox_active" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                  </div>
                  <label class="col-md-2 control-label prog_visible" >{{Visible}}</label>
                  <div class="col-md-1 prog_visible">
                    <input type="checkbox" class="eqLogicAttr checkbox-inline" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
                  </div>
                </div>
                                             
            </fieldset> 
        </form>
        
        </div>
        <div role="tabpanel" class="tab-pane" id="infotab"> 
         <br/> 
        <br />        
             <div id="table_sonde" class="form-group">
            	<form class="form-horizontal">
                	<fieldset>
                    	<div class="item-conf"></div>             
                	</fieldset>
                </form>        
            </div> 
            
             <div class="form-group">
            	<form class="form-horizontal">
                	<fieldset>
                         <label class="col-md-2 control-label" >{{Inclure un nouveau module/exclure}}</label>
                        <div class="col-md-1">
                          <input type="checkbox" class="checkInclude checkbox-inline" data-label-text="{{Activer}}"  unchecked/>
                		</div>                    
                    
                    	           
                	</fieldset>
                </form>        
            </div>             
            

            
            <div class="includeEquipement">
                <div class="alert alert-danger"> {{
                  - L'inclusion fonctionne comme avec la zibase
                  - Il ne faut pas inclure un module déjà inclus ou un module ne nécessitant pas d'inclusion (sondes oregon, prise intertechno ...) <br/>
                  - Respecter les consignes concernant votre module <br/>
                  - Se référer à la documentation avant toute inclusion/exclusion<br/>
                  - Attendre que la zibase ne soit plus en mode inclusion/exclusion avant d'en relancer une nouvelle
                  }} 
                </div> 
                <br /> 
                 <label class="col-md-2 control-label" ></label>           
               <a class="btn btn-warning modeEquipement" title="{{ Inclure périphérique }}" data-action="ASSOC" style="margin-bottom : 5px;"><i class="fa fa-sign-in fa-rotate-90"></i> {{Mode inclusion}}</a>
                <a class="btn btn-danger modeEquipement" title="{{ Exclure périphérique }}" data-action="UNASSOC" style="margin-bottom : 5px;"><i class="fa fa-sign-in fa-rotate-90"></i> {{Mode Exclusion}}</a>
			</div>

            <div id="action">
                <div class="mode panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#action_clock" href="#action_add_actionState">
                        <span class="name">{{Url à lancer au changement d'état:}}</span>
                        </a>
                        </h4>
                    </div>
                    <div id="action_add_actionState" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="well">
                            
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label">{{URL}}</label>
                                        <div class="col-lg-8">
                                        	<input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="pushState" placeholder="{{Mettez ici l'URL à appeler lors d'une mise à jour de la valeur de la commande.}}"/>
                                            <!--<input type="text"  class="eqLogicAttr form-control" data-l1key="configuration"  data-l1key="pushOn" title="{{Mettez ici l'URL à appeler lors d'une mise à jour de la valeur de la commande.}}"/>-->
                                        </div>
                                    </div>                                
                                </form>
                            </div>
                        </div>
                    </div>
                </div>             
                <div class="mode panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#action_clock" href="#action_add_action">
                        <span class="name">{{Action(s) à executer quand commande on détectée:}}</span>
                        </a>
                        </h4>
                    </div>
                    <div id="action_add_action" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="well">
                            
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label">{{URL}}</label>
                                        <div class="col-lg-8">
                                        	<input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="pushOn" placeholder="{{Mettez ici l'URL à appeler lors d'une mise à jour de la valeur de la commande.}}"/>
                                        </div>
                                    </div>                                
                                    <div class="form-group">
                                            <div class="btn-group pull-left" role="group">
                                                <a class="btn btn-success btn-xs addEvent"  data-action="on" style="margin-left: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter Action}}</a>
                                            </div>
                                    </div>
                                    <hr/>
                                    <div id="div_action_on"></div>
                                    <hr/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 
                <div  class="mode panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#action_clock" href="#action_alarm_addaction">
                        <span class="name">{{Action(s) à executer quand commande off détectée  :}}</span>
                        </a>
                        </h4>
                    </div>
                    <div id="action_alarm_addaction" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="well">
                                <form class="form-horizontal" role="form">
                                   <div class="form-group">
                                        <label class="col-lg-1 control-label">{{URL}}</label>
                                        <div class="col-lg-8">
                                        	<input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="pushOff" placeholder="{{Mettez ici l'URL à appeler lors d'une mise à jour de la valeur de la commande.}}"/>
                                        </div>
                                    </div>                                     
                                
                                    <div class="form-group">
                                            <div class="btn-group pull-left" role="group">
                                                <a class="btn btn-success btn-xs addEvent" data-action="off" style="margin-left: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter Action}}</a>
                                            </div>
                                    </div>
                                    <hr/>
                                    <div id="div_action_off"></div>
                                    <hr/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>             
            
            
            

        </div>
        <br/>
         <div role="tabpanel" class="tab-pane" id="cmdtab">  
         <br/> 
        <br />
        <br />
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th >{{Nom}}</th>
                    <th >{{Type}}</th>
                    <th>Options</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>        
        
        <div id="div_Z1base"></div>
        <table id="table_Z1base" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{nom}}</th><th>{{Valeur}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
        <div id="div_Z1bas3"></div>
        <table id="table_Z1bas3" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{id}}</th><th>{{Nom}}</th><th>{{Options}}</th><th>{{Actions}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>


   		</div>
        </div>
</div>

<?php include_file('desktop', 'jeebase', 'js', 'jeebase'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>