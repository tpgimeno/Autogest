/* Jquery */
/* global numeral */

$(document).ready(function(){ 
    
    /*
     * =============================================================================
     * GLOBAL FUNCTIONS ON PAGE READY
     * =============================================================================
     */
    
    /*
     * Numeral JS Function
     * =============================================================================
     */
    
    numeral.register('locale', 'es', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'k',
            million: 'mm',
            billion: 'b',
            trillion: 't'
        },

        currency: {
            symbol: '€'
        }
    });
    numeral.locale('es');
    
    /*
     *   Init DataTables
     *   ==========================================================================
     */    
    
    $('.dataTable').DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    
    
    
    /*
     *  Function to reset all the inputs 
     *  ============================================================================
     * /
    
    $('#reset').on('click', function(){
        $('input[type=text]').each(function(){
           $(this).val(""); 
        });
        
    });   
    
    /*
     *  Initializing Select2
     * =============================================================================
     */
    $('.select2').select2({
        tags : true       
    });
    
    
    
    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });
    if(!$('#offerDate').val()){
        $('#offerDate').val(new Date().toDateInputValue());
    }
    
    /*
     * =============================================================================
     * VEHICLE FUNCTIONS ON PAGE READY
     * =============================================================================
     */
    
    // Function to validate checked on checboxes
    if($('form.form-horizontal').attr('id') === "formVehiculo"){
        var checks_form = ['secondKey', 'rebu'];
        for(let i = 0; i < checks_form.length; i++){        
            $('#'+checks_form[i]).change(function(){
                if($(this).prop('checked')){
                   $(this).val(1);
                }else{
                   $(this).val(0);
                }
            });
        }
    
    // Function to set and unset Vehicle Accesories
    
        var checks_accesories = $('.accesory_check');
        checks_accesories.each(function(){
            $(this).change(function(){
                if($(this).prop('checked')){
                    add_accesories($(this).attr('id'));
                }else{
                    del_accesories($(this).attr('id'));
                }
            });
            set_accesories();
        }); 
        
        if(($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab')){            
            var delButton = $('#delete_button');            
            delButton.attr('style', 'display:none;');
        }
        
        set_components_prices();
        set_supplies_prices();
        set_works_prices();
    }
    /*
     * =============================================================================
     * SELLOFFERS FUNCTIONS ON PAGE READY
     * =============================================================================
     */
      
    /*
    * EventListener to set SellOffer Vehicle Prices
    * =============================================================================
    */
   
    var titleForm = $('.form-horizontal').attr('id');
    if(titleForm === 'formOfertadeVenta'){   
        set_selloffer_vehicle_prices();
        $('#formOfertadeVenta #plate').change(function(){            
            set_selloffer_vehicle_prices();        
        });
        $('#formOfertadeVenta #vehicleDiscount').change(function(){
            set_selloffer_vehicle_prices();
        });
        $('#formOfertadeVenta #brand').change(function(){ 
           set_models_by_brand($('#formOfertadeVenta #brand option:selected').val());               
           $('#formOfertadeVenta #plate').val('0');
           $('#formOfertadeVenta #plate').trigger('change');
           let brand = $('#formOfertadeVenta #brand option:selected').val();
           let model = $('#formOfertadeVenta #model option:selected').val();
           set_vehicles_by_model(brand,model); 
           $('#formOfertadeVenta #plate').trigger('change');
           
        });
        $('#formOfertadeVenta #model').change(function(){
            set_vehicles_by_model($('#formOfertadeVenta #brand option:selected').val(), $('#formOfertadeVenta #model option:selected').val());
        });
        
        $('#formOfertadeVenta #discount').change(function(){
            set_selloffer_price();
            $('#formOfertadeVenta #discount').val(numeral($('#formOfertadeVenta #discount').val()).format('(0,0.00$)'));
        });
        
        if(!$('#offerNumber').val()){
            get_new_offerNumber();
        }        
        if(($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab')){
            
            var delButton = $('#delete_button');            
            delButton.attr('style', 'display:none;');
        }
        set_components_prices();
        set_supplies_prices();
        set_works_prices();
    }
    
    /*
     * =============================================================================
     * TABS PAGES FUNCTIONS ON PAGE READY
     * =============================================================================
     */    
    
    /*
     * Calculate Imports in Modals and Currency Format them
     * =============================================================================
     */    
    
    
    $('.modal-form').each(function(){
        $(this).each(function(){
            var modal = $(this).attr('id');
            $('#'+modal+' #cantity').change(function(){
              var cant = $(this).val();
              var price = $('#'+modal+' #pvp').val();
              var total = numeral(parseFloat(cant) * parseFloat(price));
              $('#'+modal+' #total').val(total.format('(0,0.00$)'));
           });
        });
    });
    
    /*
     *   Function to keep opened the menu-collapse selected and activate current screen.
     * ==================================================================================
     */
    
    $('.nav-link').each(function(){
        $(this).on('shown.bs.tab', function(){
            $('.select2').select2();
            if($(this).attr('id') === 'accesories-tab'){
                set_accesories();
            }            
            if($(this).attr('id') === 'components-tab' || $(this).attr('id') === 'supplies-tab' || $(this).attr('id') === 'works-tab'){
                
                var delButton = $('#delete_button');
                delButton.attr('style', 'display:none;');
            }
        });
    });
        
    /*
     *   Add Double Click event to DataTables in Vehicles and Offers
     *   =======================================================================
     */  
    var assets = ['Components', 'Supplies', 'Works'];
    var assetsFunctions = ['setComponent', 'setSupply', 'setWork'];
    for(let i = 0;i < assets.length; i++){
        let table = new DataTable('#dataTable'+assets[i]);
        table.on('dblclick', 'tbody tr', function(){
            let data = table.row(this).data();
            assetsFunctions[i](data);        
        });
    }
    
});

