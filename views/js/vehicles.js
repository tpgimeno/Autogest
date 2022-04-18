/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global numeral */
/* Global variable creation */

var vehicle_components = new Array();
var price_components = null;
var tva_components = null;
var total_components = null;
var vehicle_supplies = new Array();
var price_supplies = null;
var tva_supplies = null;
var total_supplies = null;
var price_vehicle = null;
var suma_prices_supplies = 0;
var suma_prices_components = 0;

window.addEventListener('load', function () {    
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
    var selected_tab = "{{ selectedTab }}";
    switch (selected_tab)
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
    document.getElementById("inputPvp").value = pvp.format('(0,0.0$)');
    var pvc = numeral(document.getElementById("inputCost").value);
    document.getElementById("inputCost").value = pvc.format('(0,0.0$)');
    var tva_pvc = numeral(pvc.value() * 0.21);
    document.getElementById("inputTvaCost").value = tva_pvc.format('(0,0.0$)');
    var tva_pvp = numeral(pvp.value() * 0.21);
    document.getElementById("inputTvaSell").value = tva_pvp.format('(0,0.0$)');
    var total_pvc = numeral(pvc.value() + tva_pvc.value());
    document.getElementById("inputTotalCost").value = total_pvc.format('(0,0.0$)');
    var total_pvp = numeral(pvp.value() + tva_pvp.value());
    document.getElementById("inputTotalSell").value = total_pvp.format('(0,0.0$)');
    var componentPvp = numeral(document.getElementById('inputComponentPrice').value);
    document.getElementById('inputComponentPrice').value = componentPvp.format('(0,0.0$');
    var componentCantity = numeral(document.getElementById('inputComponentCantity').value);
    var componentBase = numeral(componentPvp.value() * componentCantity.value());
    document.getElementById('inputComponentTotal').value = componentBase.format('0,0.0$');
    var total_component = document.getElementById('inputComponentTotal').value;
    var supplyPvp = numeral(document.getElementById('inputSupplyPrice').value);
    document.getElementById('inputSupplyPrice').value = supplyPvp.format('(0,0.0$');
    var supplyCantity = numeral(document.getElementById('inputSupplyCantity').value);
    var supplyBase = numeral(supplyPvp.value() * supplyCantity.value());
    document.getElementById('inputSupplyTotal').value = supplyBase.format('0,0.0$');
    var total_supply = document.getElementById('inputSupplyTotal').value;

    document.getElementById('inputSupplyPrice').addEventListener("change", function () {
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0,0.0$)');
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        total_supply = numeral(price_supply.value() * cantity_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,0.0$)');
    });
    document.getElementById('inputSupplyCantity').addEventListener("change", function () {
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        total_supply = numeral(price_supply.value() * cantity_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,0.0$)');
    });
    document.getElementById('inputSupplyTotal').addEventListener("change", function () {
        total_supply = numeral(document.getElementById('inputSupplyTotal').value);
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,0.0$)');
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        price_supply = numeral(total_supply.value() / cantity_supply.value());
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0,0.0$)');
    });
    document.getElementById('inputComponentPrice').addEventListener("change", function () {
        price_component = numeral(document.getElementById('inputComponentPrice').value);
        document.getElementById('inputComponentPrice').value = price_component.format('(0,0.0$)');
        cantity_component = numeral(document.getElementById('inputComponentCantity').value);
        base_component = numeral(price_component.value() * cantity_component.value());
        total_component.value = numeral(base_component.value()).format('(0,0.0$)');
    });
    document.getElementById('inputComponentCantity').addEventListener("change", function () {
        price_component = numeral(document.getElementById('inputComponentPrice').value);
        cantity_component = numeral(document.getElementById('inputComponentCantity').value);
        base_component = numeral(price_component.value() * cantity_component.value());
        document.getElementById('inputComponentTotal').value = base_component.format('(0,0.0$)');
    });
    document.getElementById('inputComponentsPrice').addEventListener('change', function () {
        base = numeral(price_components.value());
        document.getElementById("inputPvp").value = base.format('(0,0.0$)');
        price_components = numeral(document.getElementById('inputComponentsBase').value);
        document.getElementById('inputComponentsBase').value = price_components.format('(0,0.0$)');
        tva_components = numeral(price_components.value() * 0.21);
        tva_components = numeral(document.getElementById('inputComponentsTva').value);
        total_components = numeral(price_components.value() + tva_components.value());
        document.getElementById('inputComponentsTotal').value = total_components.format('(0,0.0$)');
    });
    document.getElementById('inputPvp').addEventListener('change', function(){
        pvc = numeral(document.getElementById('inputPvp').value);
        document.getElementById('inputPvp').value = pvc.format('(0,0.0$)');
        tva_pvc = numeral(pvc.value() * 0.21);
        document.getElementById('inputTvaSell').value = tva_pvc.format('(0,0.0$)');
        total_pvc = numeral(pvc.value() + tva_pvc.value());
        document.getElementById('inputTotalSell').value = total_pvc.format('(0,0.0$)');
    });
    document.getElementById('inputCost').addEventListener('change', function(){
        pvp = numeral(document.getElementById('inputCost').value);
        document.getElementById('inputCost').value = pvp.format('(0,0.0$)');
        tva_pvp = numeral(pvc.value() * 0.21);
        document.getElementById('inputTvaCost').value = tva_pvp.format('(0,0.0$)');
        total_pvp = numeral(pvp.value() + tva_pvp.value());
        document.getElementById('inputTotalCost').value = total_pvp.format('(0,0.0$)');
    });
    var json_components_response = '{{vehicleComponents|raw}}';
    var vehicle = document.getElementById('inputId').value;   
    if (json_components_response.length > 0) {
        vehicle_components = JSON.parse(json_components_response);       
        var table_components = document.getElementById("ComponentsTable");
        while(table_components.childElementCount > 0) {
            table_components.removeChild(table_components.firstChild);
        }
        
        table_components.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr>";

        for (let i = 0; i < vehicle_components.length; i++) {
            vehicle_components[i]['total'] = numeral(numeral(vehicle_components[i]['cantity']).value() * numeral(vehicle_components[i]['pvp']).value()).value();
            table_components.innerHTML += "<tr><td>" + vehicle_components[i]['componentId'] + "</td><td>" + vehicle_components[i]['ref'] + "</td><td>" + vehicle_components[i]['name'] + "</td><td>" + numeral(vehicle_components[i]['pvp']).format('(0,0.0$)') + "</td><td>" + numeral(vehicle_components[i]['cantity']).value() + "</td><td>" + numeral(vehicle_components[i]['total']).format('(0,0.0$)') + "</td><td><a href=/Intranet/vehicles/vehicleComponents/edit?componentId=" + numeral(vehicle_components[i]['componentId']).value() + "&id=" + numeral(vehicle).value() + "&cantity=" + numeral(vehicle_components[i]['cantity']).value() + "&pvp=" + numeral(vehicle_components[i]['pvp']).value() + "&selectedTab=accesories>Edit</a></td><td><a href=/Intranet/vehicles/vehicleComponents/del?componentId=" + numeral(vehicle_components[i]['componentId']).value() + "&id=" + numeral(vehicle).value() + "&cantity=" + numeral(vehicle_components[i]['cantity']).value() + "&pvp=" + numeral(vehicle_components[i]['pvp']).value() +"&selectedTab=accesories>Eliminar</a></td>";
            suma_prices_components += numeral(price_components).value() + numeral(vehicle_components[i]['total']).value();
        }
        if(!price_supplies){
            price_supplies = numeral(0);
        }
        if(!price_components){
            price_components = numeral(0);
        }
        price_components = numeral(suma_prices_components);        
        document.getElementById('inputComponentsPrice').value = price_components.format('(0,0.0$)');
        tva_components = numeral(price_components.value() * 0.21);
        document.getElementById('inputComponentsTva').value = tva_components.format('(0,0.0$)');
        total_components = numeral(price_components.value() + tva_components.value());
        document.getElementById('inputComponentsTotal').value = total_components.format('(0,0.0$)');        
        base = numeral(pvp.value() + price_supplies.value() + price_components.value());
        document.getElementById("inputPvp").value = base.format('(0,0.0$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTvaSell").value = tva.format('(0,0.0$)');
        total = numeral(base.value() + tva.value());
        document.getElementById("inputTotalSell").value = total.format('(0,0.0$)');
    }
    var json_supplies_response = '{{vehicleSupplies|raw}}';
    if (json_supplies_response.length > 0) {
        vehicle_supplies = JSON.parse(json_supplies_response);
        var table_supplies = document.getElementById("SuppliesTable");
        while (table_supplies.childElementCount > 0)
        {
            table_supplies.removeChild(table_supplies.firstChild);
        }
        table_supplies.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr>";

        for (let i = 0; i < vehicle_supplies.length; i++)
        {
            vehicle_supplies[i]['total'] = numeral(numeral(vehicle_supplies[i]['cantity']).value() * numeral(vehicle_supplies[i]['pvp']).value()).value();
            table_supplies.innerHTML += "<tr><td>" + numeral(vehicle_supplies[i]['supplyId']).value() + "</td><td>" + vehicle_supplies[i]['ref'] + "</td><td>" + vehicle_supplies[i]['name'] + "</td><td>" + numeral(vehicle_supplies[i]['pvp']).format('(0,0.0$)') + "</td><td>" + numeral(vehicle_supplies[i]['cantity']).value() + "</td><td>" + numeral(vehicle_supplies[i]['total']).value() + "</td><td><a href=/Intranet/vehicles/vehicleSupplies/edit?supplyId=" + numeral(vehicle_supplies[i]['supplyId']).value() + "&id=" + numeral(vehicle_supplies[i]['vehicleId']).value() + "&selectedTab=accesories>Edit</a></td><td><a href=/Intranet/vehicles/vehicleSupplies/del?supplyId=" + numeral(vehicle_supplies[i]['supplyId']).value() + "&id=" + numeral(vehicle_supplies[i]['vehicleId']).value() + "&selectedTab=accesories>Eliminar</a></td>";
            suma_prices_supplies += numeral(price_supplies).value() + numeral(vehicle_supplies[i]['total']).value();
        }
        price_supplies = numeral(suma_prices_supplies);
        if(!price_supplies)
        {
            price_supplies = numeral(0);
        }
        document.getElementById('inputSuppliesPrice').value = price_supplies.format('(0,0.0$)');
        tva_supplies = numeral(price_supplies.value() * 0.21);
        document.getElementById('inputSuppliesTva').value = tva_supplies.format('(0,0.0$)');
        total_supplies = numeral(price_supplies.value() + tva_supplies.value());
        document.getElementById('inputSuppliesTotal').value = total_supplies.format('(0,0.0$)');
        base = numeral(pvp.value() + price_supplies.value() + price_components.value());
        document.getElementById("inputPvp").value = base.format('(0,0.0$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTvaSell").value = tva.format('(0,0.0$)');
        total = numeral(base.value() + tva.value());
        document.getElementById("inputTotalSell").value = total.format('(0,0.0$)');
    }
    document.getElementById("inputPvp").addEventListener("change", function () {
        var pvp = numeral(document.getElementById("inputPvp").value);
        document.getElementById("inputPvp").value = pvp.format('(0,00.00$)');
    });
    var checkboxes = document.getElementsByClassName('form-check-input');
    for (let i = 0; i < checkboxes.length; i++)
    {
        checkboxes[i].addEventListener("change", function ()
        {
            var vehicle = document.getElementById('inputId');
            var sender = {'accesory': this.value, 'accesory_name': this.name, 'vehicleId': numeral(vehicle.value).value()};
            if (this.checked)
            {
                var request = new XMLHttpRequest();
                request.open('POST', '/Intranet/vehicles/accesories/add', true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                request.send("vhaccesory=" + JSON.stringify(sender));
                function reqListener()
                {
                    var alert = document.getElementById('alert');
                    var response = request.responseText;
                    alert.innerHTML = response;
                }
                request.addEventListener("load", reqListener);
            } else
            {
                var request = new XMLHttpRequest();
                request.open('POST', '/Intranet/vehicles/accesories/del', true);
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
});
function addSupply()
{    
    var vehicle = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    price_vehicle = numeral(document.getElementById('inputPvp').value);
    var ref_supply = document.getElementById('inputSupplyReference');
    var name_supply = document.getElementById('inputSupplyName');       
    var price_supply = numeral(document.getElementById('inputSupplyPrice').value);
    var cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);    
    price_supplies = numeral(document.getElementById('inputSuppliesBase').value);
    tva_supplies = numeral(document.getElementById('inputSuppliesTva').value);
    total_supplies = numeral(document.getElementById('inputSuppliesTotal').value);
    var importe = 0;
    importe = numeral(price_supply.value() * cantity_supply.value());    
    var vehicle_supply = new Array(); 
    vehicle_supply = {'id':numeral(vehicle.value).value(),'supplyId':numeral(id_supply.value).value(),'reference':ref_supply.value,'name':name_supply.value,'price':price_supply.value(),'cantity':cantity_supply.value(),'total':importe.value()};
    
    var exists = false;
    if(vehicle_supplies.length > 0)
    {        
        for(let i=0;i < vehicle_supplies.length; i++)
        {
            if(vehicle_supply['supplyId'] === vehicle_supplies[i]['supplyId'])
            {                
                vehicle_supplies[i]['cantity'] = numeral(numeral(vehicle_supplies[i]['cantity']).value() + numeral(vehicle_supply['cantity']).value()).value();
                vehicle_supplies[i]['total'] = numeral(numeral(vehicle_supplies[i]['price']).value() * numeral(vehicle_supplies[i]['cantity']).value()).value();
                exists = true;
            }            
        }
        if(exists === false)
        {
            vehicle_supplies.push(vehicle_supply);
        }        
    }
    else
    {
        vehicle_supplies.push(vehicle_supply);                 
    }     
    var table_supplies = document.getElementById("SuppliesTable");
    while(table_supplies.childElementCount > 0)
    {
        table_supplies.removeChild(table_supplies.firstChild);
    }    
    table_supplies.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr> ";
    if(vehicle_supplies.length === 0)
    {
        table_supplies.innerHTML += "<tr><td> No hay datos </td></tr>";        
    }
    else
    {
        for(let i = 0; i < vehicle_supplies.length ; i++)
        {            
            table_supplies.innerHTML += "<tr><td>"+numeral(vehicle_supplies[i]['supplyId']).value()+"</td><td>"+vehicle_supplies[i]['reference']+"</td><td>"+vehicle_supplies[i]['name']+"</td><td>"+numeral(vehicle_supplies[i]['price']).format('(0,0.0$)')+"</td><td>"+numeral(vehicle_supplies[i]['cantity']).value()+"</td><td>"+numeral(vehicle_supplies[i]['total']).format('(0,0.0$)')+"</td><td><a href=/Intranet/vehicles/vehicleSupplies/edit?supplyId="+numeral(vehicle_supplies[i]['supplyId']).value()+"&id="+numeral(vehicle_supplies[i]['vehicleId']).value()+"&cantity=" + numeral(vehicle_supplies[i]['cantity']).value() + "&price=" + numeral(vehicle_supplies[i]['price']).value() +">Edit</a></td><td><a href=/Intranet/vehicles/vehicleSupplies/del?supplyId="+vehicle_supplies[i]['supplyId']+"&id="+numeral(vehicle.value).value()+">Eliminar</a></td>";
            suma_prices_supplies += numeral(vehicle_supplies[i]['total']).value();           
        }  
    }       
    price_supplies = numeral(suma_prices_supplies).value();
    tva_supplies = numeral(numeral(price_supplies).value() * 0.21);
    total_supplies = numeral(price_supplies).value() + numeral(tva_supplies).value();
    document.getElementById('inputSuppliesPrice').value = numeral(price_supplies).format('(0,0.0$)');
    document.getElementById('inputSuppliesTva').value = numeral(tva_supplies).format('(0,0.0$)');
    document.getElementById('inputSuppliesTotal').value = numeral(total_supplies).format('(0,0.0$)');   
    base = numeral(price_vehicle.value() + numeral(price_supplies).value() + numeral(price_components).value());
    document.getElementById("inputPvp").value = base.format('(0,0.0$)');  
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTvaSell").value = tva.format('(0,0.0$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotalSell").value = total.format('(0,0.0$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/Intranet/vehicles/vehicleSupplies/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
    
    request.send('supply=' + JSON.stringify(vehicle_supply));    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
    
}
function addComponent() {
    var vehicle = document.getElementById('inputId');  
    price_vehicle = document.getElementById('inputPvp').value;
    var id_component = document.getElementById('inputComponentId');
    var ref_component = document.getElementById('inputComponentRef');
    var name_component = document.getElementById('inputComponentName');
    var price_component = numeral(document.getElementById('inputComponentPrice').value);
    var cantity_component = numeral(document.getElementById('inputComponentCantity').value);
    price_components = numeral(document.getElementById('inputComponentsPrice').value);
    tva_components = numeral(document.getElementById('inputComponentsTva').value);
    total_components = numeral(document.getElementById('inputComponentsTotal').value);
    var importe = 0;
    importe = numeral(price_component.value() * cantity_component.value());
    var vehicle_component = new Array();
    vehicle_component = {'id': numeral(vehicle.value).value(), 'componentId': numeral(id_component.value).value(), 'ref': ref_component.value, 'name': name_component.value, 'pvp': price_component.value(), 'cantity': cantity_component.value(), 'total': importe.value()};
   
    if (vehicle_components.length > 0)
    {
        for (let i = 0; i < vehicle_components.length; i++)
        {
            if (vehicle_component['componentId'] === vehicle_components[i]['componentId'])
            {
                vehicle_components[i]['cantity'] = numeral(numeral(vehicle_components[i]['cantity']).value() + numeral(vehicle_component['cantity']).value()).value();
                vehicle_components[i]['total'] = numeral(numeral(vehicle_components[i]['pvp']).value() * numeral(vehicle_components[i]['cantity']).value()).value();
            } else
            {
                vehicle_components.push(vehicle_component);
            }
        }
    } else
    {
        vehicle_components.push(vehicle_component);
    }
    var table_components = document.getElementById("ComponentsTable");    
    if (table_components.childElementCount > 0)
    {
        table_components.removeChild(table_components.firstChild);
    }
    table_components.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr> "
    if (vehicle_components.length === 0)
    {
        table_components.innerHTML += "<tr><td> No hay datos </td></tr>";
    } else
    {
        for (let i = 0; i < vehicle_components.length; i++)
        {
            table_components.innerHTML += "<tr><td>" + numeral(vehicle_components[i]['componentId']).value() + "</td><td>" + vehicle_components[i]['ref'] + "</td><td>" + vehicle_components[i]['name'] + "</td><td>" + numeral(vehicle_components[i]['pvp']).format('(0,0.0$)') + "</td><td>" + numeral(vehicle_components[i]['cantity']).value() + "</td><td>" + numeral(vehicle_components[i]['total']).format('(0,0.0$)') + "</td><td><a href=/Intranet/vehicles/vehicleComponents/edit?componentId=" + numeral(vehicle_components[i]['componentId']).value() + "&id=" + numeral(vehicle.value).value() + "&cantity=" + numeral(vehicle_components[i]['cantity']).value() + "&pvp=" + numeral(vehicle_components[i]['pvp']).value() + "&selectedTab=accesories'>Edit</a></td><td><a href=/Intranet/vehicles/vehicleComponents/del?componentId=" + numeral(vehicle_components[i]['componentId']).value() + "&id=" + numeral(vehicle.value).value() + "&selectedTab=accesories>Eliminar</a></td>";
            suma_prices_components += numeral(price_components).value() + numeral(vehicle_components[i]['total']).value();
        }
    }
    price_components = numeral(suma_prices_components).value();
    tva_components = numeral(numeral(price_components).value() * 0.21);
    total_components = numeral(price_components).value() + numeral(tva_components).value();
    document.getElementById('inputComponentsPrice').value = numeral(price_components).format('(0,0.0$)');
    document.getElementById('inputComponentsTva').value = numeral(tva_components).format('(0,0.0$)');
    document.getElementById('inputComponentsTotal').value = numeral(total_components).format('(0,0.0$)');
   
    base = numeral(numeral(price_vehicle.value).value() + numeral(price_supplies).value() + numeral(price_components).value());
    document.getElementById("inputPvp").value = base.format('(0,0.0$)');

    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTvaSell").value = tva.format('(0,0.0$)');
    total = numeral(base.value() + tva.value());
    document.getElementById("inputTotalSell").value = total.format('(0,0.0$)');
    var request = new XMLHttpRequest();
    request.open('POST', '/Intranet/vehicles/vehicleComponents/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send('component=' + JSON.stringify(vehicle_component));
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
}