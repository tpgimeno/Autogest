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
var price_components = null;
var tva_components = null;
var total_components = null;
var price_works = null;
var tva_works = null;
var total_works = null;
var price_vehicle = null;
var price = null;
var tva = null;
var discount = null;
var base = null;
var total = null;

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
        request.open('POST', '/crm/offers/customer/search', true);
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
                       table.innerHTML += "<tr><td>"+response[i]['name']+"</td><td>"+response[i]['city']+"</td><td>"+response[i]['fiscal_id']+"</td><td>"+response[i]['address']+"</td><td><a href=/crm/offers/customer/select?customer_id="+response[i]['id']+"&offer_id={{offer.id}}&vehicle_id={{vehicle.id}}>Select</a></td></tr>";
                   }                               
                }                        
            }
            request.addEventListener("load", reqListener);
        }                   
    });
    document.getElementById("searchVehicleField").addEventListener("keyup", function()
    {                      
        var request = new XMLHttpRequest();
        request.open('POST', '/crm/offers/vehicle/search', true);
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
                       table.innerHTML += "<tr><td>"+response[i]['plate']+"</td><td>"+response[i]['vin']+"</td><td>"+response[i]['brand']+"</td><td>"+response[i]['model']+"</td><td><a href=/crm/offers/customer/select?customer_id="+response[i]['id']+"&offer_id={{offer.id}}&customer_id={{customer.id}}>Select</a></td></tr>";
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

    var offer = document.getElementById('inputId');
    
    price = numeral(document.getElementById("inputPrice").value); 
    discount = numeral(document.getElementById("inputDiscount").value);
    base = numeral(0);
    tva = numeral(document.getElementById("inputTva").value);
    total = numeral(document.getElementById("inputTotal").value);
           
    price_vehicle = numeral(document.getElementById('inputVehiclePrice').value); 
    var vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
    var base_vehicle = numeral(price_vehicle.value() - vehicle_discount.value());
    var tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);  
    var total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
    
    var price_supply = numeral(document.getElementById('inputSupplyPrice').value);
    var base_supply = numeral(0);
    var cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
    var total_supply = numeral(document.getElementById('inputSupplyTotal').value);
    
    var price_component = numeral(document.getElementById('inputComponentPrice').value);
    var base_component = numeral(0); 
    var cantity_component = numeral(document.getElementById('inputComponentCantity').value);
    var total_component = numeral(document.getElementById('inputComponentTotal').value);
    
    price_supplies = numeral(document.getElementById('inputSuppliesBase').value);
    tva_supplies = numeral(document.getElementById('inputSuppliesTva').value);
    total_supplies = numeral(document.getElementById('inputSuppliesTotal').value);
    
    price_components = numeral(document.getElementById('inputComponentsBase').value);
    tva_components = numeral(document.getElementById('inputComponentsTva').value);
    total_components = numeral(document.getElementById('inputComponentsTotal').value);
    
    price_works = numeral(document.getElementById('inputWorksBase').value);
    tva_works = numeral(document.getElementById('inputWorksTva').value);
    total_works = numeral(document.getElementById('inputWorksTotal').value);
    
    var price_work = numeral(document.getElementById('inputWorkPrice').value);
    var cantity_work = numeral(document.getElementById('inputWorkCantity').value);    
    var total_work = numeral(document.getElementById('inputWorkTotal').value);
    
    document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0.0,$)');    
    document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0.0,$)');  
    base_vehicle = numeral(price_vehicle.value() - vehicle_discount.value());
    tva_vehicle = numeral(base_vehicle.value() * 0.21);   
    document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0.0,$)');    
    total_vehicle = numeral(base_vehicle.value() + tva_vehicle.value());
    document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0.0,$)');     
    
    document.getElementById('inputSupplyPrice').value = price_supply.format('(0.0,$)');    
    base_supply = numeral(price_supply.value() * cantity_supply.value());    
    document.getElementById('inputSupplyTotal').value = base_supply.format('(0.0,$)');
    
    document.getElementById('inputComponentPrice').value = price_component.format('(0.0,$)'); 
    base_component = numeral(price_component.value());    
    document.getElementById('inputComponentTotal').value = base_component.format('(0.0,$)');
    
    document.getElementById('inputSuppliesBase').value = price_supplies.format('(0.0,$)');
    tva_supplies = numeral(price_supplies.value() * 0.21);
    document.getElementById('inputSuppliesTva').value = tva_supplies.format('(0.0,$)');
    total_supplies = numeral(price_supplies.value() + tva_supplies.value());
    document.getElementById('inputSuppliesTotal').value = total_supplies.format('(0.0,$)');
    
    document.getElementById('inputComponentsBase').value = price_components.format('(0.0,$)');
    tva_components = numeral(price_components.value() * 0.21);
    document.getElementById('inputComponentsTva').value = tva_components.format('(0.0,$)');
    total_components = numeral(price_components.value() + tva_components.value());
    document.getElementById('inputComponentsTotal').value = total_components.format('(0.0,$)');
    
    document.getElementById('inputWorksBase').value = price_works.format('(0.0,$)');
    tva_works = numeral(price_works.value() * 0.21);
    document.getElementById('inputWorksTva').value = tva_works.format('(0.0,$)');
    total_works = numeral(price_works.value() + tva_works.value());
    document.getElementById('inputWorksTotal').value = total_works.format('(0.0,$)');
    
      
    document.getElementById('inputWorkPrice').value = price_work.format('(0.0,$)');        
    cantity_work = numeral(document.getElementById('inputWorkCantity').value);
    total_work = numeral (price_work.value() * cantity_work.value());
    document.getElementById('inputWorkTotal').value = total_work.format('(0.0,$)');    
       

    base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
    console.log(base.value());
    document.getElementById("inputPrice").value = base.format('(0.0,$)');
    document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0.0,$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0.0,$)');
    
    document.getElementById('inputVehiclePrice').addEventListener("change", function () 
    {        
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0.0,$)');
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0.0,$)');
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);        
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0.0,$)');
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        total_vehicle = numeral(price_vehicle.value() - vehicle_discount.value() + tva_vehicle.value());
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0.0,$)');
    });    
    document.getElementById('inputVehicleDiscount').addEventListener("change", function ()
    {
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0.0,$)');
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        total_vehicle = numeral(total_vehicle.value() - vehicle_discount.value());
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0.0,$)');
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        price_vehicle = numeral(total_vehicle.value() / 1.21);
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0.0,$)');        
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);          
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0.0,$)');
       
    });    
    document.getElementById('inputVehicleTotal').addEventListener("change", function ()
    {
        total_vehicle = numeral(document.getElementById('inputVehicleTotal').value);
        document.getElementById('inputVehicleTotal').value = total_vehicle.format('(0.0,$)');
        price_vehicle = numeral(document.getElementById('inputVehiclePrice').value);
        price_vehicle = numeral(total_vehicle.value() / 1.21);         
        document.getElementById('inputVehiclePrice').value = price_vehicle.format('(0.0,$)');
        vehicle_discount = numeral(document.getElementById('inputVehicleDiscount').value);
        vehicle_discount = numeral(0);
        document.getElementById('inputVehicleDiscount').value = vehicle_discount.format('(0.0,$)');
        tva_vehicle = numeral(document.getElementById('inputVehicleTva').value);
        tva_vehicle = numeral((price_vehicle.value() - vehicle_discount.value()) * 0.21);
        document.getElementById('inputVehicleTva').value = tva_vehicle.format('(0.0,$)');        
    });
    document.getElementById('inputSupplyPrice').addEventListener("change", function () 
    {        
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0.0,$)');        
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);        
        total_supply = numeral(price_supply.value() * cantity_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0.0,$)');
    });
    document.getElementById('inputSupplyCantity').addEventListener("change", function()
    {
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        total_supply = numeral(price_supply.value() * cantity_supply.value());
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0.0,$)');
    });
    document.getElementById('inputSupplyTotal').addEventListener("change", function ()
    {
        total_supply = numeral(document.getElementById('inputSupplyTotal').value);
        document.getElementById('inputSupplyTotal').value = total_supply.format('(0.0,$)');
        price_supply = numeral(document.getElementById('inputSupplyPrice').value);
        cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);
        price_supply = numeral(total_supply.value() / cantity_supply.value());
        document.getElementById('inputSupplyPrice').value = price_supply.format('(0.0,$)');             
    });
    document.getElementById('inputComponentPrice').addEventListener("change", function()
    {
        price_component = numeral(document.getElementById('inputComponentPrice').value);
        document.getElementById('inputComponentPrice').value = price_component.format('(0.0,$)');
        cantity_component = numeral(document.getElementById('inputComponentCantity').value);
        base_component = numeral(price_component.value() * cantity_component.value());
        total_component.value  = numeral(base_component.value()).format('(0.0,$)');
    });
    document.getElementById('inputComponentCantity').addEventListener("change", function()
    {
        price_component = numeral(document.getElementById('inputComponentPrice').value);
        document.getElementById('inputComponentPrice').value = price_component.format('(0.0,$)');
        cantity_component = numeral(document.getElementById('inputComponentCantity').value);
        base_component = numeral(price_component.value() * cantity_component.value());
        total_component.value  = numeral(base_component.value()).format('(0.0,$)');
    });
    document.getElementById('inputComponentTotal').addEventListener("change", function()
    {
        total_component = numeral(document.getElementById('inputComponentTotal').value);
        document.getElementById('inputComponentTotal').value = total_component.format('(0.0,$)');
        cantity_component = numeral(document.getElementById('inputComponentCantity').value);
        price_component = numeral(total_component.value() / cantity_component.value());
        document.getElementById('inputComponentPrice').value = price_component.format('(0.0,$)');        
    });
    document.getElementById('inputComponentsBase').addEventListener('change', function()
    {
        base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
        document.getElementById("inputPrice").value = base.format('(0.0,$)');
        price_components = numeral(document.getElementById('inputComponentsBase').value);
        document.getElementById('inputComponentsBase').value = price_components.format('(0.0,$)'); 
        tva_components = numeral(price_components.value() * 0.21);
        tva_components = numeral(document.getElementById('inputComponentsTva').value);
        total_components = numeral(price_components.value() + tva_components.value());
        document.getElementById('inputComponentsTotal').value = total_components.format('(0.0,$)');
    });    
    document.getElementById('inputWorkPrice').addEventListener("change", function () 
    {        
        price_work = numeral(document.getElementById('inputWorkPrice').value);
        document.getElementById('inputWorkPrice').value = price_work.format('(0.0,$)');        
        cantity_work = numeral(document.getElementById('inputWorkCantity').value);        
        total_work = numeral(price_work.value() * cantity_work.value());
        document.getElementById('inputWorkTotal').value = total_work.format('(0.0,$)');
    });
    
    document.getElementById('inputWorkTotal').addEventListener("change", function ()
    {
        total_work = numeral(document.getElementById('inputWorkTotal').value);
        document.getElementById('inputWorkTotal').value = total_work.format('(0.0,$)');
        cantity_work = numeral(document.getElementById('inputWorkCantity').value);
        price_work = numeral(total_work.value() / cantity_work.value());
        document.getElementById('inputWorkPrice').value = price_work.format('(0.0,$)');                
    });

    document.getElementById("inputPrice").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
        document.getElementById("inputPrice").value = price.format('(0.0,$)');
    });
    document.getElementById("inputDiscount").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
        document.getElementById("inputPrice").value = price.format('(0.0,$)');
    });
    document.getElementById("inputTotal").addEventListener("change", function()
    {
        total = numeral(document.getElementById("inputTotal").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(total.value() / 1.21);                    
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        price = numeral(base.value() - discount.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
        document.getElementById("inputPrice").value = price.format('(0.0,$)');
    });
    document.getElementById('inputWorkCantity').addEventListener("change", function()
    {
        price_work = numeral(document.getElementById('inputWorkPrice').value);
        cantity_work = numeral(document.getElementById('inputWorkCantity').value);
        total_work = numeral(price_work.value() * cantity_work.value());
        document.getElementById('inputWorkTotal').value = total_work.format('(0.0,$)');        
    });   
    document.getElementById('inputWorksBase').addEventListener('change', function()
    {
        base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
        document.getElementById("inputPrice").value = base.format('(0.0,$)');
    });
    
    var suma_prices_supplies = 0;
    var suma_prices_components = 0; 
    var suma_prices_works = 0;
    var json_supplies_response = '{{offerSupplies|raw}}';
    var offer = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    var id_vehicle = document.getElementById('inputVehicleId');
    var id_customer = document.getElementById('inputCustomerId');
    var id_component = document.getElementById('inputComponentId');
    var id_work = document.getElementById('inputWorkId');
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
            table_supplies.innerHTML += "<tr><td>"+offer_supplies[i]['supply_id']+"</td><td>"+offer_supplies[i]['reference']+"</td><td>"+offer_supplies[i]['name']+"</td><td>"+offer_supplies[i]['price']+"</td><td>"+offer_supplies[i]['cantity']+"</td><td>"+offer_supplies[i]['total']+"</td><td><a href=/crm/offers/supplies/edit?supply_id="+offer_supplies[i]['supply_id']+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&component_id="+numeral(id_component.value).value()+"&work_id="+numeral(id_work.value).value()+"&customer_id="+numeral(id_customer.value).value()+">Edit</a></td><td><a href=/crm/offers/supplies/del?supply_id="+offer_supplies[i]['supply_id']+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&component_id="+numeral(id_component.value).value()+"&work_id="+numeral(id_work.value).value()+"&customer_id="+numeral(id_customer.value).value()+">Eliminar</a></td>";
            suma_prices_supplies += numeral(price_supplies).value() + numeral(offer_supplies[i]['total']).value();           
        } 
        price_supplies = numeral(suma_prices_supplies);
        document.getElementById('inputSuppliesBase').value = price_supplies.format('(0.0,$)');
        tva_supplies = numeral(price_supplies.value() * 0.21);
        document.getElementById('inputSuppliesTva').value = tva_supplies.format('(0.0,$)');
        total_supplies = numeral(price_supplies.value() + tva_supplies.value());
        document.getElementById('inputSuppliesTotal').value = total_supplies.format('(0.0,$)');
        base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
        document.getElementById("inputPrice").value = base.format('(0.0,$)');
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
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
            table_components.innerHTML += "<tr><td>"+offer_components[i]['component_id']+"</td><td>"+offer_components[i]['reference']+"</td><td>"+offer_components[i]['name']+"</td><td>"+numeral(offer_components[i]['price']).format('(0.0,$)')+"</td><td>"+offer_components[i]['cantity']+"</td><td>"+numeral(offer_components[i]['total']).format('(0.0,$)')+"</td><td><a href=/crm/offers/components/edit?component_id="+numeral(offer_components[i]['component_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&work_id="+numeral(id_work.value).value()+">Edit</a></td><td><a href=/crm/offers/components/del?component_id="+numeral(offer_components[i]['component_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&work_id="+numeral(id_work.value).value()+">Eliminar</a></td>";
            suma_prices_components += numeral(price_components).value() + numeral(offer_components[i]['total']).value();           
        }        
        price_components = numeral(suma_prices_components);        
        document.getElementById('inputComponentsBase').value = price_components.format('(0.0,$)');
        tva_components = numeral(price_components.value() * 0.21);
        document.getElementById('inputComponentsTva').value = tva_components.format('(0.0,$)');
        total_components = numeral(price_components.value() + tva_components.value());
        document.getElementById('inputComponentsTotal').value = total_components.format('(0.0,$)');
        base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
        document.getElementById("inputPrice").value = base.format('(0.0,$)');
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
    }
    var json_works_response = '{{offerWorks|raw}}';     
    if(json_works_response.length > 0)
    {
        offer_works = JSON.parse(json_works_response);        
        var table_works = document.getElementById("WorksTable");
        var suma_prices_works = 0;
        while(table_works.childElementCount > 0)
        {
            table_works.removeChild(table_works.firstChild);
        }
        table_works.innerHTML = "<tr><th>ID</th><th>Descripcion</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr>"
        
        for(let i = 0; i < offer_works.length ; i++)
        {            
            offer_works[i]['total'] = numeral(numeral(offer_works[i]['cantity']).value() * numeral(offer_works[i]['price']).value()).value();
            table_works.innerHTML += "<tr><td>"+offer_works[i]['work_id']+"</td><td>"+offer_works[i]['description']+"</td><td>"+numeral(offer_works[i]['price']).format('(0.0,$)')+"</td><td>"+offer_works[i]['cantity']+"</td><td>"+numeral(offer_works[i]['total']).format('(0.0,$)')+"</td><td><a href=/crm/offers/works/edit?work_id="+numeral(offer_works[i]['work_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&component_id="+numeral(id_component.value).value()+">Edit</a></td><td><a href=/crm/offers/works/del?work_id="+numeral(offer_works[i]['work_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&component_id="+numeral(id_component.value).value()+">Eliminar</a></td>";
            suma_prices_works += numeral(numeral(price_works).value() + offer_works[i]['total']).value();           
        }        
        price_works = numeral(suma_prices_works);        
        document.getElementById('inputWorksBase').value = price_works.format('(0.0,$)');
        tva_works = numeral(price_works.value() * 0.21);
        document.getElementById('inputWorksTva').value = tva_works.format('(0.0,$)');
        total_works = numeral(price_works.value() + tva_works.value());
        document.getElementById('inputWorksTotal').value = total_works.format('(0.0,$)');
        base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
        document.getElementById("inputPrice").value = base.format('(0.0,$)');
        document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('(0.0,$)');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('(0.0,$)');
    }
    discount = numeral(document.getElementById('inputDiscount').value);
    base = numeral(price_vehicle.value() + price_supplies.value() + price_works.value() + price_components.value() - discount.value());
    document.getElementById("inputPrice").value = base.format('(0.0,$)');                            
    document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0.0,$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0.0,$)');
    
});
function addSupply()
{    
    var offer = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    var ref_supply = document.getElementById('inputSupplyReference');
    var name_supply = document.getElementById('inputSupplyName');
    var id_vehicle = document.getElementById('inputVehicleId');
    var id_customer = document.getElementById('inputCustomerId');
    var id_component = document.getElementById('inputComponentId');
    var id_work = document.getElementById('inputWorkId');
    var price_supply = numeral(document.getElementById('inputSupplyPrice').value);
    var cantity_supply = numeral(document.getElementById('inputSupplyCantity').value);    
    price_supplies = numeral(document.getElementById('inputSuppliesBase').value);
    tva_supplies = numeral(document.getElementById('inputSuppliesTva').value);
    total_supplies = numeral(document.getElementById('inputSuppliesTotal').value);
    var importe = 0;
    importe = numeral(price_supply.value() * cantity_supply.value());    
    var offer_supply = new Array(); 
    offer_supply = {'selloffer_id':numeral(offer.value).value(),'supply_id':numeral(id_supply.value).value(),'reference':ref_supply.value,'name':name_supply.value,'price':price_supply.value(),'cantity':cantity_supply.value(),'total':importe.value()};
    
    var exists = false;
    if(offer_supplies.length > 0)
    {        
        for(let i=0;i < offer_supplies.length; i++)
        {
            if(offer_supply['supply_id'] === offer_supplies[i]['supply_id'])
            {                
                offer_supplies[i]['cantity'] = numeral(numeral(offer_supplies[i]['cantity']).value() + numeral(offer_supply['cantity']).value()).value();
                offer_supplies[i]['total'] = numeral(numeral(offer_supplies[i]['price']).value() * numeral(offer_supplies[i]['cantity']).value()).value();
                exists = true;
            }            
        }
        if(exists === false)
        {
            offer_supplies.push(offer_supply);
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
            table_supplies.innerHTML += "<tr><td>"+offer_supplies[i]['supply_id']+"</td><td>"+offer_supplies[i]['reference']+"</td><td>"+offer_supplies[i]['name']+"</td><td>"+offer_supplies[i]['price']+"</td><td>"+offer_supplies[i]['cantity']+"</td><td>"+offer_supplies[i]['total']+"</td><td><a href=/crm/offers/supplies/edit?supply_id="+offer_supplies[i]['supply_id']+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&component_id="+numeral(id_component.value).value()+"&work_id="+numeral(id_work.value).value()+"&customer_id="+numeral(id_customer.value).value()+">Edit</a></td><td><a href=/crm/offers/supplies/del?supply_id="+offer_supplies[i]['supply_id']+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&component_id="+numeral(id_component.value).value()+"&work_id="+numeral(id_work.value).value()+"&customer_id="+numeral(id_customer.value).value()+">Eliminar</a></td>";
            suma_prices_supplies += numeral(offer_supplies[i]['total']).value();           
        }  
    }       
    price_supplies = numeral(suma_prices_supplies).value();
    tva_supplies = numeral(numeral(price_supplies).value() * 0.21);
    total_supplies = numeral(price_supplies).value() + numeral(tva_supplies).value();
    document.getElementById('inputSuppliesBase').value = numeral(price_supplies).format('(0.0,$)');
    document.getElementById('inputSuppliesTva').value = numeral(tva_supplies).format('(0.0,$)');
    document.getElementById('inputSuppliesTotal').value = numeral(total_supplies).format('(0.0,$)');
    discount = numeral(document.getElementById('inputDiscount').value);
    base = numeral(price_vehicle.value() + numeral(price_supplies).value() + numeral(price_works).value() + numeral(price_components).value() - discount.value());
    document.getElementById("inputPrice").value = base.format('(0.0,$)');                            
    document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0.0,$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0.0,$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/crm/offers/supplies/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
    console.log(offer_supply);
    request.send('supply=' + JSON.stringify(offer_supply));    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
    
}
function addComponent()
{
    var offer = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    var id_component = document.getElementById('inputComponentId');
    var id_vehicle = document.getElementById('inputVehicleId');
    var id_customer = document.getElementById('inputCustomerId');    
    var id_work = document.getElementById('inputWorkId');
    var ref_component = document.getElementById('inputComponentReference');
    var name_component = document.getElementById('inputComponentName');
    var price_component = numeral(document.getElementById('inputComponentPrice').value);
    var cantity_component = numeral(document.getElementById('inputComponentCantity').value);    
    price_components = numeral(document.getElementById('inputComponentsBase').value);
    tva_components = numeral(document.getElementById('inputComponentsTva').value);
    total_components = numeral(document.getElementById('inputComponentsTotal').value);
    var importe = 0;
    importe = numeral(price_component.value() * cantity_component.value());    
    var offer_component = new Array();
    offer_component = {'selloffer_id':numeral(offer.value).value(),'component_id':numeral(id_component.value).value(),'reference':ref_component.value,'name':name_component.value,'price':price_component.value(),'cantity':cantity_component.value(),'total':importe.value()};
    if(offer_components.length > 0)
    {
        for(let i=0;i < offer_components.length; i++)
        {
            if(offer_component['component_id'] === offer_components[i]['component_id'])
            {
                offer_components[i]['cantity'] = numeral(numeral(offer_components[i]['cantity']).value() + numeral(offer_component['cantity']).value()).value();
                offer_components[i]['total'] = numeral(numeral(offer_components[i]['price']).value() * numeral(offer_components[i]['cantity']).value()).value();
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
            table_components.innerHTML += "<tr><td>"+offer_components[i]['component_id']+"</td><td>"+offer_components[i]['reference']+"</td><td>"+offer_components[i]['name']+"</td><td>"+numeral(offer_components[i]['price']).format('(0.0,$)')+"</td><td>"+offer_components[i]['cantity']+"</td><td>"+numeral(offer_components[i]['total']).format('(0.0,$)')+"</td><td><a href=/crm/offers/components/edit?component_id="+numeral(offer_components[i]['component_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&work_id="+numeral(id_work.value).value()+">Edit</a></td><td><a href=/crm/offers/components/del?component_id="+numeral(offer_components[i]['component_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&work_id="+numeral(id_work.value).value()+">Eliminar</a></td>";
            suma_prices_components += numeral(price_components).value() + numeral(offer_components[i]['total']).value();           
        }  
    }       
    price_components = numeral(suma_prices_components).value();    
    tva_components = numeral(numeral(price_components).value() * 0.21);
    total_components = numeral(price_components).value() + numeral(tva_components).value();
    document.getElementById('inputComponentsBase').value = numeral(price_components).format('(0.0,$)');
    document.getElementById('inputComponentsTva').value = numeral(tva_components).format('(0.0,$)');
    document.getElementById('inputComponentsTotal').value = numeral(total_components).format('(0.0,$)');
    discount = numeral(document.getElementById('inputDiscount').value);
    base = numeral(price_vehicle.value() + numeral(price_supplies).value() + numeral(price_works).value() + numeral(price_components).value() - discount.value());
    document.getElementById("inputPrice").value = base.format('(0.0,$)');                            
    document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0.0,$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0.0,$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/crm/offers/components/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    request.send('component=' + JSON.stringify(offer_component));    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
}
function addWork()
{
    var offer = document.getElementById('inputId');
    var id_supply = document.getElementById('inputSupplyId');
    var id_component = document.getElementById('inputComponentId');
    var id_vehicle = document.getElementById('inputVehicleId');
    var id_customer = document.getElementById('inputCustomerId');    
    var id_work = document.getElementById('inputWorkId');
    var name_work = document.getElementById('inputWorkDescription');
    var price_work = numeral(document.getElementById('inputWorkPrice').value);
    var cantity_work = numeral(document.getElementById('inputWorkCantity').value);    
    price_works = numeral(document.getElementById('inputWorksBase').value);
    tva_works = numeral(document.getElementById('inputWorksTva').value);
    total_works = numeral(document.getElementById('inputWorksTotal').value);
    var importe = 0;
    importe = numeral(price_work.value() * cantity_work.value());    
    var offer_work = new Array();    
    offer_work = {'selloffer_id':numeral(offer.value).value(),'work_id':numeral(id_work.value).value(),'description': name_work.value,'price': price_work.value(),'cantity':cantity_work.value(),'total':importe.value()};
    
    if(offer_works.length > 0)
    {
        for(let i=0;i < offer_works.length; i++)
        {
            if(offer_work['id'] === offer_works[i]['id'])
            {
                offer_works[i]['cantity'] = numeral(numeral(offer_works[i]['cantity']).value() + numeral(offer_work['cantity']).value()).value();
                offer_works[i]['total'] = numeral(numeral(offer_works[i]['price']).value() * numeral(offer_works[i]['cantity']).value()).value();
            }
            else
            {
               offer_works.push(offer_work);
            }
        }       
    }
    else
    {       
        offer_works.push(offer_work);         
    }    
    var suma_prices_works = 0;    
    var table_works = document.getElementById("WorksTable");
    while(table_works.childElementCount > 0)
    {
        table_works.removeChild(table_works.firstChild);
    }
    table_works.innerHTML = "<tr><th>ID</th><th>Descripcion</th><th>Precio</th><th>Cantidad</th><th>Importe</th><th>Editar</th><th>Eliminar</th></tr> "
    if(offer_works.length === 0)
    {
        table_works.innerHTML += "<tr><td> No hay datos </td></tr>";        
    }
    else
    {
        for(let i = 0; i < offer_works.length ; i++)
        {            
            table_works.innerHTML += "<tr><td>"+offer_works[i]['work_id']+"</td><td>"+offer_works[i]['description']+"</td><td>"+numeral(offer_works[i]['price']).format('(0.0,$)')+"</td><td>"+offer_works[i]['cantity']+"</td><td>"+numeral(offer_works[i]['total']).format('(0.0,$)')+"</td><td><a href=/crm/offers/works/edit?work_id="+offer_works[i]['work_id']+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&component_id="+numeral(id_component.value).value()+">Edit</a></td><td><a href=/crm/offers/works/del?work_id="+numeral(offer_works[i]['work_id']).value()+"&offer_id="+numeral(offer.value).value()+"&vehicle_id="+numeral(id_vehicle.value).value()+"&customer_id="+numeral(id_customer.value).value()+"&supply_id="+numeral(id_supply.value).value()+"&component_id="+numeral(id_component.value).value()+">Eliminar</a></td>";
            suma_prices_works += numeral(numeral(price_works).value() + offer_works[i]['total']).value();           
        }  
    }       
    price_works = numeral(suma_prices_works).value();
    tva_works = numeral(numeral(price_works).value() * 0.21);
    total_works = numeral(price_works).value() + numeral(tva_works).value();
    document.getElementById('inputWorksBase').value = numeral(price_works).format('(0.0,$)');
    document.getElementById('inputWorksTva').value = numeral(tva_works).format('(0.0,$)');
    document.getElementById('inputWorksTotal').value = numeral(total_works).format('(0.0,$)');
    discount = numeral(document.getElementById('inputDiscount').value);
    base = numeral(price_vehicle.value() + price_supplies.value() + numeral(price_works).value() + numeral(price_components).value() - discount.value());
    document.getElementById("inputPrice").value = base.format('(0.0,$)');                            
    document.getElementById("inputDiscount").value = discount.format('(0.0,$)');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('(0.0,$)');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('(0.0,$)');
    var request = new XMLHttpRequest();    
    request.open('POST', '/crm/offers/works/add', true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    request.send('work=' + JSON.stringify(offer_work));    
    function reqListener()
    {
        var response = request.responseText;
        var alert = document.getElementById('alert');
        alert.innerHTML = response;
    }
    request.addEventListener("load", reqListener);
}
function renderSuppliesTable()
{
    
}