/*
 * =============================================================================
 * Vehicle and SellOffer Common Functions
 * =============================================================================
 */

function setAsset(form, asset, data){ 
    
    var array = [];
    if(!data[asset]){
        array.push(null);
    }
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    }     
    if($('#' + form + ' .modal-body #selloffer_id').attr('id')){
        $('#' + form + ' .modal-body #selloffer_id').val($('.form-horizontal #id').val());
    }else if($('#' + form + ' .modal-body #vehicle_id').attr('id')){
        $('#' + form + ' .modal-body #vehicle_id').val($('.form-horizontal #id').val());
    }
    console.log(array);
    if(array[0] === null){       
        $('#' + form + ' .modal-body .row .input-group #id').val(array[0]);
        $('#' + form + ' .modal-body #' + asset).val(array[1]);  
    }else{
        $('#' + form + ' .modal-body .row .input-group #id').val(array[1]);
        $('#' + form + ' .modal-body #' + asset).val(array[0]);  
    }      
    $('#' + form + ' .modal-body .row .input-group #ref').val(array[2]);
    $('#' + form + ' .modal-body .row .input-group #name').val(array[3]);    
    $('#' + form + ' .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[4])).format('(0,0.00$)'));
    $('#' + form + ' .modal-body .row .input-group #cantity').val(array[5]);    
    $('#' + form + ' .modal-body .row .input-group #total').val(numeral(numeral(parseFloat(array[4])).value() * numeral(parseFloat(array[5])).value()).format('(0,0.00$)'));    
    $('#' + form).modal('show');    
}

function set_assets(tab){     
    var urlData = location.href; 
    var pos = urlData.indexOf('&selected_tab=');
    if(pos !== -1){
        urlData = urlData.substr(0, pos);
    }
    urlData = urlData + "&selected_tab=" + tab;
    location.href = urlData;
}

