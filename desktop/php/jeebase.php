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
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-alert eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="deleteDataZibase"><i class="fa fa-plus-circle"></i> {{Effacer tous les équipements}}</a>                
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
            
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
            <center>
            <i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
            </center>
    		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
			</div> 
                 <div class="cursor eqLogicAction" data-action="addEquipement" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                    <i class="fa fa-plus-circle" style="font-size : 6em;color:#00A9EC;"></i>
                    <br>
                     <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00A9EC">{{Ajouter}}</span>
                </div>                       
            
		</div>  
        
        <?php
					echo "<legend>{{Modules}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'module') {
					
					

							$opacity = '';
								if ($eqLogic->getIsEnable() != 1) {
								$opacity = '
								-webkit-filter: grayscale(100%);
								-moz-filter: grayscale(100);
								-o-filter: grayscale(100%);
								-ms-filter: grayscale(100%);
								filter: grayscale(100%); opacity: 0.35;';
								}								
		
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px; ' . $opacity . ' " >';
							echo "<center>";
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "</center>";
							echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;" ><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
							echo '</div>';	
														
								
							}
					}
					echo '</div>';
      
               
       

					echo "<legend>{{Sondes}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'sonde') {
					
					

							$opacity = '';
								if ($eqLogic->getIsEnable() != 1) {
								$opacity = '
								-webkit-filter: grayscale(100%);
								-moz-filter: grayscale(100);
								-o-filter: grayscale(100%);
								-ms-filter: grayscale(100%);
								filter: grayscale(100%); opacity: 0.35;';
								}								
		
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px; ' . $opacity . ' " >';
							echo "<center>";
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "</center>";
							echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;" ><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
							echo '</div>';	
														
								
							}
					}
					echo '</div>';
					
					echo "<legend>{{Sensors}}</legend>";
					echo '<div class="eqLogicThumbnailContainer">';
					foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('type') == 'sensor') {
					
					

							$opacity = '';
								if ($eqLogic->getIsEnable() != 1) {
								$opacity = '
								-webkit-filter: grayscale(100%);
								-moz-filter: grayscale(100);
								-o-filter: grayscale(100%);
								-ms-filter: grayscale(100%);
								filter: grayscale(100%); opacity: 0.35;';
								}								
		
							echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px; ' . $opacity . ' " >';
							echo "<center>";
							echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
							echo "</center>";
							echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;" ><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
							echo '</div>';	
														
								
							}
					}
					echo '</div>';					
					
			?>
            
            

            
        </div> 
        
         <div class="col-md-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
         
          <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
          <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
        
         <ul class="nav nav-tabs" role="tablist">
          <li role="presentation"><a href="" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
          <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
          <li role="presentation"><a href="#infotab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Informations}}</a></li>
          <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
        </ul>    
        
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
		<div role="tabpanel" class="tab-pane active" id="eqlogictab"> 
        <br/>          
         
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
                
                 <div class="form-group ident">
                   <label class="col-md-2 control-label">{{Identifiant}}</label>
                    <div class="col-md-1">
                        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="id" placeholder="id" disabled/>
                    </div>        
                    
                </div> 
                <div id="custom">
                     <div class="form-group ">
                       <label class="col-md-2 control-label">{{Identifiant Actif}}</label>
                        <div class="col-md-2">
                            <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="on" placeholder="id" />
                        </div>        
                        
                    </div> 
                     <div class="form-group ">
                       <label class="col-md-2 control-label">{{Identifiant inactif}}</label>
                        <div class="col-md-2">
                            <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="off" placeholder="id" />
                        </div>        
                    </div> 
                     <div class="form-group ">
                       <label class="col-md-2 control-label">{{Temps RAZ (minutes)}}</label>
                        <div class="col-md-1">
                            <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="raz" placeholder="time" />
                        </div>        
                    </div>  
                     <div class="form-group ">
                       <label class="col-md-2 control-label">{{Refresh}}</label>
                        <div class="col-md-2">
                            <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="refresh" placeholder="cron" disabled/>
                        </div>        
                    </div>                                     
                </div>                               
            </fieldset> 
        </form>
        
        </div>
        
         <div role="tabpanel" class="tab-pane" id="infotab">  
         <br/> 
         
                
         <div id="table_sonde" class="form-group">
           <label class="col-md-2 control-label">{{Identifiant Sonde}}</label>
            <div class="col-md-3">
                <input type="text" id="sonde_os" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="sonde_os" placeholder="sonde_os"/>
            </div>
           <label class="col-md-2 control-label">{{Type de sonde}}</label>
            <div class="col-md-3">
                <input type="text" id="type" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="type_sonde" placeholder="type de sonde"/>
            </div>            
            
        </div>  
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
                    <th>{{id}}</th><th>{{Nom}}</th><th>{{Historiser}}</th><th>{{Afficher}}</th>
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