<?php

/**
 * Mensagens de notifica&ccedil;&otilde;es para os dispositivos m�veis.
 * 
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2016 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 */
class MensagemRestController extends Minc_Controller_AbstractRest{
    
    public function init() {
        $this->setPublicMethod('post');
        $this->setPublicMethod('index');
        $this->setPublicMethod('get');
        $this->setPublicMethod('put');
        $this->setPublicMethod('delete');
        parent::init();
    }

    public function postAction(){}
    
    public function indexAction(){
        $next = $this->_request->getParam('next');
        $offset = $this->_request->getParam('offset');
        $total = $this->_request->getParam('total');
        $idRegistration = $this->registrationId;
        $new = $this->_request->getParam('new');
        
        $listaMensagens = array();
        $modelMensagem = new Mensagem();
        $objParam = (object) array(
            'next' => $next,
            'offset' => $offset,
            'idRegistration' => $idRegistration,
            'new' => $new);
        # Verifica se existe necessidade de buscar o n�mero total de registros da consulta
        if(!$total){
            $total = $modelMensagem->buscarTotalListarDeDispositivo($objParam);
        }
        # Busca os dados da lista
        $objListaRs = $modelMensagem->listarDeDispositivo($objParam);
        if($objListaRs){
            $listaMensagens = $this->formatarUtf8Mensagens($objListaRs->toArray());
        }
        # Resposta do servi&ccedil;o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode(array(
            'list' => $listaMensagens,
            'total' => (int)$total)
        ));
    }
    
    public function getAction(){}

    public function putAction(){
        $idMensagem = $this->_request->getParam('idMensagem');
        $result = new stdClass();
        if($idMensagem){
            $modelMensagem = new Mensagem();
            $mensagem = $modelMensagem->find($idMensagem)->current();
            $objAcesso = new Acesso();
            if(!$mensagem->dtAcesso){
                $mensagem->dtAcesso = $objAcesso->getExpressionDate();
                $mensagem->save();
            }
            $result = (object)$mensagem->toArray();
        }
        
        # Resposta do servi&ccedil;o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($result));
    }

    public function deleteAction(){
        $idMensagem = $this->_request->getParam('idMensagem');
        $result = new stdClass();
        if($idMensagem){
            $modelMensagem = new Mensagem();
            $mensagem = $modelMensagem->find($idMensagem)->current();
            $objAcesso = new Acesso();
            if(!$mensagem->dtExclusao){
                $mensagem->dtExclusao = $objAcesso->getExpressionDate();
                $mensagem->save();
            }
            $result = (object)$mensagem->toArray();
        }
        
        # Resposta do servi&ccedil;o.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($result));
    }

    /**
     * Formata lista de mensagens para o formato utf8.
     * 
     * @param array $arrListaRs
     * @return array $listaMensagens
     */
    private function formatarUtf8Mensagens(array $arrListaRs = NULL) {
        $listaMensagens = array();
        if($arrListaRs){
            foreach ($arrListaRs as $mensagem) {
                $mensagem['titulo'] = utf8_encode($mensagem['titulo']);
                $mensagem['descricao'] = utf8_encode($mensagem['descricao']);
                $listaMensagens[] = (object)$mensagem;
            }
        }
        
        return $listaMensagens;
    }
}