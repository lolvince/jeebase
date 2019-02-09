<?php
if (!isConnect('admin')) {
        throw new Exception('{{401 - Accès non autorisé}}');
}
?>

    <div class="form-group ">
       <label class="col-md-2 control-label">{{Identifiant Sonde}}</label>
        <div class="col-md-2">
            <input type="text" id="sonde_os" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="sonde_os" placeholder="sonde_os"/>
        </div>
    </div>
     <div class="form-group ">
       <label class="col-md-2 control-label">{{Type de sonde}}</label>
        <div class="col-md-2">
           <!-- <input type="text" id="type" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="type_sonde" placeholder="type de sonde" disabled/>-->
            <select id="type" class="eqLogicAttr form-control"  data-l1key="configuration" data-l2key="type_sonde">
                  <option value="temperature">{{Température}}</option>
                  <option value="light">{{Luminosité}}</option>
                  <option value="power">{{Puissance}}</option>
                  <option value="rain">{{Pluviomètre}}</option> 
                  <option value="wind">{{Anémomètre}}</option>               
            </select>
        </div>  
    </div> 