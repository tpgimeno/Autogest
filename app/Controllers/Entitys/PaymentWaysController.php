<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Accounts;
use App\Models\PaymentWays;
use App\Services\Entitys\PaymentWaysService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/**
 * Description of paymentWayController
 *
 * @author TpGimeno
 */
class PaymentWaysController extends BaseController {

    protected $paymentWayService;

    public function __construct(PaymentWaysService $paymentWayService) {
        parent::__construct();
        $this->paymentWayService = $paymentWayService;
        $this->model = new PaymentWays();
        $this->route = 'paymentWays';
        $this->titleList = 'Formas de Pago';
        $this->titleForm = 'Forma de Pago';
        $this->labels = $this->paymentWayService->getLabelsArray();
        $this->itemsList = array('id', 'name', 'accountAssociated');
    }

    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }

    public function getPaymentWaysDataAction($request) {
        $responseMessage = null;
        $accounts = $this->paymentWayService->getAllRegisters(new Accounts());
        $iterables = ['accounts' => $accounts];
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            try {
                $paymentWayValidator = v::key('name', v::stringType()->notEmpty());
                $paymentWayValidator->assert($postData); // true                 
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        } else {
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->paymentWayService->deleteRegister($this->model, $params);
        return new RedirectResponse('/Intranet/paymentWays/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
