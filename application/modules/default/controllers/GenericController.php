<?php
/**
 * Controle Gen�rico (Utilizado por todos os controles)
 * Trata as mensagens do sistema
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 2.0
 * @package application
 * @subpackage application.controllers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class GenericController extends Zend_Controller_Action
{
	/**
	 * Vari�vel com a mensagem
	 * @var $_msg
	 */
	protected $_msg;



	/**
	 * Vari�vel com a p�gina de redirecionamento
	 * @var $_url
	 */
	protected $_url;



	/**
	 * Vari�vel com o tipo de mensagem
	 * Valores: ALERT, CONFIRM, ERROR ou vazio
	 * @var $_type
	 */
	protected $_type;



	/**
	 * Reescreve o m�todo init() para aceitar 
	 * as mensagens e redirecionamentos. 
	 * Teremos que cham�-lo dentro do 
	 * m�todo init() da classe filha assim: parent::init();
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->_msg  = $this->_helper->getHelper('FlashMessenger');
		$this->_url  = $this->_helper->getHelper('Redirector');
		$this->_type = $this->_helper->getHelper('FlashMessengerType');
	} // fecha init()



	/**
	 * M�todo para chamar as mensagens e fazer o redirecionamento
	 * @access protected
	 * @param string $msg
	 * @param string $url
	 * @param string $type
	 * @return void
	 */
	protected function message($msg, $url, $type = null)
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->flashMessenger->addMessage($msg);
		$this->_helper->flashMessengerType->addMessage($type);
		$this->_redirect($url);
		exit();
	} // fecha message()



	/**
	 * Reescreve o m�todo postDispatch() que � respons�vel 
	 * por executar uma a��o ap�s a execu��o de um m�todo
	 * @access public
	 * @param void
	 * @return void
	 */
	public function postDispatch()
	{
		if ($this->_msg->hasMessages())
		{
			$this->view->message = implode("<br />", $this->_msg->getMessages());
		}
		if ($this->_type->hasMessages())
		{
			$this->view->message_type = implode("<br />", $this->_type->getMessages());
		}
		parent::postDispatch(); // chama o m�todo pai
	} // fecha postDispatch()

} // fecha class