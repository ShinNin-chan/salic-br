<?php

/**
 * Registro do dispositivo para envio de notifica��es.
 * 
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2016 - Minist�rio da Cultura - Todos os direitos reservados.
 */
class DispositivoMovelRestController extends Minc_Controller_AbstractRest{
    
    public function init() {
        $this->setPublicMethod('get');
        $this->setPublicMethod('post');
        $this->setPublicMethod('index');
        parent::init();
    }

    public function postAction() {
        # Pegando parametros via POST no formato JSON
        $body = $this->getRequest()->getRawBody();
        $post = Zend_Json::decode($body);
        $registrationId = $post['registrationId'];
        $cpf = isset($post['cpf'])? $post['cpf']: NULL;
        
        $modelDispositivoMovel = new Dispositivomovel();
        $dispositivo = $modelDispositivoMovel->salvar($registrationId, $cpf);

        # Resposta da autentica��o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($dispositivo));
    }
    
    public function indexAction(){}
    
    public function getAction(){}

    public function putAction(){}

    public function deleteAction(){}

}