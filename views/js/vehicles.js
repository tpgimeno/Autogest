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
    
    var data = document.getElementById('data');
    var data_tab = document.getElementById('data-tab');
    var buy = document.getElementById('buy');
    var buy_tab = document.getElementById('buy-tab');
    var sell = document.getElementById('sell');
    var sell_tab = document.getElementById('sell-tab');
    var accesories = document.getElementById('accesories');
    var accesories_tab = document.getElementById('accesories-tab');
    var selected_tab = "{{ selected_tab }}";
    
    switch(selected_tab)
    {
        case 'data':
            data.classList.add('show', 'active');
            data_tab.classList.add('active');
            buy.classList.remove('show', 'active');
            buy_tab.classList.remove('active');
            sell.classList.remove('show', 'active');
            sell_tab.classList.remove('active');
            accesories.classList.remove('show', 'active');
            accesories_tab.classList.remove('active');
            break;
        case 'buy':
            data.classList.remove('show', 'active');
            data_tab.classList.remove('active');
            buy.classList.add('show', 'active');
            buy_tab.classList.add('active');
            sell.classList.remove('show', 'active');
            sell_tab.classList.remove('active');
            accesories.classList.remove('show', 'active');
            accesories_tab.classList.remove('active');
            break;
        case 'sell':
            data.classList.remove('show', 'active');
            data_tab.classList.remove('active');
            buy.classList.remove('show', 'active');
            buy_tab.classList.remove('active');
            sell.classList.add('show', 'active');
            sell_tab.classList.add('active');
            accesories.classList.remove('show', 'active');
            accesories_tab.classList.remove('active');
            break;
        case 'accesories':
            data.classList.remove('show', 'active');
            data_tab.classList.remove('active');
            buy.classList.remove('show', 'active');
            buy_tab.classList.remove('active');
            sell.classList.remove('show', 'active');
            sell_tab.classList.remove('active');
            accesories.classList.add('show', 'active');
            accesories_tab.classList.add('active');
            break;
    }
    
    
    
    
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
    var checkboxes = document.getElementsByClassName('form-check-input');
    for(let i = 0; i < checkboxes.length;i++)
    {
        checkboxes[i].addEventListener("change", function()
        {
            var vehicle = document.getElementById('inputId'); 
            var sender = {'accesory': this.value, 'accesory': this.name, 'vehicle_id':numeral(vehicle.value).value()};            
            if(this.checked)
            {
                var request = new XMLHttpRequest();
                request.open('POST', '/intranet/vehicles/accesories/add', true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');                 
                request.send("vhaccesory=" + JSON.stringify(sender));
                function reqListener()
                {
                   var alert = document.getElementById('alert');
                   var response = request.responseText;
                   alert.innerHTML = response;                   
                }
                request.addEventListener("load", reqListener);
            }
            else
            {
                var request = new XMLHttpRequest();
                request.open('POST', '/intranet/vehicles/accesories/del', true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');                
                request.send("vhaccesory=" + JSON.stringify(sender));
                function reqListener()
                {
                   var alert = document.getElementById('alert');
                   var response = request.responseText;
                   alert.innerHTML = response;  
                }
                request.addEventListener("load", reqListener);
            }
        });
    }
    var type = "{{ type_selected }}";
    document.getElementById("selectType").value = type;
    var brand = "{{ brand_selected }}";
    document.getElementById("selectBrand").value = brand;
    var model = "{{ model_selected}}";
    document.getElementById("selectModel").value = model;
    var store = "{{ store_selected}}";
    document.getElementById("selectStore").value = store;
    var location = "{{ location_selected }}";
    document.getElementById("selectLocation").value = location;
    
});