/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


window.addEventListener('load', function()
{ 
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
    
    var pvp = numeral(document.getElementById("inputPvp").value);
    document.getElementById("inputPvp").value = pvp.format('(0,00.00$)'); 
    var pvc = numeral(document.getElementById("inputCost").value);
    document.getElementById("inputCost").value = pvc.format('(0,00.00$)'); 
    var tva_pvc = numeral(pvc.value() * 0.21);
    document.getElementById("inputTvaCost").value = tva_pvc.format('(0,00.00$)');
    var tva_pvp = numeral(pvp.value() * 0.21);
    document.getElementById("inputTvaSell").value = tva_pvp.format('(0,00.00$)');
    var total_pvc = numeral(pvc.value() + tva_pvc.value());
    document.getElementById("inputTotalCost").value = total_pvc.format('(0,00.00$)');
    var total_pvp = numeral(pvp.value() + tva_pvp.value());
    document.getElementById("inputTotalSell").value = total_pvp.format('(0,00.00$)');
   
    
    document.getElementById("inputPvp").addEventListener("change", function()
    {
        var pvp = numeral(document.getElementById("inputPvp").value);
        document.getElementById("inputPvp").value = pvp.format('(0,00.00$)');
    });
    
});