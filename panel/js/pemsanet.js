/* Jquery */
$(document).ready(function(){       
    $(function() {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        $('.swalDefaultError').click(function() {
            Toast.fire({
                icon: 'error',
                title: 'El Título del combinado no pueder ser repetido!'
            });
        });
    }); 
    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
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
    
    var precios =  document.getElementsByClassName('precio');   
    precios.forEach((item) =>{
        formatoPrecios(item);
        item.addEventListener("change", function(){
            formatoPrecios(item);
        });
    }); 
    
    
});
function formatoPrecios(item){        
    if(!item.innerHTML){
        var tmp = numeral(item.value);
        item.value = tmp.format('(0,0.00$)');
    }else{ 
        var tmp = numeral(item.innerHTML);
        item.innerHTML = tmp.format('(0,0.00$)');
    }
}
function redondeo_precios(precio){
    var result = Math.ceil(precio);
    return result - 0.1;
}

