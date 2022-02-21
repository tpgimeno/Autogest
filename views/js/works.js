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
    var tva_pvp = 0;   
    var total_pvp = 0;  
    
    
    tva_pvp = numeral(pvp.value() * 0.21);
   
    total_pvp = numeral(pvp.value() + tva_pvp.value());    
    document.getElementById("inputPvp").value = pvp.format('(0,00.00$)');    
    document.getElementById("inputTvaSell").value = tva_pvp.format('(0,00.00$)');
    document.getElementById("inputTotalSell").value = total_pvp.format('(0,00.00$)');    
    
    document.getElementById("inputPvp").addEventListener("change", function()
    {        
        pvp = numeral(document.getElementById("inputPvp").value);        
        document.getElementById("inputPvp").value = pvp.format('(0,00.00$)');
        tva_pvp = numeral(pvp.value() * 0.21);
        total_pvp = numeral(pvp.value() + tva_pvp.value());
        document.getElementById("inputTvaSell").value = tva_pvp.format('(0,00.00$)');
        document.getElementById("inputTotalSell").value = total_pvp.format('(0,00.00$)');        
         
    });   
    document.getElementById("inputTotalSell").addEventListener("change", function()
    {        
        total_pvp = numeral(document.getElementById("inputTotalSell").value);        
        document.getElementById("inputTotalSell").value = total_pvp.format('(0,00.00$)');
        pvp = numeral(total_pvp.value() / 1.21);
        tva_pvp = numeral(total_pvp.value() - pvp.value());
        document.getElementById("inputPvp").value = pvp.format('(0,00.00$)');
        document.getElementById("inputTvaSell").value = tva_pvp.format('(0,00.00$)');        
         
    });
    
});
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


