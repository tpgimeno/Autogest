/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

window.addEventListener('load', function()
{
    var home_tab = document.getElementById("home-tab");
    var home = document.getElementById("home");
    var buys_tab = document.getElementById("buys-tab");
    var buys = document.getElementById("buys");
    var sales_tab = document.getElementById("sales-tab");
    var sales = document.getElementById("sales");
    var reports_tab = document.getElementById("reports-tab");
    var reports = document.getElementById("reports")
    
    var selected_tab = "{{selected_tab}}";
    switch(selected_tab)
    {
        case 'home':
            home.classList.add("show", "active");
            home_tab.classList.add("active");
            buys.classList.remove("show", "active");
            buys_tab.classList.remove("active");
            sales.classList.remove("show", "active");
            sales_tab.classList.remove("active");
            reports.classList.remove("show", "active");
            reports_tab.classList.remove("active");
            break;
        case 'buys':
            home.classList.remove("show", "active");
            home_tab.classList.remove("active");
            buys.classList.add("show", "active");
            buys_tab.classList.add("active");
            sales.classList.remove("show", "active");
            sales_tab.classList.remove("active");
            reports.classList.remove("show", "active");
            reports_tab.classList.remove("active");
            break;
        case 'sales':
            home.classList.remove("show", "active");
            home_tab.classList.remove("active");
            buys.classList.remove("show", "active");
            buys_tab.classList.remove("active");
            sales.classList.add("show", "active");
            sales_tab.classList.add("active");
            reports.classList.remove("show", "active");
            reports_tab.classList.remove("active");
            break;
        case 'reports':
            home.classList.remove("show", "active");
            home_tab.classList.remove("active");
            buys.classList.remove("show", "active");
            buys_tab.classList.remove("active");
            sales.classList.remove("show", "active");
            sales_tab.classList.remove("active");
            reports.classList.add("show", "active");
            reports_tab.classList.add("active");
            break;
    }    
});