function saveAssets(url, data, modal, modal_form, tab){ 
   console.log(data);
    if(!$('.form-horizontal #id').val()){
        $('#' + modal_form).modal('hide');
        $('#' + modal).modal('hide');
        $('#alert').html('Debe guardar primero!');
    }else{
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function(result){                
                $(modal_form).modal('hide');
                $(modal).modal('hide');
                $('.alert').html(result);
                let timeout = setTimeout(set_assets(tab), 3000);
                clearTimeout(timeout);
            }
        });
    }
}

function delAsset(url, data, tab){ 
    $.ajax({
        method: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function(result){
            $('.alert').html(result);
            let timeout = setTimeout(set_assets(tab), 3000);
            clearTimeout(timeout);
        }
    });
}

function setComponent(data){ 
    setAsset("component_form_modal", "component_id", data);       
}

function setSupply(data){ 
    setAsset("supply_form_modal", "supply_id", data);
}

function setWork(data){   
    setAsset("work_form_modal", "work_id", data);
}

function set_components_prices(){
    var base_total = 0;
    $('#dataTableVehicleComponents td').each(function(){
        if($(this).attr('item') === 'pvp'){
            base_total = numeral(base_total).value() + numeral($(this).attr('item_value')).value();
        }
    });
    $('#baseComponents').val(numeral(base_total).format('(0,0.00$)'));
    $('#tvaComponents').val(numeral(base_total * 0.21).format('(0,0.00$)'));
    $('#totalComponents').val(numeral(numeral(base_total).value() + numeral($('#tvaComponents').val()).value()).format('(0,0.00$)'));
    if($('form#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('form#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }
}

function set_supplies_prices(){
    var base_total = 0;
    $('#dataTableVehicleSupplies td').each(function(){        
        if($(this).attr('item') === 'pvp'){
            base_total = numeral(base_total).value() + numeral($(this).attr('item_value')).value();
        }
    });
    $('#baseSupplies').val(numeral(base_total).format('(0,0.00$)'));
    $('#tvaSupplies').val(numeral(base_total * 0.21).format('(0,0.00$)'));
    $('#totalSupplies').val(numeral(numeral(base_total).value() + numeral($('#tvaSupplies').val()).value()).format('(0,0.00$)'));
    if($('form#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('form#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }
}

function set_works_prices(){
    var base_total = 0;    
    $('#dataTableVehicleWorks td').each(function(){
        if($(this).attr('item') === 'pvp'){
            base_total = numeral(base_total).value() + numeral($(this).attr('item_value')).value();
        }
    });
    $('#baseWorks').val(numeral(base_total).format('(0,0.00$)'));
    $('#tvaWorks').val(numeral(base_total * 0.21).format('(0,0.00$)'));
    $('#totalWorks').val(numeral(numeral(base_total).value() + numeral($('#tvaWorks').val()).value()).format('(0,0.00$)'));
    if($('form#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('form#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }
}

/*
 * =============================================================================
 * SellOffer Functions
 * =============================================================================
 */

function get_new_offerNumber(){
     $.ajax({
        method: "POST",
        url: "Intranet/sales/offers/number/get",
        data: {},
        dataType: "json",
        success: function(data){
            $('#offerNumber').val(data);
        }
    });
}

function set_selloffer_price(){    
    var discount = numeral($('#discount').val());    
    var vehiclePvp = numeral($('#vehiclePvp').val());
    var vehicleDiscount = numeral($('#vehicleDiscount').val());
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral(((vehiclePvp.value() - vehicleDiscount.value()) + baseComponents.value() + baseSupplies.value() + baseWorks.value()) - discount.value());
    $('#formOfertadeVenta #pvp').val(sum_bases.format('(0,0.00$)'));
    $('#formOfertadeVenta #tva').val(numeral(sum_bases.value() * 0.21).format('(0,0.00$)'));
    var tva = numeral($('#tva').val());
    $('#formOfertadeVenta #total').val(numeral(sum_bases.value() + tva.value()).format('(0,0.00$)'));    
}

function set_selloffer_vehicle_prices(){
    let pvp = numeral(parseFloat($('#formOfertadeVenta #plate option:selected').attr('price')));
    $('#formOfertadeVenta #vehiclePvp').val(pvp.format('(0,0.00$)'));
    let tva = numeral(pvp.value() * 0.21);
    $('#formOfertadeVenta #vehicleTva').val(tva.format('(0,0.00$)'));
    let discount = numeral(parseFloat($('#formOfertadeVenta #vehicleDiscount').val()));
    let total = numeral(pvp.value() - discount.value() + tva.value());
    $('#formOfertadeVenta #vehicleDiscount').val(discount.format('(0,0.00$)'));
    $('#formOfertadeVenta #vehicleTotal').val(total.format('(0,0.00$)')); 
    $('#formOfertadeVenta #vin').val($('#formOfertadeVenta #plate option:selected').attr('vin'));
    $('#formOfertadeVenta #km').val($('#formOfertadeVenta #plate option:selected').attr('km'));    
}

function set_models_by_brand(brand){    
    $.ajax({
        method: "POST",
        url: "Intranet/sales/offers/brands/get",
        data: {'brand' : brand},
        async: false,
        dataType: "json",
        success: function(data){
            var newArray = [];
            $('#formOfertadeVenta #model').empty();
            for(let i = 0;i < data.length; i++){
                let tempArray = {'id' : data[i].id ,'text' : data[i].name };
                newArray.push(tempArray);
            }
            newArray.push({'id' : '0', 'text' : 'Sin datos'});
            $('#formOfertadeVenta #model').select2({
                data : newArray
            });
        }
    });
}

function set_vehicles_by_model(brand, model){
    $.ajax({
        method: "POST",
        url: "Intranet/sales/offers/vehicles/get",
        data: {'brand' : brand, 'model' : model},
        async: false,
        dataType: "json",
        success: function(data){
            $('#formOfertadeVenta #plate').empty();
            for(let i = 0;i < data.length; i++){
                let tempOption = '<option km="' + data[i].km + '" vin="' + data[i].vin + '" price="' + data[i].pvp + '" vehicle_brand="' + data[i].brand_id + '" vehicle_model="' + data[i].model_id + '" value="' + data[i].id + '" >'+ data[i].plate + '</option>'; 
                $('#formOfertadeVenta #plate').append(tempOption);
            }
            $('#formOfertadeVenta #plate').append('<option value="0">Sin datos</option>');             
            $('#formOfertadeVenta #plate').select2();
            
        }
    });
}

function saveSellOfferComponent(){
    var data = {'selloffer_id' : $('#selloffer_component_form #selloffer_id').val(), 
            'component_id' : $('#selloffer_component_form #component_id').val(),
            'pvp' : $('#selloffer_component_form #pvp').val(),
            'cantity' : $('#selloffer_component_form #cantity').val()};
    saveAssets("Intranet/sales/offers/components/add",  data, '#components_modal', '#component_form_modal', 'components');     
    
}

function delSellOfferComponent(data){ 
    var url = "Intranet/sales/offers/components/del";    
    delAsset(url, data, 'components'); 
    
}

function saveSellOfferSupply(){   
    var data = {'selloffer_id' : $('#selloffer_supply_form #selloffer_id').val(), 
        'supply_id' : $('#selloffer_supply_form #supply_id').val(),
        'pvp' : $('#selloffer_supply_form #pvp').val(),
        'cantity' : $('#selloffer_supply_form #cantity').val()};    
    saveAssets("Intranet/sales/offers/supplies/add",  data, '#supplies_modal', '#supply_form_modal', 'supplies');    
}

function delSellOfferSupply(data){ 
    var url = "Intranet/sales/offers/supplies/del";
    delAsset(url, data, 'supplies');    
}

function saveSellOfferWork(){
    var data = {'selloffer_id' : $('#selloffer_work_form #selloffer_id').val(), 
        'work_id' : $('#selloffer_work_form #work_id').val(),
        'pvp' : $('#selloffer_work_form #pvp').val(),
        'cantity' : $('#selloffer_work_form #cantity').val()};   
    saveAssets("Intranet/sales/offers/works/add",  data, '#works_modal', '#work_form_modal', 'works');    
}

function delSellOfferWork(data){ 
    var url = "Intranet/sales/offers/works/del";
    delAsset(url, data, 'works');
    
}

/*
 * =============================================================================
 * Vehicle Functions
 * =============================================================================
 */

function set_vehicle_price(){    
    var discount = numeral($('#discount').val());    
    var vehiclePvp = numeral($('#vehiclePvp').val());
    var vehicleDiscount = numeral($('#vehicleDiscount').val());
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());    
    var sum_bases = numeral(((vehiclePvp.value() - vehicleDiscount.value()) + baseComponents.value() + baseSupplies.value() + baseWorks.value()) - discount.value());
    $('#formVehiculo #vehiclePvp').val(sum_bases.format('(0,0.00$)'));
    $('#formVehiculo #tva').val(numeral(sum_bases.value() * 0.21).format('(0,0.00$)'));
    var tva = numeral($('#tva').val());
    $('#formVehiculo #total').val(numeral(sum_bases.value() + tva.value()).format('(0,0.00$)'));    
}

function set_accesories(){
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/accesories/get",
        data: {'id' : $('#id').val()},
        dataType: "json",
        success: function(data){            
            for(let i = 0; i < data.length; i++){
                $('#'+data[i]["id"]).prop('checked', true);
            }
        }
    });
}

function add_accesories(accesory){
    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/accesories/add",
        data: {'vehicle_id' : $('#id').val(), 'accesory_id' : accesory},
        dataType: "json",
        success: function(data){            
            var alert = $('.alert');
            alert.html(data);
        }
    });
}

function del_accesories(accesory){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/accesories/del",
        data: {'vehicle_id' : $('#id').val(), 'accesory_id' : accesory},
        dataType: "json",
        success: function(data){
            var alert = $('.alert');
            alert.html(data);
        }
    });
}

