/* Jquery */
$(document).ready(function(){ 
    
    /*
     *   Init DataTables
     */
    
    
    $('.dataTable').DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    
    
    
    let table = new DataTable('#dataTableComponents');
    table.on('dblclick', 'tbody tr', function(){
        let data = table.row(this).data();
        setVehicleComponent(data);        
    });
    
    
    /* Function to reset all the inputs */
    
    $('#reset').on('click', function(){
        $('input[type=text]').each(function(){
           $(this).val(""); 
        });
        
    });
    
    /*
     * 
     *   Function to keep opened the menu-collapse selected and activate current screen.
     * 
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
    
    $('.select2').select2();
    
    
    
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
    
    set_accesories();
    
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
            
   
    
    
});

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

function set_components(){
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

function setVehicleComponent(data){ 
    
    var array = [];
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    }    
    $('#components_form_modal .modal-body .row .input-group #id').val(array[0]);
    $('#components_form_modal .modal-body .row .input-group #ref').val(array[1]);
    $('#components_form_modal .modal-body .row .input-group #name').val(array[2]);    
    $('#components_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[3])).format('(0,0.00$)'));
    $('#components_form_modal .modal-body .row .input-group #cantity').val(array[4]);   
    $('#components_form_modal').modal('show');
}



function saveVehicleComponent(){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleComponents/save",
        data: {'vehicle_id' : $('#id').val(), 
            'component_id' : $('#vehicle_component_form #id').val(),
            'pvp' : $('#vehicle_component_form #pvp').val(),
            'cantity' : $('#vehicle_component_form #cantity').val()},
        dataType: "json",
        success: function(result){            
            $('#components_form_modal').modal('hide');
            $('#components_modal').modal('hide');
            $('.alert').html(result);
            set_components();
          
        }
    });
}


function delVehicleComponent(data){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleComponents/del",
        data: {'id' : data.id},
        dataType: "json",
        success: function(result){          
            $('.alert').html(result);
            set_components();
          
        }
    });
}

function set_supplies(){
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleSupplies/set",
        data: {'vehicle_id' : $('#id').val()},
        dataType: "json",
        success: function(){  
            var url = location.href; 
            console.log(url);
            var pos = url.indexOf('&selected_tab=');
            console.log(pos);
            if(pos !== -1){
                url = url.substr(0, pos);
            }
            url = url + '&selected_tab=supplies';
            location.href = url;
        }
    });
}

function setVehicleSupply(data){ 
    var array = [];
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    }    
    $('#supplies_form_modal .modal-body .row .input-group #id').val(array[0]);
    $('#supplies_form_modal .modal-body .row .input-group #ref').val(array[1]);
    $('#supplies_form_modal .modal-body .row .input-group #name').val(array[2]);    
    $('#supplies_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[3])).format('(0,0.00$)'));      
    $('#supplies_form_modal .modal-body .row .input-group #cantity').val(array[4]);
    $('#supplies_form_modal .modal-body .row .input-group #total').val(numeral(parseFloat(array[3]) * parseFloat(array[4])).format('(0,0.00$)'));
    $('#supplies_form_modal').modal('show');
}



function saveVehicleSupply(){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleSupplies/add",
        data: {'vehicle_id' : $('#id').val(), 
            'supply_id' : $('#vehicle_supply_form #id').val(),
            'pvp' : $('#vehicle_supply_form #pvp').val(),
            'cantity' : $('#vehicle_supply_form #cantity').val()},
        dataType: "json",
        success: function(result){ 
            $('#supplies_form_modal').modal('hide');
            $('#supplies_modal').modal('hide');
            $('.alert').html(result);
            set_supplies();
            
          
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
            set_supplies();
            }
    });
}

function set_works(){
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleWorks/set",
        data: {'vehicle_id' : $('#id').val()},
        dataType: "json",
        success: function(){  
            var url = location.href;             
            var pos = url.indexOf('&selected_tab=');            
            if(pos !== -1){
                url = url.substr(0, pos);
            }
            url = url + '&selected_tab=works';                
            location.href = url;
        }
    });
}

function setVehicleWork(data){ 
    var array = [];
    for (var value in data){   
        if(value !== "mader"){
            array.push(data[value]);
        }
    }     
    $('#works_form_modal .modal-body .row .input-group #id').val(array[0]);
    $('#works_form_modal .modal-body .row .input-group #reference').val(array[1]);
    $('#works_form_modal .modal-body .row .input-group #description').val(array[2]);    
    $('#works_form_modal .modal-body .row .input-group #pvp').val(numeral(parseFloat(array[3])).format('(0,0.00$)'));      
    $('#works_form_modal .modal-body .row .input-group #cantity').val(array[4]);
    $('#works_form_modal .modal-body .row .input-group #total').val(numeral(parseFloat(array[3]) * parseFloat(array[4])).format('(0,0.00$)'));
    $('#works_form_modal').modal('show');
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
            set_works();
        }
    });
}


function delVehicleWork(data){    
    $.ajax({
        method: "POST",
        url: "Intranet/vehicles/vehicleWorks/del",
        data: {'id' : data.id},
        dataType: "json",
        success: function(result){            
            $('.alert').html(result);
            set_works();
          
        }
    });
}


/* Javascript */

window.addEventListener('load', function(){
    
    //Configuración de la libreria Numeral de JS para el formato de numeros en Moneda
    
    numeral.register('locale', 'es', {
        delimiters: {
            thousands: ',',
            decimal: '.'
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
    
    // Recorrido de todos los elementos con la clase "precio" para su formato de moneda
    
    
    
});

