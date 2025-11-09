/* Jquery */
/* global numeral */

var original_vehicle_price;
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
     *   Manage Nav-Tabs Main Menu
     *   ==========================================================================
     */  
    
    $('.nav-pills .nav-item').each(function(){
        if($(this).attr('id') === $('#menu').val()){
            $(this).removeClass('menu-close');
            $(this).addClass('menu-open');
        }else{
            $(this).removeClass('menu-open');
            $(this).addClass('menu-close');
        }        
    });
    
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
     */
    
    $('#reset').on('click', function(){
        $('input[type=text]').each(function(){
           $(this).val(""); 
        });
        
    });   
    
   
    $('.date').each(function(){
        $(this).datetimepicker({
            icons : { time: 'far fa-clock'},
            format: 'DD/MM/YYYY hh:mm:ss'
        });
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
        original_vehicle_price = $('#vehiclePvp').val();
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
        $('#formVehiculo #vehicleDiscount').change(function(){
            set_vehicle_price();
            $('#formVehiculo #vehicleDiscount').val(numeral($('#formVehiculo #vehicleDiscount').val()).format('(0.0,$)'));
        });
        set_vehicle_price();
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
//            set_vehicles_by_plate("Intranet/sales/offers/plate/get", "#formOfertadeVenta", $('#formOfertadeVenta #plate').val());
            set_selloffer_vehicle_prices();        
        });
        $('#formOfertadeVenta #vehicleDiscount').change(function(){
            set_selloffer_vehicle_prices();
        });
        $('#formOfertadeVenta #brand').change(function(){ 
           set_models_by_brand("Intranet/sales/offers/brands/get", '#formOfertadeVenta', $('#formOfertadeVenta #brand option:selected').val(), $('#formOfertadeVenta #model option:selected').val());               
           $('#formOfertadeVenta #plate').val('0');
           $('#formOfertadeVenta #plate').trigger('change');
           let brand = $('#formOfertadeVenta #brand option:selected').val();
           let model = $('#formOfertadeVenta #model option:selected').val();
           set_vehicles_by_model("Intranet/sales/offers/brands/get", "#formOfertadeVenta", brand, model); 
           $('#formOfertadeVenta #plate').trigger('change');
           
        });
        $('#formOfertadeVenta #model').change(function(){
            set_vehicles_by_model("Intranet/sales/offers/vehicles/get", "#formOfertadeVenta", $('#formOfertadeVenta #brand option:selected').val(), $('#formOfertadeVenta #model option:selected').val());
        });
        
        $('#formOfertadeVenta #discount').change(function(){
            set_selloffer_price();
            $('#formOfertadeVenta #discount').val(numeral($('#formOfertadeVenta #discount').val()).format('(0.0,$)'));
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
     * GARAGE ORDERS FUNCTIONS ON PAGE READY
     * =============================================================================
     */
    
    var titleForm = $('.form-horizontal').attr('id');    
    if(titleForm === 'formOrdendeTrabajo'){        
        if($('#orderNumber').val() === null || $('#orderNumber').val() === ""){           
            get_new_orderNumber();
        } 
        if(($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab')){            
            var delButton = $('#delete_button');            
            delButton.attr('style', 'display:none;');
        }
        $('#formOrdendeTrabajo #discount').change(function(){
            set_garageOrder_price();
            $('#formOrdendeTrabajo #discount').val(numeral($('#formOrdendeTrabajo #discount').val()).format('(0.0,$)'));
        });
        set_models_by_brand("/Intranet/garages/models/get",'#formOrdendeTrabajo', $('#formOrdendeTrabajo #brand option:selected').val());
        $('#formOrdendeTrabajo #brand').change(function(){
            set_models_by_brand("/Intranet/garages/models/get",'#formOrdendeTrabajo', $('#formOrdendeTrabajo #brand option:selected').val());
        });
        
        set_components_prices();
        set_supplies_prices();
        set_works_prices();
        set_garageOrder_price();
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
              $('#'+modal+' #total').val(total.format('(0.0,$)'));
           });
        });
    });
    
    /*
     *   Function to keep opened the menu-collapse selected and activate current screen.
     * ==================================================================================
     */
    
    $('.nav-link').each(function(){
        $(this).on('shown.bs.tab', function(){
            $('.select2').select2({
                tags : true
            });
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
    
    
    var titleForm = $('.form-horizontal').attr('id');
    if(titleForm === 'formHojadeTrabajo'){         
        if($('#workSheetNumber').val() === null || $('#workSheetNumber').val() === ""){            
            get_new_workSheetNumber();
        } 
        if(($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab')){            
            var delButton = $('#delete_button');            
            delButton.attr('style', 'display:none;');
        }
         $('#formHojadeTrabajo #discount').change(function(){
            set_worksheet_prices();
            $('#formHojadeTrabajo #discount').val(numeral($('#formHojadeTrabajo #discount').val()).format('(0.0,$)'));
        });       
        set_components_prices();
        set_supplies_prices();
        set_works_prices();
        set_worksheet_prices();
    }
    
        /*
     * =============================================================================
     * EXPRERT OPINIONS FUNCTIONS ON PAGE READY
     * =============================================================================
     */
    
    var titleForm = $('.form-horizontal').attr('id');
    if(titleForm === 'formPeritacion'){         
        if($('#opinionId').val() === null || $('#opinionId').val() === ""){            
            get_new_opinionId();
        }
        set_models_by_brand("/Intranet/garages/models/get",'#formPeritacion', $('#formPeritacion #brand option:selected').val());
        if(($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'components-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'supplies-tab') || ($('.nav-tabs .nav-item .nav-link.active').attr('id') === 'works-tab')){            
            var delButton = $('#delete_button');            
            delButton.attr('style', 'display:none;');
        }  
        
        $('#formPeritacion #discount').change(function(){
            set_worksheet_prices();
            $('#formPeritacion #discount').val(numeral($('#formPeritacion #discount').val()).format('(0.0,$)'));
        });  
        
        $('#formPeritacion #brand').change(function(){ 
           set_models_by_brand("/Intranet/garages/models/get",'#formPeritacion', $('#formPeritacion #brand option:selected').val());               
           $('#formPeritacion #plate').val('0');
           set_vehicles_by_model("/Intranet/garages/vehicles/get", '#formPeritacion',$('#formPeritacion #brand option:selected').val(), $('#formPeritacion #model option:selected').val());
           $('#formPeritacion #km').val(($('#formPeritacion #plate option:selected').attr('km')));
           $('#formPeritacion #vin').val(($('#formPeritacion #plate option:selected').attr('vin')));
           
          
        });
        
        $('#formPeritacion #plate').change(function(){
            set_vehicles_by_plate("/Intranet/garages/vehicles/plate",'#formPeritacion' ,$('#formPeritacion #plate option:selected').val());
        });
        
        $('#formPeritacion #model').change(function(){
            setTimeout(() => {set_vehicles_by_model("/Intranet/garages/vehicles/get", '#formPeritacion',$('#formPeritacion #brand option:selected').val(), $('#formPeritacion #model option:selected').val());}, 1000);
        });
        
        $('#formPeritacion #discount').change(function(){
            set_selloffer_price();
            $('#formPeritacion #discount').val(numeral($('#formPeritacion #discount').val()).format('(0.0,$)'));
        });
        set_components_prices();
        set_supplies_prices();
        set_works_prices();
        set_expertOpinion_prices();
    }
     
    
    /*
     * =============================================================================
     * ASSURANCES FUNCTIONS ON PAGE READY
     * =============================================================================
     */
    
    var titleForm = $('.form-horizontal').attr('id');
    if(titleForm === 'formPolizadeSeguro'){   
        $('#formPolizadeSeguro #price').change(function(){
            $('#formPolizadeSeguro #price').val(numeral($('#formPolizadeSeguro #price').val()).format('(0.0,$)'));
        });
        $('#formPolizadeSeguro #discount').change(function(){
            $('#formPolizadeSeguro #discount').val(numeral($('#formPolizadeSeguro #discount').val()).format('(0.0,$)'));
        });
    }      
    
});

/*
 * =============================================================================
 * Common Functions
 * =============================================================================
 */


function init_selects(){
    $('.select2').select2({
        tags : true       
    });
}


/*
 * =============================================================================
 * Common Functions
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
    if($('#' + form + ' .modal-body #sellOffer_id').attr('id')){
        $('#' + form + ' .modal-body #sellOffer_id').val($('.form-horizontal #id').val());
    }else if($('#' + form + ' .modal-body #vehicle_id').attr('id')){
        $('#' + form + ' .modal-body #vehicle_id').val($('.form-horizontal #id').val());
    }else if($('#' + form + '.modal-body #garageOrder_id').attr('id')){
        $('#' + form + '.modal-body #garageOrder_id').val($('.form-horizontal #id').val());    
    }else if($('#' + form + '.modal-body #workSheet_id').attr('id')){
        $('#' + form + '.modal-body #workSheet_id').val($('.form-horizontal #id').val());
    }else if($('#' + form + '.modal-body #expertOpinion_id').attr('id')){
        $('#' + form + '.modal-body #expertOpinion_id').val($('.form-horizontal #id').val());
    }
    
    if(array[0] === null){       
        $('#' + form + ' .modal-body .row .input-group #id').val(array[0]);
        $('#' + form + ' .modal-body #' + asset).val(array[1]);  
    }else{
        $('#' + form + ' .modal-body .row .input-group #id').val(array[1]);
        $('#' + form + ' .modal-body #' + asset).val(array[0]);  
    }      
    $('#' + form + ' .modal-body .row .input-group #ref').val(array[2]);
    $('#' + form + ' .modal-body .row .input-group #name').val(array[3]);    
    $('#' + form + ' .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[4])).format('(0.0,$)'));
    $('#' + form + ' .modal-body .row .input-group #cantity').val(array[5]);    
    $('#' + form + ' .modal-body .row .input-group #total').val(numeral(numeral(parseFloat(array[4])).value() * numeral(parseFloat(array[5])).value()).format('(0.0,$)'));    
    $('#' + form).modal('show');    
}

function set_assets(tab, urlData){    
    urlData = urlData + "&selected_tab=" + tab;
    location.href = urlData;
}

function saveAssets(url, data, modal, modal_form, tab, setUrl){    
    if($('.form-horizontal #id').val() === ""){        
        $(modal_form).modal('hide');
        $(  modal).modal('hide');
        $('.alert').html('Debe guardar primero!');
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
                let timeout = setTimeout(set_assets(tab, setUrl), 3000);
                clearTimeout(timeout);
            }
        });
    }
}

function delAsset(url, data, tab, setUrl){ 
    $.ajax({
        method: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function(result){
            
            $('.alert').html(result);
            let timeout = setTimeout(set_assets(tab, setUrl), 3000);
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
        if($(this).attr('item') === 'total'){            
            base_total = numeral(base_total).value() + numeral($(this).text()).value();
        }        
    });
    
    $('#baseComponents').val(numeral(base_total).format('(0.0,$)'));
    $('#tvaComponents').val(numeral(base_total * 0.21).format('(0.0,$)'));
    $('#totalComponents').val(numeral(numeral(base_total).value() + numeral($('#tvaComponents').val()).value()).format('(0.0,$)'));
    if($('#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }else if($('#formOrdendeTrabajo').attr('id')){
        set_garageOrder_price();
    }else if($('#formHojadeTrabajo').attr('id')){
        set_garageOrder_price();
    }
}

function set_supplies_prices(){
    var base_total = 0;
    $('#dataTableVehicleSupplies td').each(function(){
        if($(this).attr('item') === 'total'){
            base_total = numeral(base_total).value() + numeral($(this).text()).value();            
        }
    });    
    $('#baseSupplies').val(numeral(base_total).format('(0.0,$)'));
    $('#tvaSupplies').val(numeral(base_total * 0.21).format('(0.0,$)'));
    $('#totalSupplies').val(numeral(numeral(base_total).value() + numeral($('#tvaSupplies').val()).value()).format('(0.0,$)'));
    if($('form#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('form#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }else if($('form#formOrdendeTrabajo').attr('id')){
        set_garageOrder_price();
    }else if($('form#formHojadeTrabajo').attr('id')){
        set_garageOrder_price();
    }
}

function set_works_prices(){
    var base_total = 0;    
    $('#dataTableVehicleWorks td').each(function(){
        if($(this).attr('item') === 'total'){
            base_total = numeral(base_total).value() + numeral($(this).text()).value();
        }
    });
    $('#baseWorks').val(numeral(base_total).format('(0.0,$)'));
    $('#tvaWorks').val(numeral(base_total * 0.21).format('(0.0,$)'));
    $('#totalWorks').val(numeral(numeral(base_total).value() + numeral($('#tvaWorks').val()).value()).format('(0.0,$)'));
    if($('form#formVehiculo').attr('id')){
        set_vehicle_price();
    }else if($('form#formOfertadeVenta').attr('id')){
        set_selloffer_price();
    }else if($('form#formOrdendeTrabajo').attr('id')){
        set_garageOrder_price();
    }else if($('#formHojadeTrabajo').attr('id')){
        set_garageOrder_price();
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
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral((vehiclePvp.value() + baseComponents.value() + baseSupplies.value() + baseWorks.value()) - discount.value());
    $('#formOfertadeVenta #pvp').val(sum_bases.format('(0.0,$)'));
    $('#formOfertadeVenta #tva').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#tva').val());
    $('#formOfertadeVenta #total').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));    
}

function set_selloffer_vehicle_prices(){
    original_vehicle_price = $('#formOfertadeVenta #plate option:selected').attr('price');
    let pvp = numeral(parseFloat(original_vehicle_price));
    let discount = numeral(parseFloat($('#formOfertadeVenta #vehicleDiscount').val()));
    $('#formOfertadeVenta #vehiclePvp').val(numeral(pvp.value() - discount.value()).format('(0.0,$)'));
    let tva = numeral(numeral($('#formOfertadeVenta #vehiclePvp').val()).value() * 0.21);
    $('#formOfertadeVenta #vehicleTva').val(tva.format('(0.0,$)'));    
    let total = numeral(pvp.value() - discount.value() + tva.value());
    $('#formOfertadeVenta #vehicleDiscount').val(discount.format('(0.0,$)'));
    $('#formOfertadeVenta #vehicleTotal').val(total.format('(0.0,$)')); 
    $('#formOfertadeVenta #vin').val($('#formOfertadeVenta #plate option:selected').attr('vin'));
    $('#formOfertadeVenta #km').val($('#formOfertadeVenta #plate option:selected').attr('km')); 
    set_selloffer_price();
}

function set_models_by_brand(url, form, brand){    
    $.ajax({
        method: "POST",
        url: url,
        data: {'brand' : brand},
        async: false,
        dataType: "json",
        success: function(data){            
            var newArray = [];
            $(form + ' #model').empty();
            for(let i = 0;i < data.length; i++){
                let tempArray = {'id' : data[i].id ,'text' : data[i].name };
                newArray.push(tempArray);
            }
            newArray.push({'id' : '0', 'text' : 'Sin datos'});
            $(form + ' #model').select2({
                data : newArray
            });
        }
    });
}

function set_vehicles_by_model(url, form, brand, model){   
    
    $.ajax({
        method: "POST",
        url: url,
        data: {'brand' : brand, 'model' : model},
        async: false,
        dataType: "json",
        success: function(data){ 
           
            $(form + ' #plate').empty();
            for(let i = 0;i < data.length; i++){
                let tempOption = '<option km="' + data[i].km + '" vin="' + data[i].vin + '" price="' + data[i].pvp + '" vehicle_brand="' + data[i].brand_id + '" vehicle_model="' + data[i].model_id + '" value="' + data[i].id + '" >'+ data[i].plate + '</option>'; 
                $(form + ' #plate').append(tempOption);
            }
            $(form + ' #plate').append('<option value="0">Sin datos</option>');             
            $(form + ' #plate').select2();
            
        }
    });
}

function set_vehicles_by_plate(url, form, plate){   
    $.ajax({
        method: "POST",
        url: url,
        data: {'plate' : plate},
        async: false,
        dataType: "json",
        success: function(data){            
            $(form + '#brand').val(data['brand']).trigger('change.select2');           
        }
    });
}

function saveSellOfferComponent(){    
    var data = {'selloffer_id' : $('.form-horizontal #id').val(), 
            'component_id' : $('#sellOffer_component_form #component_id').val(),
            'pvp' : $('#sellOffer_component_form #pvp').val(),
            'cantity' : $('#sellOffer_component_form #cantity').val()};
    saveAssets("Intranet/sales/offers/components/add",  data, '#components_modal', '#component_form_modal', 'components', "Intranet/sales/offers/form?id=" + $('#sellOffer_component_form #sellOffer_id').val() + "&menu=ventas&item=offers");     
    
}

function delSellOfferComponent(data){     
    var url = "Intranet/sales/offers/components/del";  
    var setData = {'id' : data.sellOffercomponent_id};
    delAsset(url, setData, 'components', "Intranet/sales/offers/form?id=" + $('.form-horizontal #id').val() + "&menu=ventas&item=offers"); 
    
}

function saveSellOfferSupply(){    
    var data = {'selloffer_id' : $('.form-horizontal #id').val(), 
        'supply_id' : $('#sellOffer_supply_form #supply_id').val(),
        'pvp' : $('#sellOffer_supply_form #pvp').val(),
        'cantity' : $('#sellOffer_supply_form #cantity').val()};    
    saveAssets("Intranet/sales/offers/supplies/add",  data, '#supplies_modal', '#supply_form_modal', 'supplies', "Intranet/sales/offers/form?id=" + $('.form-horizontal #id').val() + "&menu=ventas&item=offers");    
}

function delSellOfferSupply(data){     
    var url = "Intranet/sales/offers/supplies/del";
    var setData = {'id' : data.sellOffersupply_id};
    delAsset(url, setData, 'supplies', "Intranet/sales/offers/form?id=" + $('.form-horizontal #id').val() + "&menu=ventas&item=offers");    
}

function saveSellOfferWork(){
    var data = {'selloffer_id' : $('.form-horizontal #id').val(), 
        'work_id' : $('#sellOffer_work_form #work_id').val(),
        'pvp' : $('#sellOffer_work_form #pvp').val(),
        'cantity' : $('#sellOffer_work_form #cantity').val()};   
    saveAssets("Intranet/sales/offers/works/add",  data, '#works_modal', '#work_form_modal', 'works', "Intranet/sales/offers/form?id=" + $('.form-horizontal #id').val() + "&menu=ventas&item=offers");    
}

function delSellOfferWork(data){ 
    var url = "Intranet/sales/offers/works/del";
    var setData = {'id' : data.sellOfferwork_id};
    delAsset(url, setData, 'works', "Intranet/sales/offers/form?id=" + $('.form-horizontal #id').val() + "&menu=ventas&item=offers");
    
}

/*
 * =============================================================================
 * Vehicle Functions
 * =============================================================================
 */

function set_vehicle_price(){    
    var vehiclePvp = numeral(original_vehicle_price);
    var vehicleDiscount = numeral($('#vehicleDiscount').val());
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());  
    var pvp_vehicle = numeral(vehiclePvp.value() - vehicleDiscount.value());
    var sum_assets = numeral(baseComponents.value() + baseSupplies.value() + baseWorks.value());
    var sum_bases = numeral(pvp_vehicle.value() + sum_assets.value());
    $('#formVehiculo #costTva').val(numeral(numeral($('#formVehiculo #cost').val()).value() * 0.21).format('(0.0,$)'));
    $('#formVehiculo #costTotal').val(numeral((numeral($('#formVehiculo #cost').val()).value()) + (numeral($('#formVehiculo #costTva').val()).value())).format('(0.0,$)'));
    $('#formVehiculo #vehiclePvp').val(sum_bases.format('(0.0,$)'));
    $('#formVehiculo #vehicleTva').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#formVehiculo #vehicleTva').val());
    $('#formVehiculo #vehicleTva').val(tva.format('(0.0,$)'));
    $('#formVehiculo #vehicleTotal').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));
    
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
    var url = "Intranet/vehicles/vehicleComponents/save";
    var data = {'vehicle_id' :  $('.form-horizontal #id').val(), 
            'component_id' : $('#vehicle_component_form #component_id').val(),
            'pvp' : $('#vehicle_component_form #pvp').val(),
            'cantity' : $('#vehicle_component_form #cantity').val()};    
    saveAssets(url, data, '#components_modal', '#component_form_modal','components', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
   
}

function delVehicleComponent(data){     
    var url = "Intranet/vehicles/vehicleComponents/del";
    var setData = {'id' : data.vehiclecomponent_id};
    delAsset(url, setData, 'components', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
    
}


function saveVehicleSupply(){  
    var url = "Intranet/vehicles/vehicleSupplies/add";
    var data = {'vehicle_id' : $('.form-horizontal #id').val(), 
            'supply_id' : $('#vehicle_supply_form #supply_id').val(),
            'pvp' : $('#vehicle_supply_form #pvp').val(),
            'cantity' : $('#vehicle_supply_form #cantity').val()};
    saveAssets(url, data, '#supplies_modal', '#supply_form_modal', 'supplies', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
   
}

function delVehicleSupply(data){ 
    var url = "Intranet/vehicles/vehicleSupplies/del";
    var setData = {'id' : data.vehiclesupply_id};
    delAsset(url, setData, 'supplies', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
    
}

function saveVehicleWork(){ 
    var url = "Intranet/vehicles/vehicleWorks/add";
    var data = {'vehicle_id' : $('.form-horizontal #id').val(), 
            'work_id' : $('#vehicle_work_form #work_id').val(),
            'pvp' : $('#vehicle_work_form #pvp').val(),
            'cantity' : $('#vehicle_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
    
}

function delVehicleWork(data){  
    var url = "Intranet/vehicles/vehicleWorks/del";
    var setData = {'id' : data.vehiclework_id};
    delAsset(url, setData, 'works', 'Intranet/vehicles/form?id=' + $('.form-horizontal #id').val() + '&menu=stock&item=vehicles');
    
}


/*
 * =============================================================================
 * Garage Orders Functions
 * =============================================================================
 */

function saveGarageOrderComponent(){
    var url = "Intranet/garageOrders/components/add";   
    var data = {'garageOrder_id' :  $('.form-horizontal #id').val(), 
            'component_id' : $('#garageOrder_component_form #component_id').val(),
            'pvp' : $('#garageOrder_component_form #pvp').val(),
            'cantity' : $('#garageOrder_component_form #cantity').val()};
    
    saveAssets(url, data, '#components_modal', '#component_form_modal','components', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
   
}

function delGarageOrderComponent(data){     
    var url = "Intranet/garageOrders/components/del";
    var setData = {'id' : data.garageOrdercomponent_id};    
    delAsset(url, setData, 'components', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
    
}


function saveGarageOrderSupply(){  
    var url = "Intranet/garageOrders/supplies/add";
    var data = {'garageOrder_id' : $('.form-horizontal #id').val(), 
            'supply_id' : $('#garageOrder_supply_form #supply_id').val(),
            'pvp' : $('#garageOrder_supply_form #pvp').val(),
            'cantity' : $('#garageOrder_supply_form #cantity').val()};
    saveAssets(url, data, '#supplies_modal', '#supply_form_modal', 'supplies', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
   
}

function delGarageOrderSupply(data){ 
   
    var url = "Intranet/garageOrders/supplies/del";
    var setData = {'id' : data.garageOrdersupply_id};
    delAsset(url, setData, 'supplies', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
    
}

function saveGarageOrderWork(){ 
    var url = "Intranet/garageOrders/works/add";
    
    var data = {'garageOrder_id' : $('.form-horizontal #id').val(), 
            'work_id' : $('#garageOrder_work_form #work_id').val(),
            'pvp' : $('#garageOrder_work_form #pvp').val(),
            'cantity' : $('#garageOrder_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
    
}

function delGarageOrderWork(data){  
    var url = "Intranet/garageOrders/works/del";
    var data = {'id' : data.garageOrderwork_id};
    delAsset(url, data, 'works', 'Intranet/garageOrders/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=repairOrders');
    
}

function get_new_orderNumber(){
     $.ajax({
        method: "POST",
        url: "Intranet/garageOrders/number/get",
        data: {},
        dataType: "json",
        success: function(data){            
            $('#orderNumber').val(data);
        }
    });
}

function set_garageOrder_price(){    
    var discount = numeral($('#discountOrder').val());      
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral(baseComponents.value() + baseSupplies.value() + baseWorks.value() - discount.value());
    $('#formOrdendeTrabajo #baseOrder').val(sum_bases.format('(0.0,$)'));
    $('#formOrdendeTrabajo #tvaOrder').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#tvaOrder').val());
    $('#formOrdendeTrabajo #totalOrder').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));    
}


/*
 * =============================================================================
 * Work Sheets Functions
 * =============================================================================
 */

function get_new_workSheetNumber(){
     $.ajax({
        method: "POST",
        url: "Intranet/workSheets/number/get",
        data: {},
        dataType: "json",
        success: function(data){            
            $('#workSheetNumber').val(data);
        }
    });
}

function saveWorkSheetsComponent(){
    var url = "Intranet/workSheets/components/add";   
    var data = {'workSheet_id' :  $('.form-horizontal #id').val(), 
            'component_id' : $('#workSheets_component_form #component_id').val(),
            'pvp' : $('#workSheets_component_form #pvp').val(),
            'cantity' : $('#workSheets_component_form #cantity').val()};
   
    saveAssets(url, data, '#components_modal', '#component_form_modal','components', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=workSheets');
   
}

function delWorkSheetsComponent(data){     
    var url = "Intranet/workSheets/components/del";
    var setData = {'id' : data.workSheetComponent_id};    
    delAsset(url, setData, 'components', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=workSheets');
    
}


function saveWorkSheetsSupply(){  
    var url = "Intranet/workSheets/supplies/add";
    var data = {'workSheet_id' : $('.form-horizontal #id').val(), 
            'supply_id' : $('#workSheets_supply_form #supply_id').val(),
            'pvp' : $('#workSheets_supply_form #pvp').val(),
            'cantity' : $('#workSheets_supply_form #cantity').val()};
    
    saveAssets(url, data, '#supplies_modal', '#supply_form_modal', 'supplies', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=workSheets');
   
}

function delWorkSheetsSupply(data){    
    var url = "Intranet/workSheets/supplies/del";
    var setData = {'id' : data.workSheetSupply_id};
    delAsset(url, setData, 'supplies', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=vehicles');
    
}

function saveWorkSheetsWork(){ 
    var url = "Intranet/workSheets/works/add";
    
    var data = {'workSheet_id' : $('.form-horizontal #id').val(), 
            'work_id' : $('#workSheets_work_form #work_id').val(),
            'pvp' : $('#workSheets_work_form #pvp').val(),
            'cantity' : $('#workSheets_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=workSheets');
    
}

function delWorkSheetsWork(data){  
    var url = "Intranet/workSheets/works/del";
    var data = {'id' : data.workSheetWork_id};
    delAsset(url, data, 'works', 'Intranet/workSheets/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=workSheets');
    
}

function set_worksheet_prices(){  
    
    var discount = numeral($('#discountWorkSheet').val());      
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral(baseComponents.value() + baseSupplies.value() + baseWorks.value() - discount.value());
    $('#formHojadeTrabajo #baseWorkSheet').val(sum_bases.format('(0.0,$)'));
    $('#formHojadeTrabajo #tvaWorkSheet').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#tvaWorkSheet').val());
    $('#formHojadeTrabajo #totalWorkSheet').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));    
}

/*
 * =============================================================================
 * Expert Opinions Functions
 * =============================================================================
 */

function get_new_opinionId(){
     $.ajax({
        method: "POST",
        url: "Intranet/garages/expertOpinions/id/get",
        data: {},
        dataType: "json",
        success: function(data){  
            $('#opinionId').val(data);
        }
    });
}

function saveExpertOpinionsComponent(){
    var url = "Intranet/garages/expertOpinions/components/add";   
    var data = {'expertOpinion_id' :  $('.form-horizontal #id').val(), 
            'component_id' : $('#expertOpinion_component_form #component_id').val(),
            'pvp' : $('#expertOpinion_component_form #pvp').val(),
            'cantity' : $('#expertOpinion_component_form #cantity').val()};  
    
    saveAssets(url, data, '#components_modal', '#component_form_modal','components', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
   
}

function delExpertOpinionsComponent(data){     
    var url = "Intranet/garages/expertOpinions/components/del";
    var setData = {'id' : data.expertOpinionComponent_id};    
    delAsset(url, setData, 'components', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}


function saveExpertOpinionsSupply(){  
    var url = "Intranet/garages/expertOpinions/supplies/add";
    var data = {'expertOpinion_id' : $('.form-horizontal #id').val(), 
            'supply_id' : $('#expertOpinion_supply_form #supply_id').val(),
            'pvp' : $('#expertOpinion_supply_form #pvp').val(),
            'cantity' : $('#expertOpinion_supply_form #cantity').val()};
    
    saveAssets(url, data, '#supplies_modal', '#supply_form_modal', 'supplies', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
   
}

function delExpertOpinionsSupply(data){    
    var url = "Intranet/garages/expertOpinions/supplies/del";
    var setData = {'id' : data.expertOpinionSupply_id};
    delAsset(url, setData, 'supplies', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function saveExpertOpinionsWork(){ 
    var url = "Intranet/garages/expertOpinions/works/add";
    
    var data = {'expertOpinion_id' : $('.form-horizontal #id').val(), 
            'work_id' : $('#expertOpinion_work_form #work_id').val(),
            'pvp' : $('#expertOpinion_work_form #pvp').val(),
            'cantity' : $('#expertOpinion_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function delExpertOpinionsWork(data){  
    var url = "Intranet/garages/expertOpinions/works/del";
    var data = {'id' : data.expertOpinionWork_id};
    delAsset(url, data, 'works', 'Intranet/garages/expertOpinions/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function set_expertOpinion_prices(){      
    var discount = numeral($('#discountExpertOpinion').val());      
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral(baseComponents.value() + baseSupplies.value() + baseWorks.value() - discount.value());
    $('#formPeritacion #baseExpertOpinion').val(sum_bases.format('(0.0,$)'));
    $('#formPeritacion #tvaExpertOpinion').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#tvaExpertOpinion').val());
    $('#formPeritacion #totalExpertOpinion').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));    
}
