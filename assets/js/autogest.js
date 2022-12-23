/* Jquery */
$(document).ready(function(){    
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
    
    $('#reset').on('click', function(){
        $('input[type=text]').each(function(){
           $(this).val(""); 
        });
        
    });
});


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

