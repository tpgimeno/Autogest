/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

window.addEventListener('load', function()
{        
    
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
            thousands: '.',
            decimal: ','
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

    var discount = numeral(document.getElementById("inputDiscount").value);                
    var tva = numeral(document.getElementById("inputTva").value);
    var total = numeral(document.getElementById("inputTotal").value);
    var price = numeral(document.getElementById("inputPrice").value);                
    var base = 0;                                

    base = numeral(price.value() - discount.value());
    document.getElementById("inputPrice").value = price.format('0,00.0$');
    document.getElementById("inputDiscount").value = discount.format('0,00.0$');
    tva = numeral(base.value() * 0.21);
    document.getElementById("inputTva").value = tva.format('0,00.0$');
    total = numeral(base.value() + tva.value());  
    document.getElementById("inputTotal").value = total.format('0,00.0$');

    document.getElementById("inputPrice").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('0,00.00$');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('0,00.00$');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('0,00.00$');
        document.getElementById("inputPrice").value = price.format('0,00.00$');
    });
    document.getElementById("inputDiscount").addEventListener("change", function()
    {
        price = numeral(document.getElementById("inputPrice").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(price.value() - discount.value());                    
        document.getElementById("inputDiscount").value = discount.format('0,00.0$');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('0,00.0$');
        total = numeral(base.value() + tva.value());  
        document.getElementById("inputTotal").value = total.format('0,00.0$');
        document.getElementById("inputPrice").value = price.format('0,00.0$');
    });
    document.getElementById("inputTotal").addEventListener("change", function()
    {
        total = numeral(document.getElementById("inputTotal").value);
        discount = numeral(document.getElementById("inputDiscount").value);
        base = numeral(total.value() / 1.21);                    
        document.getElementById("inputDiscount").value = discount.format('0,00.0$');
        tva = numeral(base.value() * 0.21);
        document.getElementById("inputTva").value = tva.format('0,00.0$');
        price = numeral(base.value() - discount.value());  
        document.getElementById("inputTotal").value = total.format('0,00.0$');
        document.getElementById("inputPrice").value = price.format('0,00.0$');
    });

});
