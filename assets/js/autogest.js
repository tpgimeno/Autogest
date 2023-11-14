/* Jquery */
$(document).ready(function(){ 
    
    /*
     *   Init DataTables
     */
    
    
    $('.dataTable').DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#dataTable_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true
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
    set_components();
    
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
        success: function(data){
            console.log(data);
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

