/* Jquery */
$(document).ready(function(){ 
    
    /*
     *   Init DataTables
     */    
    
    $('.dataTable').DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    
    /*
     *   Add Double Click event to DataTables in Vehicles and Offers
     */  
    var assets = ['Components', 'Supplies', 'Works'];
    var assetsFunctions = ['setVehicleComponent', 'setVehicleSupply', 'setWork'];
    for(let i = 0;i < assets.length; i++){
        let table = new DataTable('#dataTable'+assets[i]);
        table.on('dblclick', 'tbody tr', function(){
            let data = table.row(this).data();
            assetsFunctions[i](data);        
        });
    }
    
    /*
     *  Function to reset all the inputs 
     *  */
    
    $('#reset').on('click', function(){
        $('input[type=text]').each(function(){
           $(this).val(""); 
        });
        
    });
    
    /*
     * ==================================================================================
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
     * =============================================================================
     * Initializing Select2
     * =============================================================================
     */
    $('.select2').select2({
        tags : true,        
    });
    
    
    
    // Function to validate checked on checboxes
    
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
    
     /*
     * =============================================================================
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
     * =============================================================================
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
     * =============================================================================
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
        })
       
    }
    
    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });
    if(!$('#offerDate').val()){
        $('#offerDate').val(new Date().toDateInputValue());
    }
    
    if(!$('#offerNumber').val()){
        get_new_offerNumber();
    }
    set_components_prices();
    set_supplies_prices();
    set_works_prices();
    
    if($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab' || $('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab' || $('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab'){
        var delButton = $('#delete_button');
        delButton.attr('style', 'display:none;');
    }
    
    
});

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

function set_sellOffer_components(){          
    var url = location.href; 
    var pos = url.indexOf('&selected_tab=');
    if(pos !== -1){
        url = url.substr(0, pos);
    }
    url = url + "&selected_tab=components";
    location.href = url;
    
    set_selloffer_price();
}

function saveSellOfferComponent(){ 
    if(!$('#id').val()){
        $('#component_form_modal').modal('hide');
        $('#components_modal').modal('hide');
        $('#alert').html('Debe guardar la oferta primero!');
    }else{
        $.ajax({
            method: "POST",
            url: "Intranet/sales/offers/components/add",
            data: {'selloffer_id' : $('#id').val(), 
                'component_id' : $('#selloffer_component_form #component_id').val(),
                'pvp' : $('#selloffer_component_form #pvp').val(),
                'cantity' : $('#selloffer_component_form #cantity').val()},
            dataType: "json",
            success: function(result){   
                
                $('#component_form_modal').modal('hide');
                $('#components_modal').modal('hide');
                $('.alert').html(result); 
                set_sellOffer_components();
            }
        });
    }
}

function delSellOfferComponent(data){    
    url = "Intranet/sales/offers/components/del";        
    $.ajax({
        method: "POST",
        url: url,
        data: {'id' : data.selloffercomponent_id},
        dataType: "json",
        success: function(result){
            $('.alert').html(result);
            set_vehicle_components();         
        }
    });
   
}


function set_sellOffer_supplies(){
    var url = location.href;            
    var pos = url.indexOf('&selected_tab=');            
    if(pos !== -1){
        url = url.substr(0, pos);
    }
    url = url + '&selected_tab=supplies';
    location.href = url;
    set_supplies_prices();    
}

function saveSellOfferSupply(){    
    if(!$('#id').val()){        
        $('#supply_form_modal').modal('hide');
        $('#supply_modal').modal('hide');
        $('#alert').html('Debe guardar la oferta primero!');
    }else{
        $.ajax({
            method: "POST",
            url: "Intranet/sales/offers/supplies/add",
            data: {'selloffer_id' : $('#id').val(), 
                'supply_id' : $('#selloffer_supply_form #supply_id').val(),
                'pvp' : $('#selloffer_supply_form #pvp').val(),
                'cantity' : $('#selloffer_supply_form #cantity').val()},
            dataType: "json",
            success: function(result){ 
                $('#supply_form_modal').modal('hide');
                $('#supplies_modal').modal('hide');
                $('.alert').html(result);
                set_sellOffer_supplies();
            }
        });
    }
}

function delSellOfferSupply(data){ 
    $.ajax({
        method: "POST",
        url: "Intranet/sales/offers/supplies/del",
        data: {'id' : data.selloffersupply_id},
        dataType: "json",
        success: function(result){            
            $('.alert').html(result);
            set_sellOffer_supplies();
        }
    });
}

function set_sellOffer_works(){
    var url = location.href;             
    var pos = url.indexOf('&selected_tab=');            
    if(pos !== -1){
        url = url.substr(0, pos);
    }
    url = url + '&selected_tab=works';                
    location.href = url;
    set_works_prices();
}

function saveSellOfferWork(){ 
    if(!$('#id').val()){        
        $('#work_form_modal').modal('hide');
        $('#works_modal').modal('hide');
        $('#alert').html('Debe guardar la oferta primero!');
    }else{
        $.ajax({
            method: "POST",
            url: "Intranet/sales/offers/works/add",
            data: {'selloffer_id' : $('#id').val(), 
                'work_id' : $('#selloffer_work_form #work_id').val(),
                'pvp' : $('#selloffer_work_form #pvp').val(),
                'cantity' : $('#selloffer_work_form #cantity').val()},
            dataType: "json",
            success: function(result){                
                $('#work_form_modal').modal('hide');
                $('#works_modal').modal('hide');
                $('.alert').html(result);
                set_sellOffer_works()
            }
        });
    }
}

function delSellOfferWork(data){    
    $.ajax({
        method: "POST",
        url: "Intranet/sales/offers/works/del",
        data: {'id' : data.sellofferwork_id},
        dataType: "json",
        success: function(result){            
            $('.alert').html(result);
            set_sellOffer_works();
        }
    });
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

function set_vehicle_components(){
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleComponents/set",
        data: {'vehicle_id' : $('#id').val()},
        dataType: "json",
        success: function(){
            var url = location.href; 
            var pos = url.indexOf('&selected_tab=');
            if(pos !== -1){
                url = url.substr(0, pos);
            }
            url = url + "&selected_tab=components";
            location.href = url;           
        }
    });
}

function saveVehicleComponent(){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleComponents/save",
        data: {'vehicle_id' : $('#id').val(), 
            'component_id' : $('#vehicle_component_form #component_id').val(),
            'pvp' : $('#vehicle_component_form #pvp').val(),
            'cantity' : $('#vehicle_component_form #cantity').val()},
        dataType: "json",
        success: function(result){            
            $('#component_form_modal').modal('hide');
            $('#components_modal').modal('hide');
            $('.alert').html(result);
            set_vehicle_components();          
        }
    });
}

function delVehicleComponent(data){    
    url = "Intranet/vehicles/vehicleComponents/del";
    $.ajax({
        method: "POST",
        url: url,
        data: {'id' : data.id},
        dataType: "json",
        success: function(result){
            $('.alert').html(result);
            set_vehicle_components();         
        }
    });
}

function set_vehicle_supplies(){    
    var url = location.href;             
    var pos = url.indexOf('&selected_tab=');            
    if(pos !== -1){
        url = url.substr(0, pos);
    }
    url = url + '&selected_tab=supplies';
    location.href = url;
    set_supplies_prices();    
}

function saveVehicleSupply(){  
    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleSupplies/add",
        data: {'vehicle_id' : $('#id').val(), 
            'supply_id' : $('#vehicle_supply_form #supply_id').val(),
            'pvp' : $('#vehicle_supply_form #pvp').val(),
            'cantity' : $('#vehicle_supply_form #cantity').val()},
        dataType: "json",
        success: function(result){ 
            $('#supply_form_modal').modal('hide');
            $('#supplies_modal').modal('hide');
            $('.alert').html(result);
            set_vehicle_supplies();
        }
    });
}

function delVehicleSupply(data){ 
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleSupplies/del",
        data: {'id' : data.id},
        dataType: "json",
        success: function(result){            
            $('.alert').html(result);
            set_vehicle_supplies();
        }
    });
}

function set_vehicle_works(){
    var url = location.href;             
    var pos = url.indexOf('&selected_tab=');            
    if(pos !== -1){
        url = url.substr(0, pos);
    }
    url = url + '&selected_tab=works';                
    location.href = url;
    set_works_prices();    
}

function saveVehicleWork(){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleWorks/add",
        data: {'vehicle_id' : $('#id').val(), 
            'work_id' : $('#vehicle_work_form #id').val(),
            'pvp' : $('#vehicle_work_form #pvp').val(),
            'cantity' : $('#vehicle_work_form #cantity').val()},
        dataType: "json",
        success: function(result){
            $('#works_form_modal').modal('hide');
            $('#works_modal').modal('hide');
            $('.alert').html(result);
            set_vehicle_works();
        }
    });
}

function delVehicleWork(data){  
    console.log(data);
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleWorks/del",
        data: {'id' : data.id},
        dataType: "json",
        success: function(result){            
            $('.alert').html(result);
            set_vehicle_works();
        }
    });
}

/*
 * =============================================================================
 * Vehicle and SellOffer Common Functions
 * =============================================================================
 */

function setVehicleComponent(data){
    var array = [];
    if(!data.component_id){
        array.push(null);
    }
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    }  
    if($('#component_form_modal .modal-body #selloffer_id').attr('id')){
        $('#component_form_modal .modal-body #selloffer_id').val($('#id').val());
    }else if($('#component_form_modal .modal-body #vehicle_id').attr('id')){
        $('#component_form_modal .modal-body #vehicle_id').val($('#id').val());
    }
    if(!array[0]){
        $('#component_form_modal .modal-body .row .input-group #id').val(array[0]);
    }else{
        $('#component_form_modal .modal-body .row .input-group #id').val(array[1]);
    }    
    $('#component_form_modal .modal-body #component_id').val(array[1]);    
    $('#component_form_modal .modal-body .row .input-group #ref').val(array[2]);
    $('#component_form_modal .modal-body .row .input-group #name').val(array[3]);    
    $('#component_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[4])).format('(0,0.00$)'));
    $('#component_form_modal .modal-body .row .input-group #cantity').val(array[5]);   
    $('#component_form_modal').modal('show');
    updateComponentFormPrice();
}

function updateComponentFormPrice(){
    var cant = numeral($('#component_form_modal .modal-body .row .input-group #cantity').val());
    var price = numeral($('#component_form_modal .modal-body .row .input-group #pvp').val());
    var total = numeral(cant.value() * price.value());
    $('#component_form_modal .modal-body .row .input-group #total').val(total.format('(0,0.00$)'));
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

function setVehicleSupply(data){ 
    
    var array = [];
    if(!data.supply_id){
        array.push(null);
    }
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    } 
    if($('#supply_form_modal .modal-body #selloffer_id').attr('id')){
        $('#supply_form_modal .modal-body #selloffer_id').val($('#id').val());
        
    }else if($('#supply_form_modal .modal-body #vehicle_id').attr('id')){
        $('#supply_form_modal .modal-body #vehicle_id').val($('#id').val());
        
    }    
    if(!array[0]){        
        $('#supply_form_modal .modal-body .row .input-group #id').val(array[0]);
    }else{
        $('#supply_form_modal .modal-body .row .input-group #id').val(array[1]);
    }      
    $('#supply_form_modal .modal-body #supply_id').val(array[1]);   
    $('#supply_form_modal .modal-body .row .input-group #ref').val(array[2]);
    $('#supply_form_modal .modal-body .row .input-group #name').val(array[3]);    
    $('#supply_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[4])).format('(0,0.00$)'));      
    $('#supply_form_modal .modal-body .row .input-group #cantity').val(array[5]);
    $('#supply_form_modal .modal-body .row .input-group #total').val(numeral(parseFloat(array[5]) * parseFloat(array[4])).format('(0,0.00$)'));
    $('#supply_form_modal').modal('show');   
}

function setVehicleWork(data){   
    
    var array = [];
    if(!data.work_id){
        array.push(null);
    }
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    } 
    
    if($('#work_form_modal .modal-body #selloffer_id').attr('id')){
        $('#work_form_modal .modal-body #selloffer_id').val($('#id').val());        
    }else if($('#work_form_modal .modal-body #vehicle_id').attr('id')){
        $('#work_form_modal .modal-body #vehicle_id').val($('#id').val());        
    }    
    if(!array[0]){
        $('#work_form_modal .modal-body .row .input-group #id').val(array[0]);
    }else{
        $('#work_form_modal .modal-body .row .input-group #id').val(array[1]);
    }    
    $('#work_form_modal .modal-body #work_id').val(array[1]);
    $('#work_form_modal .modal-body .row .input-group #reference').val(array[2]);
    $('#work_form_modal .modal-body .row .input-group #description').val(array[3]);    
    $('#work_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[4])).format('(0,0.00$)'));      
    $('#work_form_modal .modal-body .row .input-group #cantity').val(array[5]);
    $('#work_form_modal .modal-body .row .input-group #total').val(numeral(parseFloat(array[4]) * parseFloat(array[5])).format('(0,0.00$)'));
    $('#work_form_modal').modal('show');
}