function saveVehicleComponent(){
    var data = {'vehicle_id' :  $('#vehicle_component_form #vehicle_id').val(), 
            'component_id' : $('#vehicle_component_form #component_id').val(),
            'pvp' : $('#vehicle_component_form #pvp').val(),
            'cantity' : $('#vehicle_component_form #cantity').val()};    
    saveAssets("Intranet/vehicles/vehicleComponents/save", data, '#components_modal', '#component_form_modal','components');
   
}

function delVehicleComponent(data){     
    var url = "Intranet/vehicles/vehicleComponents/del";
    var setData = {'id' : data.id};
    delAsset(url, setData, 'components');
    
}


function saveVehicleSupply(){  
    var url = "Intranet/vehicles/vehicleSupplies/add";
    var data = {'vehicle_id' : $('#vehicle_supply_form #vehicle_id').val(), 
            'supply_id' : $('#vehicle_supply_form #supply_id').val(),
            'pvp' : $('#vehicle_supply_form #pvp').val(),
            'cantity' : $('#vehicle_supply_form #cantity').val()};
    saveAssets(url, data, '#supplies_modal', '#vehicle_supply_form', 'supplies');
   
}

function delVehicleSupply(data){ 
    var url = "Intranet/vehicles/vehicleSupplies/del";
    var data = {'id' : data.id};
    delAsset(url, data, 'supplies');
    
}

function saveVehicleWork(){ 
    var url = "Intranet/vehicles/vehicleWorks/add";
    var data = {'vehicle_id' : $('#vehicle_work_form #vehicle_id').val(), 
            'work_id' : $('#vehicle_work_form #work_id').val(),
            'pvp' : $('#vehicle_work_form #pvp').val(),
            'cantity' : $('#vehicle_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works');
    
}

function delVehicleWork(data){  
    var url = "Intranet/vehicles/vehicleWorks/del";
    var data = {'id' : data.id};
    delAsset(url, data, 'works');
    
}
