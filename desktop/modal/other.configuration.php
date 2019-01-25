<?php
if (!isConnect('admin')) {
        throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div class="form-group ">
    <label class="col-md-2 control-label">{{Identifiant Actif}}</label>
    <div class="col-md-2">
        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="on" placeholder="id on" />
    </div>        
</div> 
<div class="form-group ">
    <label class="col-md-2 control-label">{{Identifiant inactif}}</label>
    <div class="col-md-2">
        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="off" placeholder="id off" />
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