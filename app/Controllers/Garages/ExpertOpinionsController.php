<?php

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\ExpertOpinions;
use App\Models\ModelVh;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\Works;
use App\Services\Garages\ExpertOpinionsService;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/**
 * Description of ExpertOpinions
 *
 * @author tonyl
 */
class ExpertOpinionsController extends BaseController{
    protected $expertOpinionsService;
    
    public function __construct(ExpertOpinionsService $expertOpinionsService) {
        parent::__construct();
        $this->expertOpinionsService = $expertOpinionsService;
        $this->model = new ExpertOpinions();
        $this->route = 'expertOpinions';
        $this->titleList = 'Peritaciones';
        $this->titleForm = 'Peritacion';
        $this->labels = $this->expertOpinionsService->getLabelsArray(); 
        $this->itemsList = array('id', 'expertName', 'plate', 'date');
        $this->properties = $this->expertOpinionsService->getModelProperties($this->model);
    }     
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }      
    
    public function getExpertOpinionsDataAction($request) {                
        $responseMessage = null;
        $iterables = ['vehicles' => $this->expertOpinionsService->getAllRegisters(new Vehicle()),
            'brands' => $this->expertOpinionsService->getAllRegisters(new Brand()),
            'models' => $this->expertOpinionsService->getAllRegisters(new ModelVh()),
            'components' => $this->expertOpinionsService->getAllRegisters(new Components()),
            'supplies' => $this->expertOpinionsService->getAllRegisters(new Supplies()),
            'works' => $this->expertOpinionsService->getAllRegisters(new Works()),
            'vehicle_component_labels' => ['expertOpinioncomponent_id' => 'expertOpinioncomponent_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_supply_labels' => ['expertOpinionsupply_id' => 'expertOpinionsupply_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_work_labels' => ['expertOpinionwork_id' => 'expertOpinionwork_id','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'component_functions' => ['set' => 'setComponent', 'delete' => 'delExpertOpinionsComponent'],
            'supply_functions' => ['set' => 'setSupply', 'delete' => 'delExpertOpinionsSupply'],
            'work_functions' => ['set' => 'setWork', 'delete' => 'delExpertOpinionsWork'],
            'assets_prices' => ['1' => 'baseComponents','2' => 'Base Componentes','3' => 'tvaComponents','4' => 'Iva','5' => 'totalComponents', '6' => 'Total Componentes','7' => 'baseSupplies', '8' => 'Base Recambios', '9' => 'tvaSupplies', '10' => 'Iva Recambios', '11' => 'totalSupplies', '12' => 'Total Recambios', '13' => 'baseWorks', '14' => 'Base Trabajos', '15' => 'tvaWorks', '16' => 'Iva Trabajos', '17' => 'totalWorks', '18' => 'Total Trabajos'],
            'assets_labels' => ['id' => 'id', 'ref' => 'ref','name' => 'name','pvp' => 'pvp'],
            'setComponentsUrl' => "Intranet/expertOpinions/components/set",
            'vehicle_components' => $this->expertOpinionsService->getExpertOpinionsComponents($request),
            'vehicle_supplies' => $this->expertOpinionsService->getExpertOpinionsSupplies($request),
            'vehicle_works' => $this->expertOpinionsService->getExpertOpinionsWorks($request),
            'parent_id' => 'expertOpinion_id',
            'object_id' => ['1' => 'component_id','2' => 'supply_id','3' => 'work_id'],
            'modals_functions' => ['setComponent' => 'setComponent','saveComponent' => 'saveExpertOpinionsComponent()','setSupply' => 'setSupply', 'saveSupply' => 'saveExpertOpinionsSupply()','setWork' => 'setWork','saveWork' => 'saveExpertOpinionsWork()'],
            'forms' => ['1' => 'expertOpinion_component_form','2' => 'expertOpinion_supply_form','3' => 'expertOpinion_work_form']];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $expertOpinionsValidator = v::key('opinionId', v::stringType()->notEmpty()) 
            ->key('date', v::notEmpty());            
            try{
                $expertOpinionsValidator->assert($postData); // true                                  
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            } 
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }        
    }
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    }
}
