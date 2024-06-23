/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


/*
 * =============================================================================
 * Work Sheets Functions
 * =============================================================================
 */

function get_new_expertOpinionNumber(){
     $.ajax({
        method: "POST",
        url: "Intranet/garages/expertOpinion/number/get",
        data: {},
        dataType: "json",
        success: function(data){            
            $('#opinionId').val(data);
        }
    });
}

function saveExpertOpinionsComponent(){
    var url = "Intranet/garages/expertOpinion/components/add";   
    var data = {'expertOpinion_id' :  $('.form-horizontal #id').val(), 
            'component_id' : $('#expertOpinions_component_form #component_id').val(),
            'pvp' : $('#expertOpinions_component_form #pvp').val(),
            'cantity' : $('#expertOpinions_component_form #cantity').val()};    
    saveAssets(url, data, '#components_modal', '#component_form_modal','components', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
   
}

function delExpertOpinionsComponent(data){     
    var url = "Intranet/garages/expertOpinion/components/del";
    var setData = {'id' : data.expertOpinionComponent_id};    
    delAsset(url, setData, 'components', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}


function saveExpertOpinionsSupply(){  
    var url = "Intranet/garages/expertOpinion/supplies/add";
    var data = {'expertOpinion_id' : $('.form-horizontal #id').val(), 
            'supply_id' : $('#expertOpinions_supply_form #supply_id').val(),
            'pvp' : $('#expertOpinions_supply_form #pvp').val(),
            'cantity' : $('#expertOpinions_supply_form #cantity').val()};
    
    saveAssets(url, data, '#supplies_modal', '#supply_form_modal', 'supplies', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
   
}

function delExpertOpinionsSupply(data){    
    var url = "Intranet/garages/expertOpinion/supplies/del";
    var setData = {'id' : data.expertOpinionSupply_id};
    delAsset(url, setData, 'supplies', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function saveExpertOpinionsWork(){ 
    var url = "Intranet/garages/expertOpinion/works/add";
    
    var data = {'expertOpinion_id' : $('.form-horizontal #id').val(), 
            'work_id' : $('#expertOpinions_work_form #work_id').val(),
            'pvp' : $('#expertOpinions_work_form #pvp').val(),
            'cantity' : $('#expertOpinions_work_form #cantity').val()};
    saveAssets(url, data, '#works_modal', '#work_form_modal', 'works', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function delExpertOpinionsWork(data){  
    var url = "Intranet/garages/expertOpinion/works/del";
    var data = {'id' : data.expertOpinionWork_id};
    delAsset(url, data, 'works', 'Intranet/garages/expertOpinion/form?id=' + $('.form-horizontal #id').val() + '&menu=taller&item=expertOpinions');
    
}

function set_worksheet_prices(){  
    
    var discount = numeral($('#discountExpertOpinion').val());      
    var baseComponents = numeral($('#baseComponents').val());    
    var baseSupplies = numeral($('#baseSupplies').val());
    var baseWorks = numeral($('#baseWorks').val());
    var sum_bases = numeral(baseComponents.value() + baseSupplies.value() + baseWorks.value() - discount.value());
    $('#formPeritacion #baseExpertOpinion').val(sum_bases.format('(0.0,$)'));
    $('#formPeritacion #tvaExpertOpinion').val(numeral(sum_bases.value() * 0.21).format('(0.0,$)'));
    var tva = numeral($('#tvaExpertOpinion').val());
    $('#formPeritacion #totalExpertOpinion').val(numeral(sum_bases.value() + tva.value()).format('(0.0,$)'));    
}