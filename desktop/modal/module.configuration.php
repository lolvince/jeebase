<?php
if (!isConnect('admin')) {
        throw new Exception('{{401 - Accès non autorisé}}');
}
?>

     <div class="form-group ">
       <label class="col-md-2 control-label">{{Identifiant Module}}</label>
        <div class="col-md-2">
            <input type="text"  class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="id" />
        </div>
    </div>
     <div class="form-group ">
       <label class="col-md-2 control-label">{{Type de protocole}}</label>
        <div class="col-md-2">
            <select id="type" class="eqLogicAttr form-control"  data-l1key="configuration" data-l2key="protocole">
                  <option value="0">{{DEFAULT BROADCAST (X10, CHACON)}}</option>
                  <option value="1">{{VISONIC433}}</option>
                  <option value="2">{{VISONIC868}}</option>
                  <option value="3">{{CHACON (32bits frame) (ChaconV2/DIO series)}}</option> 
                  <option value="4">{{DOMIA (24 bits frame) ( Chacon V1 + low-cost devices)}}</option> 
                  <option value="5">{{RF X10 }}</option>
                  <option value="6">{{ZWAVE}}</option>
                  <option value="7">{{RFS10/TS10}}</option>
                  <option value="8">{{XDD433 alrm}}</option> 
                  <option value="9">{{XDD868 alrm }}</option>  
                  <option value="10">{{XDD868 inter/shutter*}}</option>
                  <option value="11">{{XDD868 Pilot Wire}}</option> 
                  <option value="12">{{XDD868 Boiler/AC }}</option>                                        
            </select>                                
        </div> 
    </div>      
    <div class="form-group ">
        <label class="col-md-2 control-label" >{{Variateur}}</label>
        <div class="col-md-1" >
            </span><input type="checkbox" class="eqLogicAttr checkbox-inline" data-l1key="configuration"  data-l2key="dim"/>
        </div>
    </div> 
    <div class="form-group ">
       <label class="col-md-2 control-label">{{Somfy My}}</label>
        <div class="col-md-1" >
            </span><input type="checkbox" class="eqLogicAttr checkbox-inline" data-l1key="configuration"  data-l2key="somfy"/>
        </div>
    </div>     