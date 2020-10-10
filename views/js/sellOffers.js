/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global numeral */


var offer_supplies = new Array();
var offer_components = new Array();
var offer_works = new Array();
var price_supplies = null;
var tva_supplies = null;
var total_supplies = null;

window.addEventListener('load', function()
{            
    var offer = document.getElementById('offer');
    var tab_offer = document.getElementById('offer-tab');
    var customer = document.getElementById('customer');
    var tab_customer = document.getElementById('customer-tab');
    var vehicle = document.getElementById('vehicle');
    var tab_vehicle = document.getElementById('vehicle-tab');
    var supplies = document.getElementById('supplies');
    var tab_supplies = document.getElementById('supplies-tab');
    var works = document.getElementById('works');
    var tab_works = document.getElementById('works-tab');
    var selected_tab = '{{ selected_tab }}';
    
    switch(selected_tab)
    {
        case 'offer':
            customer.classList.remove('show', 'active');
            tab_customer.classList.remove('active');
            vehicle.classList.remove('show', 'active');
            tab_vehicle.classList.remove('active');
            supplies.classList.remove('show', 'active');
            tab_supplies.classList.remove('active');
            works.classList.remove('show', 'active');
            tab_works.classList.remove('active');
            offer.classList.add('show', 'active');
            tab_offer.classList.add('active');
            break;
            
        case 'customer':
            customer.classList.add('show', 'active');
            tab_customer.classList.add('active');
            vehicle.classList.remove('show', 'active');
            tab_vehicle.classList.remove('active');
            supplies.classList.remove('show', 'active');
            tab_supplies.classList.remove('active');
            works.classList.remove('show', 'active');
            tab_works.classList.remove('active');
            offer.classList.remove('show', 'active');
            tab_offer.classList.remove('active');
            break;
        
        case 'vehicle':
            customer.classList.remove('show', 'active');
            tab_customer.classList.remove('active');
            vehicle.classList.add('show', 'active');
            tab_vehicle.classList.add('active');
            supplies.classList.remove('show', 'active');
            tab_supplies.classList.remove('active');
            works.classList.remove('show', 'active');
            tab_works.classList.remove('active');
            offer.classList.remove('show', 'active');
            tab_offer.classList.remove('active');
            break;
            
        case 'supplies':
            customer.classList.remove('show', 'active');
            tab_customer.classList.remove('active');
            vehicle.classList.remove('show', 'active');
            tab_vehicle.classList.remove('active');
            supplies.classList.add('show', 'active');
            tab_supplies.classList.add('active');
            works.classList.remove('show', 'active');
            tab_works.classList.remove('active');
            offer.classList.remove('show', 'active');
            tab_offer.classList.remove('active');
            break;
        case 'works':
            customer.classList.remove('show', 'active');
            tab_customer.classList.remove('active');
            vehicle.classList.remove('show', 'active');
            tab_vehicle.classList.remove('active');
            supplies.classList.remove('show', 'active');
            tab_supplies.classList.remove('active');
            works.classList.add('show', 'active');
            tab_works.classList.add('active');
            offer.classList.remove('show', 'active');
            tab_offer.classList.remove('active');
            break;
            
        default:
            customer.classList.remove('show', 'active');
            tab_customer.classList.remove('active');
            vehicle.classList.remove('show', 'active');
            tab_vehicle.classList.remove('active');
            supplies.classList.remove('show', 'active');
            tab_supplies.classList.remove('active');
            works.classList.remove('show', 'active');
            tab_works.classList.remove('active');
            offer.classList.add('show', 'active');
            tab_offer.classList.add('active');
            break;
    }    
    
    document.getElementById("searchCustomerField").addEventListener("keyup", function()
    {                      
        var request = new XMLHttpRequest();
        request.open('POST', '/intranet/crm/offers/customer/search', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        if(document.getElementById('searchCustomerField').value == "")
        {
        }
        else
        {
            request.send("searchCustomerFilter=" + document.getElementById('searchCustomerField').value);
            function reqListener()
            {
                var response = JSON.parse(request.responseText);                            
                var table = document.getElementById("customersTable");
                while(table.childElementCount > 0)
                {
                    table.removeChild(table.firstChild);
                }                        
                if(!response[0])
                {
                    table.innerHTML = "<tr><td> No hay datos </td></tr>";
                }
                else
                {                          
                   for(let i = 0; i < response.length ; i++)
                   {
                       table.innerHTML += "<tr><td>"+response[i]['name']+"</td><td>"+response[i]['city']+"</td><td>"+response[i]['fiscal_id']+"</td><td>"+response[i]['address']+"</td><td><a href=/intranet/crm/offers/customer/select?customer_id="+response[i]['id']+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}>Select</a></td></tr>";
                   }                               
                }                        
            }
            request.addEventListener("load", reqListener);
        }                   
    });
    document.getElementById("searchVehicleField").addEventListener("keyup", function()
    {                      
        var request = new XMLHttpRequest();
        request.open('POST', '/intranet/crm/offers/vehicle/search', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        if(document.getElementById('searchVehicleField').value == "")
        {                        
        }
        else
        {
            request.send("searchVehicleFilter=" + document.getElementById('searchVehicleField').value);
            function reqListener()
            {
                var response = JSON.parse(request.responseText);                            
                var table = document.getElementById("vehiclesTable");
                while(table.childElementCount > 0)
                {
                    table.removeChild(table.firstChild);
                }                        
                if(!response[0])
                {
                    table.innerHTML = "<tr><td> No hay datos </td></tr>";
                }
                else
                {                          
                   for(let i = 0; i < response.length ; i++)
                   {
                       table.innerHTML += "<tr><td>"+response[i]['plate']+"</td><td>"+response[i]['vin']+"</td><td>"+response[i]['brand']+"</td><td>"+response[i]['model']+"</td><td><a href=/intranet/crm/offers/customer/select?customer_id="+response[i]['id']+"&offer_id={{offer.id}}&customer_id={{customer.id}}>Select</a></td></tr>";
                   }                               
                }                        
            }
            request.addEventListener("load", reqListener);
        }                   
    });                

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
        ordinal: function (number) {
            var b = number % 10;
            return (b === 1 || b === 3) ? 'er' :
                (b === 2) ? 'do' :
                (b === 7 || b === 0) ? 'mo' :
                (b === 8) ? 'vo' :
                (b === 9) ? 'no' : 'to';
        },
        currency: {
            symbol: '€'
        }
    });
    numeral.locale('es');

    var price = numeral(document.getElementById("inputPrice").value); 
    var discount = numeral(document.getElementById("inputDiscount").value);
    var base = numeral(0);
    var tva = numeral(document.getElementById("inputTva").value);
    var total = numeral(document.getElementById("inputTotal").value);
           
    var price_vehicle = numeral(document.getElementById('inputVehiclePrice').value); 
    var vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
    var base_vehicle = numeral(price_vehicle.value() - vehicle_discount.value());
    var tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);  
    var total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
    
    var price_supply = numeral(document.getElementById('inputSupplyPrice').value);
    var base_supply = numeral(0);    
    var total_supply = numeral(document.getElementById('inputSupplyTotal').value);
    
    var price_component = numeral(document.getElementById('inputComponentPrice').value);
    var base_component = numeral(0);    
    var total_component = numeral(document.getElementById('inputComponentTotal').value);
    
    var price_supplies = numeral(document.getElementById('inputSuppliesBase').value);
    var tva_supplies = numeral(document.getElementById('inputSuppliesTva').value);
    var total_supplies = numeral(document.getElementById('inputSuppliesTotal').value);
    
    var price_components = numeral(document.getElementById('inputComponentsBase').value);
    var tva_components = numeral(document.getElementById('inputComponentsTva').value);
    var total_components = numeral(document.getElementById('inputComponentsTotal').value);
    
    var price_works = numeral(document.getElementById('inputWorksBase').value);
    var tva_works = numeral(document.getElementById('inputWorksTva').value);
    var total_works = numeral(document.getElementById('inputWorksTotal').value);
    
    var price_work = numeral(document.getElementById('inputWorkPrice').value);
    var cantity_work = numeral(document.getElementById('inputWorkCantity').value);    
    var total_work = numeral(document.getElementById('inputWorkTotal').value);
    
    document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0,00.00$)');    
    document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0,00.00$)');  
    base_vehicle = numeral(price_vehicle.value() - vehicle_discount.value());
    tva_vehicle = numeral(base_vehicle.value() * 0.21);   
    document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0,00.00$)');    
    total_vehicle = numeral(base_vehicle.value() + tva_vehicle.value());
    document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0,00.00$)');     
    
    document.getElementById('inputSupplyPrice').value = price_supply.format('(0,00.00$)'); 
    base_supply = numeral(price_supply.value());    
    document.getElementById('inputSupplyTotal').value = base_supply.format('(0,00.00$)');
    
    document.getElementById('inputComponentPrice').value = price_component.format('(0,00.00$)'); 
    base_component = numeral(price_component.value());    
    document.getElementById('inputComponentTotal').value = base_component.format('(0,00.00$)');
    
    document.getElementById('inputSuppliesBase').value = price_supplies.format('(0,00.00$)');
    tva_supplies = numeral(price_supplies.value() * 0.21);
    document.getElementById('inputSuppliesTva').value = tva_supplies.format('(0,00.00$)');
    total_supplies = numeral(price_supplies.value() + tva_supplies.value());
    document.getElementById('inputSuppliesTotal').value = total_supplies.format('(0,00.00$)');
    
    document.getElementById('inputComponentsBase').value = price_components.format('(0,00.00$)');
    tva_components = numeral(price_components.value() * 0.21);
    document.getElementById('inputComponentsTva').value = tva_components.format('(0,00.00$)');
    total_components = numeral(price_components.value() + tva_components.value());
    document.getElementById('inputComponentsTotal').value = total_components.format('(0,00.00$)');
    
    document.getElementById('inputWorksBase').value = price_works.format('(0,00.00$)');
    tva_works = numeral(price_works.value() * 0.21);
    document.getElementById('inputWorksTva').value = tva_works.format('(0,00.00$)');
    total_works = numeral(price_works.value() + tva_works.value());
    document.getElementById('inputWorksTotal').value = total_works.format('(0,00.00$)');
    
      
    document.getElementById('inputWorkPrice').value = price_work.format('(0,00.00$)');        
    cantity_work = numeral(document.getElementById('inputWorkCantity').value);
    total_work = numeral (price_work.value() * cantity_work.value());
    document.getElementById('inputWorkTotal').value = total_work.format('(0,00.00$)');    
       

    base = numeral(price_vehicle.value() + price_supply.value() + price_work.value() - discount.value());
    document.getElementById("inputPrice").value = base.format('(0,00.00$)');
    document.getElementById("inputDiscount").value = discount.format('(0,00.00$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0,00.00$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0,00.00$)');
    
    document.getElementById('inputVehiclePrice').addEventListener("change", function () 
    {        
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0,00.00$)');
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0,00.00$)');
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);        
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0,00.00$)');
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        total_vehicle = numeral(price_vehicle.value() - vehicle_discount.value() + tva_vehicle.value());
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0,00.00$)');
    });    
    document.getElementById('inputVehicleDiscount').addEventListener("change", function ()
    {
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0,00.00$)');
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        total_vehicle = numeral(total_vehicle.value() - vehicle_discount.value());
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0,00.00$)');
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        price_vehicle = numeral(total_vehicle.value() / 1.21);
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0,00.00$)');        
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);          
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0,00.00$)');
       
    });    
    document.getElementById('inputVehicleTotal').addEventListener("change", function ()
    {
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0,00.00$)');
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        price_vehicle = numeral(total_vehicle.value() / 1.21);         
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0,00.00$)');
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        vehicle_discount = numeral(0);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0,00.00$)');
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0,00.00$)');        
    });
    document.getElementById('inputSupplyPrice').addEventListener("change", function () 
    {        
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0,00.00$)');        
        tva_supply = numeral(document.getElementById('inputSupplyTva').value);
        tva_supply = numeral(price_supply.value() * 0.21);        
        document.getElementById('inputSupplyTva').value = tva_supply.format('(0,00.00$)');
        total_supply = numeral(document.getElementById('inputSupplyTotal').value);
        total_supply = numeral(price_supply.value() + tva_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,00.00$)');
    });
    document.getElementById('inputWorkPrice').addEventListener("change", function () 
    {        
        price_work = numeral(document.getElementById('inputWorkPrice').value);
        document.getElementById('inputWorkPrice').value = price_work.format('(0,00.00$)');        
        tva_work = numeral(document.getElementById('inputWorkTva').value);
        tva_work = numeral(price_work.value() * 0.21);        
        document.getElementById('inputWorkTva').value = tva_work.format('(0,00.00$)');
        total_work = numeral(document.getElementById('inputWorkTotal').value);
        total_work = numeral(price_work.value() + tva_work.value());
        document.getElementById('inputWorkTotal').value = total_work.format('(0,00.00$)');
    });
    document.getElementById('inputSupplyTotal').addEventListener("change", function ()
    {
        total_supply = numeral(document.getElementById('inputSupplyTotal').value);
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,00.00$)');
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        price_supply = numeral(total_supply.value() / 1.21);         
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0,00.00$)');
        supply_discount = numeral(document.getElementById('inputSupplyDiscount').value);
        supply_discount = numeral(0);
        document.getElementById('inputSupplyDiscount').value = supply_discount.format('(0,00.00$)');
        tva_supply = numeral(document.getElementById('inputSupplyTva').value);
        tva_supply = numeral((price_supply.value() - supply_discount.value()) * 0.21);
        document.getElementById('inputSupplyTva').value = tva_supply.format('(0,00.00$)');        
    });
    document.getElementById('inputWorkTotal').addEventListener("change", function ()
    {
        total_work = numeral(document.getElementById('inputWorkTotal').value);
        document.getElementById('inputWorkTotal').value = total_work.format('(0,00.00$)');
        price_work = numeral(document.getElementById('inputWorkPrice').value);
        price_work = numeral(total_work.value() / 1.21);         
        document.getElementById('inputWorkPrice').value = price_work.format('(0,00.00$)');
        work_discount = numeral(document.getElementById('inputWorkDiscount').value);
        work_discount = numeral(0);
        document.getElementById('inputWorkDiscount').value = work_discount.format('(0,00.00$)');
        tva_work = numeral(document.getElementById('inputWorkTva').value);
        tva_work = numeral((price_work.value() - work_discount.value()) * 0.21);
        document.getElementById('inputWorkTva').value = tva_work.format('(0,00.00$)');        
    });

    document.getElementById("inputPrice").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('(0,00.00$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0,00.00$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0,00.00$)');
        document.getElementById("inputPrice").value = price.format('(0,00.00$)');
    });
    document.getElementById("inputDiscount").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('(0,00.00$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0,00.00$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0,00.00$)');
        document.getElementById("inputPrice").value = price.format('(0,00.00$)');
    });
    document.getElementById("inputTotal").addEventListener("change", function()
    {
        total = numeral(document.getElementById("inputTotal").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(total.value() / 1.21);                    
        document.getElementById("inputDiscount").value = discount.format('(0,00.00$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0,00.00$)');
        price = numeral(base.value() - discount.value());  
        document.getElementById("inputTotal").value = total.format('(0,00.00$)');
        document.getElementById("inputPrice").value = price.format('(0,00.00$)');
    });
    document.getElementById('inputWorkCantity').addEventListener("change", function()
    {
        price_work = numeral(document.getElementById('inputWorkBase').value);
        cantity_work = numeral(document.getElementById('inputWorkCantity').value);
        total_work = numeral(price_work.value() * cantity_work.value());
        document.getElementById('inputWorkTotal').value = total.work.format('(0,00.00$)');
    });
    document.getElementById('inputSupplyCantity').addEventListener("change", function()
    {
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        total_supply = numeral(price_supply.value() * cantity_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0,00.00$)');
    });
    var suma_prices_supplies = 0;
    var suma_prices_components = 0;
    var json_supplies_response = '{{offerSupplies|raw}}';
    if(json_supplies_response)
    {
        offer_supplies = JSON.parse(json_supplies_response);    
        var table_supplies = document.getElementById("SuppliesTable");
        while(table_supplies.childElementCount > 0)
        {
            table_supplies.removeChild(table_supplies.firstChild);
        }
        table_supplies.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr>"

        for(let i = 0; i < offer_supplies.length ; i++)
        {            
            offer_supplies[i]['total'] = numeral(numeral(offer_supplies[i]['cantity']).value() * numeral(offer_supplies[i]['price']).value()).value();
            table_supplies.innerHTML += "<tr><td>"+offer_supplies[i]['id']+"</td><td>"+offer_supplies[i]['reference']+"</td><td>"+offer_supplies[i]['name']+"</td><td>"+offer_supplies[i]['price']+"</td><td>"+offer_supplies[i]['cantity']+"</td><td>"+offer_supplies[i]['total']+"</td><td><a href=/intranet/crm/offers/supplies/edit?supply_id="+offer_supplies[i][1]+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}&customer_id={{customer.id}}>Edit</a></td><td><a href= id=deleteAccesory"+offer_supplies[i][1]+">Eliminar</a></td>";
            suma_prices_supplies += numeral(price_supplies).value() + numeral(offer_supplies[i]['total']).value();           
        } 
        price_supplies = numeral(suma_prices_supplies);
        document.getElementById('inputSuppliesBase').value = price_supplies.format('(0,00.00$)');
        tva_supplies = numeral(price_supplies.value() * 0.21);
        document.getElementById('inputSuppliesTva').value = tva_supplies.format('(0,00.00$)');
        total_supplies = numeral(price_supplies.value() + tva_supplies.value());
        document.getElementById('inputSuppliesTotal').value = total_supplies.format('(0,00.00$)');
    }
    var json_components_response = '{{offerComponents|raw}}';
    if(json_components_response)
    {
        offer_components = JSON.parse(json_components_response);    
        var table_components = document.getElementById("ComponentsTable");
        while(table_components.childElementCount > 0)
        {
            table_components.removeChild(table_components.firstChild);
        }
        table_components.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr>"

        for(let i = 0; i < offer_components.length ; i++)
        {            
            offer_components[i]['total'] = numeral(numeral(offer_components[i]['cantity']).value() * numeral(offer_components[i]['price']).value()).value();
            table_components.innerHTML += "<tr><td>"+offer_components[i]['id']+"</td><td>"+offer_components[i]['reference']+"</td><td>"+offer_components[i]['name']+"</td><td>"+offer_components[i]['price']+"</td><td>"+offer_components[i]['cantity']+"</td><td>"+offer_components[i]['total']+"</td><td><a href=/intranet/crm/offers/components/edit?supply_id="+offer_components[i][1]+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}&customer_id={{customer.id}}>Edit</a></td><td><a href= id=deleteAccesory"+offer_components[i][1]+">Eliminar</a></td>";
            suma_prices_components = numeral(price_components).value() + numeral(offer_components[i]['total']).value();           
        }        
        price_components = numeral(suma_prices_components);
        document.getElementById('inputComponentsBase').value = price_components.format('(0,00.00$)');
        tva_components = numeral(price_components.value() * 0.21);
        document.getElementById('inputComponentsTva').value = tva_components.format('(0,00.00$)');
        total_components = numeral(price_components.value() + tva_components.value());
        document.getElementById('inputComponentsTotal').value = total_components.format('(0,00.00$)');
    }
 
});
function addSupply()
{    
    var offer = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    var ref_supply = document.getElementById('inputSupplyReference');
    var name_supply = document.getElementById('inputSupplyName');
    var price_supply = numeral(document.getElementById('inputSupplyPrice').value);
    var cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
    var total_supply = numeral(document.getElementById('inputSupplyTotal').value);
    price_supplies = numeral(document.getElementById('inputSuppliesBase').value);
    tva_supplies = numeral(document.getElementById('inputSuppliesTva').value);
    total_supplies = numeral(document.getElementById('inputSuppliesTotal').value);
    var importe = 0;
    importe = numeral(price_supply.value() * cantity_supply.value());
    
    var offer_supply = new Array();
    offer_supply = [offer.value,id_supply.value,ref_supply.value,name_supply.value,price_supply.value(),cantity_supply.value(),importe.value()];
    if(offer_supplies.length > 0)
    {
        for(let i=0;i < offer_supplies.length; i++)
        {
            if(offer_supply[1] === offer_supplies[i][1])
            {
                offer_supplies[i][5] = numeral(numeral(offer_supplies[i][5]).value() + numeral(offer_supply[5]).value()).value();
                offer_supplies[i][6] = numeral(numeral(offer_supplies[i][4]).value() * numeral(offer_supplies[i][5]).value()).value();
            }
            else
            {
               offer_supplies.push(offer_supply);
            }
        }
        
    }
    else
    {       
        offer_supplies.push(offer_supply);         
    }    
    var suma_prices_supplies = 0;    
    var table_supplies = document.getElementById("SuppliesTable");
    while(table_supplies.childElementCount > 0)
    {
        table_supplies.removeChild(table_supplies.firstChild);
    }
    table_supplies.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr> "
    if(offer_supplies.length === 0)
    {
        table_supplies.innerHTML += "<tr><td> No hay datos </td></tr>";        
    }
    else
    {
        for(let i = 0; i < offer_supplies.length ; i++)
        {            
            table_supplies.innerHTML += "<tr><td>"+offer_supplies[i][1]+"</td><td>"+offer_supplies[i][2]+"</td><td>"+offer_supplies[i][3]+"</td><td>"+offer_supplies[i][4]+"</td><td>"+offer_supplies[i][5]+"</td><td>"+offer_supplies[i][6]+"</td><td><a href=/intranet/crm/offers/supplies/edit?supply_id="+offer_supplies[i][1]+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}&customer_id={{customer.id}}>Edit</a></td><td><a href= id=deleteAccesory"+offer_supplies[i][1]+">Eliminar</a></td>";
            suma_prices_supplies = numeral(price_supplies).value() + numeral(offer_supplies[i][4]).value();           
        }  
    }       
    price_supplies = numeral(suma_prices_supplies).value();
    tva_supplies = numeral(numeral(price_supplies).value() * 0.21);
    total_supplies = numeral(price_supplies).value() + numeral(tva_supplies).value();
    document.getElementById('inputSuppliesBase').value = numeral(price_supplies).format('(0,00.00$)');
    document.getElementById('inputSuppliesTva').value = numeral(tva_supplies).format('(0,00.00$)');
    document.getElementById('inputSuppliesTotal').value = numeral(total_supplies).format('(0,00.00$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/intranet/crm/offers/supplies/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    request.send('supplies=' + offer_supplies);    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('aler');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
    
}
function addComponent()
{
    var offer = document.getElementById('inputId');
    var id_component = document.getElementById('inputComponentId');
    var ref_component = document.getElementById('inputComponentReference');
    var name_component = document.getElementById('inputComponentName');
    var price_component = numeral(document.getElementById('inputComponentPrice').value);
    var cantity_component = numeral(document.getElementById('inputComponentCantity').value);
    var total_component = numeral(document.getElementById('inputComponentTotal').value);
    price_components = numeral(document.getElementById('inputComponentsBase').value);
    tva_components = numeral(document.getElementById('inputComponentsTva').value);
    total_components = numeral(document.getElementById('inputComponentsTotal').value);
    var importe = 0;
    importe = numeral(price_component.value() * cantity_component.value());
    
    var offer_component = new Array();
    offer_component = [offer.value,id_component.value,ref_component.value,name_component.value,price_component.value(),cantity_component.value(),importe.value()];
    if(offer_components.length > 0)
    {
        for(let i=0;i < offer_components.length; i++)
        {
            if(offer_component[1] === offer_components[i][1])
            {
                offer_components[i][5] = numeral(numeral(offer_components[i][5]).value() + numeral(offer_component[5]).value()).value();
                offer_components[i][6] = numeral(numeral(offer_components[i][4]).value() * numeral(offer_components[i][5]).value()).value();
            }
            else
            {
               offer_components.push(offer_component);
            }
        }
        
    }
    else
    {       
        offer_components.push(offer_component);         
    }    
    var suma_prices_components = 0;    
    var table_components = document.getElementById("ComponentsTable");
    while(table_components.childElementCount > 0)
    {
        table_components.removeChild(table_components.firstChild);
    }
    table_components.innerHTML = "<tr><th>ID</th><th>Referencia</th><th>Nombre</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr> "
    if(offer_components.length === 0)
    {
        table_components.innerHTML += "<tr><td> No hay datos </td></tr>";        
    }
    else
    {
        for(let i = 0; i < offer_components.length ; i++)
        {            
            table_components.innerHTML += "<tr><td>"+offer_components[i][1]+"</td><td>"+offer_components[i][2]+"</td><td>"+offer_components[i][3]+"</td><td>"+offer_components[i][4]+"</td><td>"+offer_components[i][5]+"</td><td>"+offer_components[i][6]+"</td><td><a href=/intranet/crm/offers/components/edit?component_id="+offer_components[i][1]+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}&customer_id={{customer.id}}>Edit</a></td><td><a href= id=deleteAccesory"+offer_components[i][1]+">Eliminar</a></td>";
            suma_prices_components = numeral(price_components).value() + numeral(offer_components[i][4]).value();           
        }  
    }       
    price_components = numeral(suma_prices_components).value();
    tva_components = numeral(numeral(price_components).value() * 0.21);
    total_components = numeral(price_components).value() + numeral(tva_components).value();
    document.getElementById('inputComponentsBase').value = numeral(price_components).format('(0,00.00$)');
    document.getElementById('inputComponentsTva').value = numeral(tva_components).format('(0,00.00$)');
    document.getElementById('inputComponentsTotal').value = numeral(total_components).format('(0,00.00$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/intranet/crm/offers/components/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    request.send('components=' + offer_components);    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
}
