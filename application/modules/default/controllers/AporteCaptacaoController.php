<?php
/**
 * 
 */
class AporteCaptacaoController extends GenericControllerNew
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init() {

        /* ========== IN�CIO PERFIL ==========*/
        // define os grupos que tem acesso
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo); // perfil novo salic

        // pega o idAgente do usu�rio logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        if (isset($auth->getIdentity()->usu_codigo)) // autenticacao novo salic
        {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario['idAgente'] : 0;
        }
        else // autenticacao espaco proponente
        {
            $this->getIdUsuario = 0;
        }
        /* ========== FIM PERFIL ==========*/

        
        /* ========== IN�CIO �RG�O ========== */
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdGrupo = $GrupoAtivo->codGrupo; // id do grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao; // id do �rg�o ativo
        parent::init();
    }

    /**
     * 
     */
    public function depositoEquivocadoAction()
    {
    	$idPronac = $this->_request->getParam("idPronac");
    	if (strlen($idPronac) > 7) {
    		$idPronac = Seguranca::dencrypt($idPronac);
    	}
    	
    	$Projetos = new Projetos();
    	$this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
    	$this->view->idPronac = $idPronac;
    	# aportes
    	$whereData = array('idPronac = ?' => $idPronac, 'nrLote = ?' => -1,);
    	if ($this->getRequest()->getParam('dtDevolucaoInicio')) {
    		$whereData['dtLote >= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoInicio'), 13);
    	}
    	if ($this->getRequest()->getParam('dtDevolucaoFim')) {
    		$whereData['dtLote <= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoFim'), 13);
    	}
    	$aporteModel = new tbAporteCaptacao();
    	$this->view->dados = $aporteModel->pesquisarDepositoEquivocado($whereData);
    	$this->view->dataDevolucaoInicio = $this->getRequest()->getParam('dtDevolucaoInicio');
    	$this->view->dataDevolucaoFim = $this->getRequest()->getParam('dtDevolucaoFim');
    }
}
