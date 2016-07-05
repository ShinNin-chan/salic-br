<?php
/**
 * PrincipalController
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class PrincipalController extends MinC_Controller_Action_Abstract
{
	/**
	 * Reescreve o m�todo init()
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina
		$auth              = Zend_Auth::getInstance(); // pega a autentica��o
		$Usuario           = new Usuario(); // objeto usu�rio
		$GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
		
		parent::perfil(); // autentica��o zend

		parent::init(); // chama o init() do pai GenericControllerNew
		
	} // fecha m�todo init()



	/**
	 * P�gina inicial do sistema
	 * @access public
	 * @param void
	 * @return void
	 */
	public function indexAction()
	{
		$this->view->saudacao = Data::saudacao() . "! " . Data::mostraData() . ".";
		
		
		$tbComunicados = new tbComunicados();
		
		$where['stEstado = ?'] = 1;
		$where['stOpcao = ?'] = 0; 
		$ordem = array();
		
		$rs = $tbComunicados->listarComunicados($where, $ordem);
		
		$this->view->comunicados = $rs;
		
	} // fecha m�todo indexAction()



	public function abasAction() {} // fecha m�todo abasAction()



	public function textoAction() {} // fecha m�todo textoAction()



	public function gridAction() {} // fecha m�todo gridAction()



	public function caixadetextoAction() {} // fecha m�todo caixadetextoAction()



	public function modalAction() {} // fecha m�todo modalAction()



	public function botoesAction() {} // fecha m�todo botoesAction()



	public function exemplosAction() {} // fecha m�todo exemplosAction()


	/**
	 * M�todo listarComunicados()
	 * @access public
	 * @param void
	 * @return List
	 */
	public function listarComunicadosAction()
	{
		//header("Content-Type: text/html; charset=ISO-8859-1");
		$this->_helper->layout->disableLayout();
		$post = Zend_Registry::get('post');
		$this->intTamPag = 1;
		
		$tbComunicados = new tbComunicados();
		
		$where = array();
			
		$periodo1 = $this->_request->getParam("periodo1");
		$periodo2 = $this->_request->getParam("periodo2");
		$stEstado = $this->_request->getParam("stEstado");
		$stOpcao  = $this->_request->getParam("stOpcao");

		if(!empty($periodo1) && !empty($periodo1))
		{
			$where['dtInicioVigencia >= ?']  = $periodo1;
			$where['dtTerminoVigencia <= ?'] = $periodo2;
		}
		
		if($stEstado != '')
		{
			$where['stEstado = ?'] = $stEstado;
		}
		
		if($stOpcao != '')
		{
			$where['stOpcao = ?'] = $stOpcao;
		}
	
		
		$pag = 1;
        //$get = Zend_Registry::get('get');
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim    = $inicio + $this->intTamPag;
        
        $total = $tbComunicados->listarComunicados($where, array(), null, null, true);

        //xd($total);
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array("6 DESC");
        $rs = $tbComunicados->listarComunicados($where, $ordem, $tamanho, $inicio);
		
		$this->view->registros 		  = $rs;
		$this->view->pag 			  = $pag;
		$this->view->total 			  = $total;
		$this->view->inicio 		  = ($inicio+1);
		$this->view->fim 			  = $fim;
		$this->view->totalPag 		  = $totalPag;
		$this->view->parametrosBusca  = $_POST;
	}


	/**
	 * M�todo buscarProjeto()
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscarprojetoAction()
	{
		$Pronac = $this->_request->getParam("Pronac");
		
		if(!empty($Pronac)){
			$proj = new Projetos();
            $resp = $proj->buscarIdPronac($Pronac);
            
            if(!empty($resp)){
            	$this->_redirect('consultardadosprojeto/index?idPronac='.$resp->IdPRONAC);
            }else{
            	parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "principal/index", "ERROR");	
            }
            
		}else{
			parent::message("Informe o Pronac.", "principal/index", "ERROR");
		}
		
	}
} // fecha class