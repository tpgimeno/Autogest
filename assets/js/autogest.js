/* Jquery */
$(document).ready(function(){    
    $('.dataTable').DataTable();
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

