<?php
/**
 * ManterAgentesController
 * @author Equipe RUP - Politec
 * @since 09/08/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class ManterAgentesController extends MinC_Controller_Action_Abstract {
    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access private
     */
    private $getIdUsuario = 0;



    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init() {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo');
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 144;  // Proponente
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 118; // Componente da Comiss�o
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento

        if (isset($auth->getIdentity()->Cpf) &&
                !empty($auth->getIdentity()->Cpf) &&
                isset($_GET['acao']) &&
                $_GET['acao'] == 'cc' &&
                isset($_GET['cpf']) &&
                !empty($_GET['cpf'])) // pega do readequa��o
        {
            parent::perfil(2); // scriptcase
        }

        if (isset($auth->getIdentity()->Cpf) &&
                !empty($auth->getIdentity()->Cpf) &&
                !isset($_GET['acao']) &&
                !isset($_GET['cpf']) &&
                empty($_GET['cpf'])) // pega do readequa��o
        {
            parent::perfil(4, $PermissoesGrupo); // migra��o e novo salic

        } else if(isset($auth->getIdentity()->usu_codigo) && !empty($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo); // migra��o e novo salic

        } else {
            parent::perfil(4, $PermissoesGrupo); // migra��o e novo salic
        }

        $auth = Zend_Auth::getInstance(); // pega a autentica�?o
        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        } else { // autenticacao scriptcase
            $this->getIdUsuario = (isset($_GET["idusuario"])) ? $_GET["idusuario"] : 0;
        }

        $Cpflogado  = $this->getIdUsuario;
        $this->view->cpfLogado             = $Cpflogado;
        $this->view->grupoativo            =  $GrupoAtivo->codGrupo;
        $this->view->comboestados          = Estado::buscar();
        $this->view->combotiposenderecos   = Tipoendereco::buscar();
        $this->view->combotiposlogradouros = Tipologradouro::buscar();
        $this->view->comboareasculturais   = Agente_Model_ManterAgentesDAO::buscarAreasCulturais();
        $this->view->combotipostelefones   = Tipotelefone::buscar();
        $this->view->combotiposemails      = Tipoemail::buscar();

        //Monta o combo das vis�es disponiveis
        $visoes = VisaoDAO::buscarVisao(null,null,true);
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $a = 0;
        $select = null;
        if ( isset ( $auth->getIdentity()->Cpf ) ) {
            $select[$a]['idVerificacao'] = 144; //PROPONENTE
            $select[$a]['Descricao'] = 'Proponente';
        } else {
            foreach($visoes as $visaoGrupo) {
                if($GrupoAtivo == 93 and ($visaoGrupo->idVerificacao == 209 or $visaoGrupo->idVerificacao == 216)) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if($GrupoAtivo == 97) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if($GrupoAtivo == 122 and ($visaoGrupo->idVerificacao == 210 or $visaoGrupo->idVerificacao == 216 or $GrupoAtivo == 123)) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                $a++;
            }
        }
//        echo Zend_Debug::dump($visoes); exit(0);

        $this->view->combovisoes = $select;
        parent::init();
    } // fecha m�todo init()



    /**
     * M�todo index()
     * @access public
     * @param void
     * @return void
     */
    public function indexAction() {

    } // fecha m�todo indexAction()



    /**
     * M�todo para realizar a buscar de agentes por cpf/cnpj ou por nome
     * @access public
     * @param void
     * @return void
     */
    public function buscaragenteAction() {
        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $cpf  = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // deleta a m�scara
            $nome = $post->nome;

            try {
                // valida��o dos campos
                if (empty($cpf) && empty($nome)) {
                    throw new Exception("Dados obrigat�rios n�o informados:<br /><br />� necess�rio informar o CPF/CNPJ ou o Nome!");
                }
                else if (!empty($cpf) && strlen($cpf) != 11 && strlen($cpf) != 14) // valida cnpj/cpf
                {
                    throw new Exception("O CPF/CNPJ informado � inv�lido!");
                }
                else if (!empty($cpf) && strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) // valida cpf
                {
                    throw new Exception("O CPF informado � inv�lido!");
                }
                else if (!empty($cpf) && strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)) // valida cnpj
                {
                    throw new Exception("O CNPJ informado � inv�lido!");
                }
                else {
                    // redireciona para a p�gina com a busca dos dados com pagina��o
                    $this->_redirect("manteragentes/listaragente?cpf=" . $cpf . "&nome=" . $nome);
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->cpf          = !empty($cpf) ? Validacao::mascaraCPFCNPJ($cpf) : ''; // caso exista, adiciona a m�scara
                $this->view->nome         = $nome;
            }
        } // fecha if
    } // fecha m�todo buscaragenteAction()


    /**
     * M�todo para listar os agentes com pagina��o
     * @access public
     * @param void
     * @return void
     */
    public function listaragenteAction() {
        // recebe os dados via get
        $get  = Zend_Registry::get('get');
        $cpf  = $get->cpf;
        $nome = $get->nome;

        // realiza a busca por cpf e/ou nome
        $buscar = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf, $nome);

        if (!$buscar) {
            // redireciona para a p�gina de cadastro de agentes, e, exibe uma notifica��o relativa ao cadastro
            parent::message("Agente n�o cadastrado!<br /><br />Por favor, cadastre o mesmo no formul�rio abaixo!", "manteragentes/agentes?acao=cc&cpf=" . $cpf . "&nome=" . $nome, "ALERT");
        } else {
            // ========== IN�CIO PAGINA��O ==========
            // criando a pagina�ao
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
            $paginator = Zend_Paginator::factory($buscar); // dados a serem paginados

            // p�gina atual e quantidade de �tens por p�gina
            $currentPage = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10); // 10 por p�gina
            // ========== FIM PAGINA��O ==========

            $this->view->buscar     = $paginator;
            $this->view->qtdAgentes = count($buscar); // quantidade de agentes
        } // fecha else
    } // fecha m�todo listaragenteAction()



    /**
     * M�todo com o formul�rio para cadastro de agentes
     * @access public
     * @param void
     * @return void
     */
    public function agentesAction() {
        if(isset($_POST['cep'])) {
            xd(1);
        }
    } // fecha m�todo agentesAction()


    /**
     * M�todo com o formul�rio para cadastro de dirigentes
     * @access public
     * @param void
     * @return void
     */
    public function dirigentesAction() {
        // configura��es do layout padr�o para o scriptcase
        // retira o topo e o rodap�
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
    } // fecha m�todo dirigentesAction()



    /**
     * M�todo com a p�gina de altera��o de vis�o
     * @access public
     * @param void
     * @return void
     */
    public function alterarvisaoAction() {
        // recebe o id do agente via get
        $get      = Zend_Registry::get('get');
        $idAgente = $get->idAgente;

        // busca todas as vis�es
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $visoes = VisaoDAO::buscarVisao(null, null, true);
        $a = 0;
        foreach($visoes as $visaoGrupo) {
            if($GrupoAtivo == 93 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 97) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }
        $this->view->visao = $select;

        // busca todas as vis�es do agente
        $visoes = VisaoDAO::buscarVisao($idAgente);
        $a = 0;
        foreach($visoes as $visaoGrupo) {
            if($GrupoAtivo == 93 and ($visaoGrupo->Visao == 209 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 94 and ($visaoGrupo->Visao == 209 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 97) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 120 and ($visaoGrupo->Visao == 210 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if($GrupoAtivo == 118 and ($visaoGrupo->Visao == 210 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }
        //xd($selectCad);
        $this->view->visaoAgente = $visoes;

        // busca o agente pelo id
        $this->view->agente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgente);

        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post        = Zend_Registry::get('post');
            $idAgente    = $post->idAgente;
            $visaoAgente = $post->visaoAgente;

            try {
                // ========== ATUALIZA AS VIS�ES DO AGENTE ==========

                // exclui todas as vis�es do agente
                $excluir = VisaoDAO::excluirVisao($idAgente);

                // cadastra todas as vis�es do agente
                foreach ($visaoAgente as $visao) :
                    $dados = array(
                        'idAgente' => $idAgente,
                        'Visao'    => $visao,
                        'Usuario'  => $this->getIdUsuario, // c�digo do usu�rio logado
                        'stAtivo'  => 'A');
                    $cadastrar = VisaoDAO::cadastrarVisao($dados);
                endforeach;

                if ($cadastrar) {
                    parent::message("Altera��o realizada com sucesso!", "manteragentes/alterarvisao?idAgente=" . $idAgente, "CONFIRM");
                } else {
                    throw new Exception("Erro ao efetuar altera��o das vis�es do agente!");
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->vies->message_type = "ERROR";
            }
        } // fecha if
    } // fecha m�todo alterarvisaoAction()



	/**
	 * M�todo para salvar os dados do agente no banco de dados e fazer a busca
	 * assim que o cpf/cnpj for informado
	 * @access public
	 * @param void
	 * @return void
	 */
	public function salvaragenteAction()
	{
		$i = 0; // inicializa o contador
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);
		$novos_valores = array(); // array com os dados do agente
		$v = ''; // flag verificadora de dados v�lidos/inv�lidos

		if ($_REQUEST['cpf']) // caso o cpf/cnpj tenha sido informado
		{
			$cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_REQUEST['cpf'])); // deleta as m�scaras

			// cpf/cnpj inv�lidos
			if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)))
			{
				$v = 'not';
				$novos_valores[$i]['msgCPF'] = utf8_encode($v);
			}
			else // cpf/cnpj v�lidos
			{
				$v = 'ok';
				$novos_valores[$i]['msgCPF'] = utf8_encode($v);

				// busca os dados do agente
				$dados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

				// caso o agente n�o esteja cadastrado, realizar� o cadastro de um novo
				if (!$dados)
				{
					$arrayCNPJCPF = array('CNPJCPF' => $cpf);
					$insere = Agente_Model_ManterAgentesDAO::cadastrarAgente($arrayCNPJCPF);
					$novos_valores[$i]['Agente'] = utf8_encode('novo');
				}
				else // o agente j� encontra-se cadastrado
				{
					$novos_valores[$i]['Agente'] = utf8_encode('cadastrado');
				}

				// busca os dados do agente pelo cpf/cnpj
				$novosdados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

				// busca as informa��es do agente
				foreach ($novosdados as $dado) :
					$novos_valores[$i]['idAgente']                = utf8_encode($dado->idAgente);
					$novos_valores[$i]['Nome']                    = utf8_encode($dado->Nome);
					$novos_valores[$i]['CEP']                     = !empty($dado->CEP) ? utf8_encode(Mascara::addMaskCEP($dado->CEP)) : ' ';
					$novos_valores[$i]['UF']                      = utf8_encode($dado->UF);
					$novos_valores[$i]['Cidade']                  = utf8_encode($dado->Cidade);
					$novos_valores[$i]['dsCidade']                = utf8_encode($dado->dsCidade);
					$novos_valores[$i]['TipoEndereco']            = utf8_encode($dado->TipoEndereco);
					$novos_valores[$i]['TipoLogradouro']          = utf8_encode($dado->TipoLogradouro);
					$novos_valores[$i]['Logradouro']              = utf8_encode($dado->Logradouro);
					$novos_valores[$i]['Numero']                  = utf8_encode($dado->Numero);
					$novos_valores[$i]['Complemento']             = utf8_encode($dado->Complemento);
					$novos_valores[$i]['Bairro']                  = utf8_encode($dado->Bairro);
					$novos_valores[$i]['DivulgarEndereco']        = utf8_encode($dado->DivulgarEndereco);
					$novos_valores[$i]['EnderecoCorrespondencia'] = utf8_encode($dado->EnderecoCorrespondencia);

					// �reas e segmentos
					$novos_valores[$i]['cdArea']                  = utf8_encode($dado->cdArea);
					$novos_valores[$i]['dsArea']                  = utf8_encode($dado->dsArea);
					$novos_valores[$i]['cdSegmento']              = utf8_encode($dado->cdSegmento);
					$novos_valores[$i]['dsSegmento']              = utf8_encode($dado->dsSegmento);
				endforeach;
			} // fecha else
			echo json_encode($novos_valores);
		} // fecha if
		else
		{
			die('0');
		}
	} // fecha m�todo salvaragenteAction()



	/**
	 * M�todo para salvar os dados do dirigente no banco de dados e fazer a busca
	 * assim que o cpf/cnpj for informado
	 * @access public
	 * @param void
	 * @return void
	 */
	public function salvardirigenteAction()
	{
		$i = 0; // inicializa o contador
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);
		$novos_valores = array(); // array com os dados do agente
		$v = ''; // flag verificadora de dados v�lidos/inv�lidos

		if ($_REQUEST['cpf'] && $_REQUEST['idAgenteGeral']) // caso o cpf/cnpj tenha sido informado
		{
			$cpf           = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_REQUEST['cpf'])); // deleta as m�scaras
			$idAgenteGeral = $_REQUEST['idAgenteGeral']; // idVinculoPrincipal

			// cpf/cnpj inv�lidos
			if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)))
			{
				$v = 'not';
				$novos_valores[$i]['msgCPF'] = utf8_encode($v);
			}
			else // cpf/cnpj v�lidos
			{
				$v = 'ok';
				$novos_valores[$i]['msgCPF'] = utf8_encode($v);

				// busca os dados do dirigente
				$dados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

				// caso o dirigente n�o esteja cadastrado, realizar� o cadastro de um novo
				if (!$dados)
				{
					// busca os dados do vinculo do dirigente (idVinculoPrincipal)
					//$buscarAgente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgenteGeral);

					// cadastra o dirigente
					$arrayCNPJCPF = array(
						'CNPJCPF'          => $cpf
						//,'CNPJCPFSuperior' => $buscarAgente[0]->CNPJCPF
					);
					$insere = Agente_Model_ManterAgentesDAO::cadastrarAgente($arrayCNPJCPF);
					$novos_valores[$i]['Agente'] = utf8_encode('novo');
				}
				else // o agente j� encontra-se cadastrado, realizar� a altera��o
				{
					$novos_valores[$i]['Agente'] = utf8_encode('cadastrado');
				}

				// busca os dados do agente pelo cpf/cnpj
				$novosdados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

				foreach ($novosdados as $dado) :
					$novos_valores[$i]['idAgente']                = utf8_encode($dado->idAgente);
					$novos_valores[$i]['Nome']                    = utf8_encode($dado->Nome);
					$novos_valores[$i]['CEP']                     = !empty($dado->CEP) ? utf8_encode(Mascara::addMaskCEP($dado->CEP)) : ' ';
					$novos_valores[$i]['UF']                      = utf8_encode($dado->UF);
					$novos_valores[$i]['Cidade']                  = utf8_encode($dado->Cidade);
					$novos_valores[$i]['dsCidade']                = utf8_encode($dado->dsCidade);
					$novos_valores[$i]['TipoEndereco']            = utf8_encode($dado->TipoEndereco);
					$novos_valores[$i]['TipoLogradouro']          = utf8_encode($dado->TipoLogradouro);
					$novos_valores[$i]['Logradouro']              = utf8_encode($dado->Logradouro);
					$novos_valores[$i]['Numero']                  = utf8_encode($dado->Numero);
					$novos_valores[$i]['Complemento']             = utf8_encode($dado->Complemento);
					$novos_valores[$i]['Bairro']                  = utf8_encode($dado->Bairro);
					$novos_valores[$i]['DivulgarEndereco']        = utf8_encode($dado->DivulgarEndereco);
					$novos_valores[$i]['EnderecoCorrespondencia'] = utf8_encode($dado->EnderecoCorrespondencia);

					// �reas e segmentos
					$novos_valores[$i]['cdArea']                  = utf8_encode($dado->cdArea);
					$novos_valores[$i]['dsArea']                  = utf8_encode($dado->dsArea);
					$novos_valores[$i]['cdSegmento']              = utf8_encode($dado->cdSegmento);
					$novos_valores[$i]['dsSegmento']              = utf8_encode($dado->dsSegmento);
				endforeach;
			} // fecha else

			echo json_encode($novos_valores);
		} // fecha if
		else
		{
			die('0');
		}
	} // fecha m�todo salvardirigenteAction()



	/**
	 * M�todo para buscar todos os e-mails do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscaremailsAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);

		// caso o id do agente esteja definido
		if ($_REQUEST['idAgente'])
		{
			$Emails = Agente_Model_ManterAgentesDAO::buscarEmails($_REQUEST['idAgente']); // busca os e-mails do agente
			$novos_emails = array();

			$e = 0;
			foreach ($Emails as $dado) :
				$novos_emails[$e]['idInternet']   = utf8_encode($dado->idInternet);
				$novos_emails[$e]['idAgente']     = utf8_encode($dado->idAgente);
				$novos_emails[$e]['TipoInternet'] = utf8_encode($dado->TipoInternet);
				$novos_emails[$e]['tipo']         = utf8_encode($dado->tipo);
				$novos_emails[$e]['Descricao']    = utf8_encode($dado->Descricao);
				$novos_emails[$e]['Status']       = utf8_encode($dado->Status);
				$novos_emails[$e]['Divulgar']     = utf8_encode($dado->Divulgar);
				$e++;
			endforeach;

			echo json_encode($novos_emails);
		} // fecha if

	} // fecha m�todo buscaremailsAction()



	/**
	 * M�todo para buscar todos os telefones do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscarfonesAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);

		// caso o id do agente esteja definido
		if ($_REQUEST['idAgente'])
		{
			$Fones = Agente_Model_ManterAgentesDAO::buscarFones($_REQUEST['idAgente']); // busca todos os telefones do agente
			$novos_fones = array();

			$f = 0;
			foreach ($Fones as $dado) :
				$novos_fones[$f]['TipoTelefone'] = utf8_encode($dado->TipoTelefone);
				$novos_fones[$f]['dsTelefone']   = utf8_encode($dado->dsTelefone);
				$novos_fones[$f]['UF']           = utf8_encode($dado->UF);
				$novos_fones[$f]['ufSigla']      = utf8_encode($dado->ufSigla);
				$novos_fones[$f]['DDD']          = utf8_encode($dado->DDD);
				$novos_fones[$f]['Codigo']       = utf8_encode($dado->Codigo);
				$novos_fones[$f]['Numero']       = utf8_encode($dado->Numero);
				$novos_fones[$f]['Divulgar']     = utf8_encode($dado->Divulgar);
				$f++;
			endforeach;

			echo json_encode($novos_fones);
		} // fecha if

	} // fecha m�todo buscarfonesAction()


        public function buscarenderecosAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);

		// caso o id do agente esteja definido
		if ($_REQUEST['idAgente'])
		{
			$Enderecos = Agente_Model_ManterAgentesDAO::buscarEnderecos($_REQUEST['idAgente']); // busca todos os endere�os do agente
			$novos_enderecos = array();

			$E = 0;
			foreach ($Enderecos as $dado) :
				$enderecos[$E]['Logradouro']      = utf8_encode($dado->Logradouro);
				$enderecos[$E]['TipoLogradouro']  = $dado->TipoLogradouro;
				$enderecos[$E]['Numero']          = $dado->Numero;
				$enderecos[$E]['Bairro']          = utf8_encode($dado->Bairro);
				$enderecos[$E]['Complemento']     = utf8_encode($dado->Complemento);
				$enderecos[$E]['Cep']             = $dado->Cep;
				$enderecos[$E]['Status']          = utf8_encode($dado->Status);
				$enderecos[$E]['Divulgar']        = utf8_encode($dado->Divulgar);
				$enderecos[$E]['Usuario']         = utf8_encode($dado->Usuario);
                                $enderecos[$E]['TipoEndereco']    = utf8_encode($dado->TipoEndereco);
                                $enderecos[$E]['CodTipoEndereco'] = $dado->CodTipoEndereco;
                                $enderecos[$E]['Municipio']       = utf8_encode($dado->Municipio);
                                $enderecos[$E]['CodMun']          = $dado->CodMun;
                                $enderecos[$E]['UF']              = utf8_encode($dado->UF);
                                $enderecos[$E]['CodUF']           = $dado->CodUF;
				$E++;
			endforeach;

			echo json_encode($enderecos);
		} // fecha if

	} // fecha m�todo buscarenderecosAction()





	/**
	 * M�todo para buscar todos os dirigentes do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscardirigentesAction()
	{
		$gmtDate = gmdate("D, d M Y H:i:s");
		header("Expires: {$gmtDate} GMT");
		header("Last-Modified: {$gmtDate} GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/html; charset=ISO-8859-1", true);

		$this->_helper->layout->disableLayout(); // desabilita o layout

		$post          = Zend_Registry::get('post');
		$cnpjcpf       = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cnpj_cpf));
		$idAgenteGeral = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->idAgente));

		if (!empty($cnpjcpf) && !empty($idAgenteGeral))
		{
			// busca os dirigentes vinculados ao cnpj/cpf informado
			//$Dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados($cnpjcpf);
			$Dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $idAgenteGeral);
			$this->view->Dirigentes = $Dirigentes;
		}
	} // fecha m�todo buscardirigentesAction()



	/**
	 * M�todo para buscar as �reas e segmentos culturais do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscaareasegmentoAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);

		// caso a �rea cultural esteja definida
		if ($_REQUEST['area'])
		{
			$novos_dados = array();
			$i = 0;

			// busca os agentes vinculados a �rea/segmento cultutal (, $_REQUEST['segmento'])
			$dados = TitulacaoConselheiroDAO::buscaAreaSegmento($_REQUEST['area']);

			// pega a quantidade de titulares na �rea
			$Q_titulares = TitulacaoConselheiroDAO::buscaTitularArea($_REQUEST['area']);
			$novos_dados[$i]['Q_titulares'] = utf8_encode($Q_titulares[0]->QTD);

			// pega a quantidade de suplentes na �rea
			$Q_suplentes = TitulacaoConselheiroDAO::buscaSuplentesArea($_REQUEST['area']);
			$novos_dados[$i]['Q_suplentes'] = utf8_encode($Q_suplentes[0]->QTD);

			// caso n�o existam mais vagas para titular e suplentes
			if ($Q_titulares[0]->QTD >= 1 && $Q_suplentes[0]->QTD >= 2)
			{
				$novos_dados[$i]['msgAS'] = utf8_encode('A �rea Cultural selecionada j� conta com 1 Titular e 2 Suplentes!');
			}
			else  if ($Q_titulares[0]->QTD == 0  && $Q_suplentes[0]->QTD == 0){
				$novos_dados[0]['Nome']    = 'Sem cadastro';
				$novos_dados[0]['Titular'] = '';
				$novos_dados[0]['msgAS'] = utf8_encode('Voc� pode cadastrar <strong> 1 </strong> Titular e <strong>  2 </strong> Suplente(s)!');
                        }else{
				$titularesDisponives = 1 - (int) $Q_titulares[0]->QTD;
				$suplentesDisponives = 2 - (int) $Q_suplentes[0]->QTD;
				$novos_dados[$i]['msgAS'] = utf8_encode('Voc� pode cadastrar <strong>' . $titularesDisponives . '</strong> Titular e <strong>' . $suplentesDisponives . '</strong> Suplente(s)!');
			}

			// pega os nomes de titulares e suplentes cadastrados
			foreach ($dados as $dado) :
				$novos_dados[$i]['Nome']    = utf8_encode($dado->Nome);
				$novos_dados[$i]['Titular'] = (utf8_encode($dado->stTitular) == 1) ? '(Titular)' : '(Suplente)';
				$i++;
			endforeach;

			echo json_encode($novos_dados);
		} // fecha if
	} // fecha m�todo buscaareasegmentoAction()



	/**
	 * M�todo para buscar as vis�es do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function buscarvisaoAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o layout
		$this->_helper->viewRenderer->setNoRender(true);

		// caso o id do agente esteja definido
		if ($_REQUEST['idAgente'])
		{
			$novos_dados = array();
			$i = 0;

			// busca as vis�es vinculadas ao agente
			$dados = VisaoDAO::buscarVisao($_REQUEST['idAgente']);

			foreach ($dados as $dado) :
				$novos_dados[$i]['Visao']     = utf8_encode($dado->Visao);
				$novos_dados[$i]['Descricao'] = utf8_encode($dado->Descricao);
				$novos_dados[$i]['verificacao'] = utf8_encode($dado->idVerificacao);
				$novos_dados[$i]['area']      = ($dado->area) ? utf8_encode($dado->area) : 'false';
				$i++;
			endforeach;

			echo json_encode($novos_dados);
		} // fecha if
	} // fecha m�todo buscarvisaoAction()



	/**
	 * M�todo para grava��o de todos os dados do agente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function gravaragentecompletoAction()
	{

		if ($this->getRequest()->isPost())
		{

			$post     = Zend_Registry::get('post');
			$idAgente = $post->idAgente;
			$cpf      = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // retira as m�scaras
			$TipoNome = (strlen($cpf) == 11 ? 18 : 19); // 18 = pessoa f�sica e 19 = pessoa jur�dica
			$Usuario  = $this->getIdUsuario; // id do usu�rio logado




// ================================================ IN�CIO SALVAR NOME ======================================================

			$nome = $post->nome;

			try
			{
				// busca o nome do agente
				$busca = NomesDAO::buscarNome($idAgente);

				if (!$busca)
				{
					$i = NomesDAO::gravarNome($idAgente, $TipoNome, $nome, 0, $Usuario);
				}
				else
				{
					$i = NomesDAO::atualizaNome($idAgente, $TipoNome, $nome, 0, $Usuario);
				}
			}
			catch (Exception $e)
			{
				parent::message("Erro ao salvar o nome: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
			}
// ================================================ FIM SALVAR NOME ======================================================


// ================================================ INICIO SALVAR VIS�O ======================================================
			$Visao = $post->visao;

			$grupologado = $post->grupologado;


			/*
			 * Valida��o - Se for componente da comiss�o ele n�o salva a vis�o
			 * Regra o componente da comiss�o n�o pode alterar sua vis�o.
			 */

			if($grupologado != 118):

			$GravarVisao = array( // insert
				'idAgente' => $idAgente,
				'Visao'    => $Visao,
				'Usuario'  => $Usuario,
				'stAtivo'  => 'A');

			try
			{
				$busca = VisaoDAO::buscarVisao($idAgente, $Visao);

				if (!$busca)
				{
					$i = VisaoDAO::cadastrarVisao($GravarVisao);
				}
			}
			catch(Exception $e)
			{
				parent::message("Erro ao salvar a vis�o: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
			}


// ================================================ FIM SALVAR VIS�O ======================================================



// ===================== IN�CIO SALVAR TITULA��O (�REA/SEGMENTO DO COMPONENTE DA COMISS�O) ================================


			$titular          = $post->titular;
			$areaCultural     = $post->areaCultural;
			$segmentoCultural = $post->segmentoCultural;

			// s� salva �rea e segmento para a vis�o de Componente da Comiss�o e se os campos titular e areaCultural forem informados
			if ((int) $Visao == 210 && ((int) $titular == 0 || (int) $titular == 1) && !empty($areaCultural))
			{
				$GravarComponente = array( // insert
					'idAgente'      => $idAgente,
					'cdArea'        => $areaCultural,
					'cdSegmento'    => $segmentoCultural,
					'stTitular'     => $titular,
					'stConselheiro' => 'A');

				$AtualizarComponente = array( // update
					'cdArea'        => $areaCultural,
					'cdSegmento'    => $segmentoCultural,
					'stTitular'     => $titular
					//'stConselheiro' => 'A' -- Qual caso de uso vai ativa e desativar?
					);

				try
				{
					// busca a titula��o do agente (titular/suplente de �rea cultural)
					$busca = TitulacaoConselheiroDAO::buscarComponente($idAgente, $Visao);

					if (!$busca)
					{
						$i = TitulacaoConselheiroDAO::gravarComponente($GravarComponente);
					}
					else
					{
						$i = TitulacaoConselheiroDAO::atualizaComponente($idAgente, $AtualizarComponente);
					}
				}
				catch(Exception $e)
				{
					parent::message("Erro ao salvar a �rea e Segmento: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
				}
			}

// ============================= FIM SALVAR TITULA��O (�REA/SEGMENTO DO COMPONENTE DA COMISS�O) ===========================

			endif; // Fecha o if da regra do componente da comiss�o


// =========================================== INICIO SALVAR ENDERE�OS ====================================================

            $cepEnderecos               = $post->ceps;
			$tipoEnderecos              = $post->tipoEnderecos;
			$ufEnderecos                = $post->ufs;
			$CidadeEnderecos            = $post->cidades;
			$Enderecos                  = $post->logradouros;
			$divulgarEnderecos          = $post->divulgarEnderecos;


			if (!empty($post->correspondenciaEnderecos))
			{
				$correspondenciaEnderecos = $post->correspondenciaEnderecos;
			}
			else
			{
				$correspondenciaEnderecos = -1;
			}

			$tipoLogradouros            = $post->tipoLogradouros;
			$numeros                    = $post->numeros;
            $complementos               = $post->complementos;
            $bairros                    = $post->bairros;

			try
			{
				// exclui todos os telefones do agente
				$delete = Agente_Model_EnderecoNacionalDAO::deletarEnderecoNacional($idAgente);

				// cadastra todos os telefones
				for ($i = 0; $i < sizeof($cepEnderecos); $i++)
				{
					if($correspondenciaEnderecos == "end"+$i ){$correspondenciaEnderecos = 1; }else{$correspondenciaEnderecos = 0;}

                                        $arrayEnderecos = array(
																'idAgente'      => $idAgente,
				                                                'Cep'           => str_replace(".","",str_replace("-","",$cepEnderecos[$i])),
				                                                'TipoEndereco'  => $tipoEnderecos[$i],
				                                                'UF'            => $ufEnderecos[$i],
				                                                'Cidade'        => $CidadeEnderecos[$i],
				                                                'Logradouro'    => $Enderecos[$i],
				                                                'Divulgar'      => $divulgarEnderecos[$i],
				                                                'TipoLogradouro'=> $tipoLogradouros[$i],
				                                                'Numero'        => $numeros[$i],
				                                                'Complemento'   => $complementos[$i],
				                                                'Bairro'        => $bairros[$i],
				                                                'Status'        => $correspondenciaEnderecos,
																'Usuario'       => $Usuario);


                                        $insere = Agente_Model_EnderecoNacionalDAO::gravarEnderecoNacional($arrayEnderecos);

				}

			}
			catch (Exception $e)
			{
				parent::message("Erro ao salvar o endere�o: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
			}

// ============================================= FIM SALVAR ENDERE�OS ====================================================


// =========================================== INICIO SALVAR TELEFONES ====================================================

			$tipoFones     = $post->tipoFones;
			$ufFones       = $post->ufFones;
			$dddFones      = $post->dddFones;
			$Fones         = $post->Fones;
			$divulgarFones = $post->divulgarFones;

			try
			{
				// exclui todos os telefones do agente
				$delete = Agente_Model_Telefone::excluirTodos($idAgente);

				// cadastra todos os telefones
				for ($i = 0; $i < sizeof($Fones); $i++)
				{
					$arrayTelefones = array(
											'idAgente'     => $idAgente,
											'TipoTelefone' => $tipoFones[$i],
											'UF'           => $ufFones[$i],
											'DDD'          => $dddFones[$i],
											'Numero'       => $Fones[$i],
											'Divulgar'     => $divulgarFones[$i],
											'Usuario'      => $Usuario);

					$insere = Agente_Model_Telefone::cadastrar($arrayTelefones);
				}

			}
			catch (Exception $e)
			{
				parent::message("Erro ao salvar o componente: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
			}
// =========================================== FIM SALVAR TELEFONES ====================================================



// =========================================== INICIO SALVAR EMAILS ====================================================

			$tipoEmails     = $post->tipoEmails;
			$Emails         = $post->Emails;
			$divulgarEmails = $post->divulgarEmails;
			$enviarEmails   = $post->enviarEmails;

			try
			{
				// exclui todos os e-mails do agente
				$delete = Email::excluirTodos($idAgente);

				// cadastra todos os e-mails
				for ($i = 0; $i < sizeof($Emails); $i++)
				{
					$arrayEmail = array(
										'idAgente'     => $idAgente,
										'TipoInternet' => $tipoEmails[$i],
										'Descricao'    => $Emails[$i],
										'Status'       => $enviarEmails[$i],
										'Divulgar'     => $divulgarEmails[$i],
										'Usuario'      => $Usuario);

					$insere = Email::cadastrar($arrayEmail);
				}

			}
			catch (Exception $e)
			{
				parent::message("Erro ao salvar o componente: " . $e->getMessage(), "manteragentes/agentes?acao=cc", "ERROR");
			}

// =========================================== FIM SALVAR EMAILS ====================================================


		}

		parent::message("Cadastro realizado com sucesso!", "manteragentes/agentes?acao=cc", "CONFIRM");



	}



	/**
	 * M�todo para grava��o de todos os dados do dirigente
	 * @access public
	 * @param void
	 * @return void
	 */
	public function gravardirigentecompletoAction()
	{
		// caso o formul�rio seja enviado via post
		if ($this->getRequest()->isPost())
		{
			// recebe os dados via post
			$post          = Zend_Registry::get('post');
			$idAgente      = $post->idAgente; // id do dirigente
			$idAgenteGeral = $post->idAgenteGeral; // usu�rio associado ao dirigente
			$cpf           = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // retira as m�scaras
			$TipoNome      = 18; // pessoa f�sica
			$Usuario       = $this->getIdUsuario; // id do usu�rio logado



			// ========== IN�CIO SALVAR NOME ==========
			$nome = $post->nome;

			try
			{
				// busca o nome do agente
				$busca = NomesDAO::buscarNome($idAgente);

				if (!$busca) // faz a inser��o do nome
				{
					$i = NomesDAO::gravarNome($idAgente, $TipoNome, $nome, 0, $Usuario);
				}
				else // faz a altera��o do nome
				{
					$i = NomesDAO::atualizaNome($idAgente, $TipoNome, $nome, 0, $Usuario);
				}
			} // fecha try
			catch (Exception $e)
			{
				$this->view->message = "Erro ao salvar o nome: " . $e->getMessage();
			}
			// ========== FIM SALVAR NOME ==========



			// ========== IN�CIO SALVAR ENDERE�O ==========
			$TipoEndereco   = $post->tipoEndereco;
			$TipoLogradouro = $post->tipoLogradouro;
			$Logradouro     = $post->logradouro;
			$Numero         = $post->numero;
			$Bairro         = $post->bairro;
			$Complemento    = $post->complemento;
			$Cidade         = $post->cidade;
			$UF             = $post->uf;
			$Cep            = Mascara::delMaskCEP($post->cep);
			$Divulgar       = $post->divulgarEndereco;
			$Status         = $post->enderecoCorrespondencia;

			$GravarEnderecoNacional = array( // insert
				'idAgente'        => $idAgente,
				'TipoEndereco'    => $TipoEndereco,
				'TipoLogradouro'  => $TipoLogradouro,
				'Logradouro'      => $Logradouro,
				'Numero'          => $Numero,
				'Bairro'          => $Bairro,
				'Complemento'     => $Complemento,
				'Cidade'          => $Cidade,
				'UF'              => $UF,
				'Cep'             => $Cep,
				'Status'          => $Status,
				'Divulgar'        => $Divulgar,
				'Usuario'         => $Usuario);

			$AtualizarEnderecoNacional = array( // update
				'TipoEndereco'    => $TipoEndereco,
				'TipoLogradouro'  => $TipoLogradouro,
				'Logradouro'      => $Logradouro,
				'Numero'          => $Numero,
				'Bairro'          => $Bairro,
				'Complemento'     => $Complemento,
				'Cidade'          => $Cidade,
				'UF'              => $UF,
				'Cep'             => $Cep,
				'Status'          => $Status,
				'Divulgar'        => $Divulgar,
				'Usuario'         => $Usuario);

			try
			{
				// busca o endere�o do agente
				$busca = Agente_Model_EnderecoNacionalDAO::buscarEnderecoNacional($idAgente);

				if (!$busca) // faz a inser��o do endere�o
				{
					$i = Agente_Model_EnderecoNacionalDAO::gravarEnderecoNacional($GravarEnderecoNacional);
				}
				else // faz a altera��o do endere�o
				{
					$i = Agente_Model_EnderecoNacionalDAO::atualizaEnderecoNacional($idAgente, $AtualizarEnderecoNacional);
				}
			} // fecha try
			catch(Exception $e)
			{
				$this->view->message = "Erro ao salvar o endere�o: " . $e->getMessage();
			}
			// ========== FIM SALVAR ENDERE�O ==========



			// ========== IN�CIO SALVAR VIS�O ==========
			$Visao = $post->visao;

			$GravarVisao = array( // insert
				'idAgente' => $idAgente,
				'Visao'    => $Visao,
				'Usuario'  => $Usuario,
				'stAtivo'  => 'A');

			try
			{
				// busca as vis�es do agente
				$busca = VisaoDAO::buscarVisao($idAgente, $Visao);

				if (!$busca) // faz a inser��o da vis�o
				{
					$i = VisaoDAO::cadastrarVisao($GravarVisao);
				}
			} // fecha try
			catch(Exception $e)
			{
				$this->view->message = "Erro ao salvar a vis�o: " . $e->getMessage();
			}
			// ========== FIM SALVAR VIS�O ==========



			// ========== IN�CIO TELEFONES ==========
			// array com os telefones
			$tipoFones     = $post->tipoFones;
			$ufFones       = $post->ufFones;
			$dddFones      = $post->dddFones;
			$Fones         = $post->Fones;
			$divulgarFones = $post->divulgarFones;

			try
			{
				// exclui todos os telefones
				$delete = Agente_Model_Telefone::excluirTodos($idAgente);

				// cadastra todos os telefones
				for ($i = 0; $i < sizeof($Fones); $i++)
				{
					$arrayTelefones = array(
						'idAgente'     => $idAgente,
						'TipoTelefone' => $tipoFones[$i],
						'UF'           => $ufFones[$i],
						'DDD'          => $dddFones[$i],
						'Numero'       => $Fones[$i],
						'Divulgar'     => $divulgarFones[$i],
						'Usuario'      => $Usuario);

					$insere = Agente_Model_Telefone::cadastrar($arrayTelefones);
				} // fecha for
			} // fecha try
			catch (Exception $e)
			{
				$this->view->message = "Erro ao salvar o componente: " . $e->getMessage();
			}
			// ========== FIM TELEFONES ==========



			// ========== IN�CIO E-MAILS ==========
			// array com os e-mails
			$tipoEmails     = $post->tipoEmails;
			$Emails         = $post->Emails;
			$divulgarEmails = $post->divulgarEmails;
			$enviarEmails   = $post->enviarEmails;

			try
			{
				// exclui todos os e-mails
				$delete = Email::excluirTodos($idAgente);

				// cadastra todos os e-mails
				for ($i = 0; $i < sizeof($Emails); $i++)
				{
					$arrayEmail = array(
						'idAgente'     => $idAgente,
						'TipoInternet' => $tipoEmails[$i],
						'Descricao'    => $Emails[$i],
						'Status'       => $enviarEmails[$i],
						'Divulgar'     => $divulgarEmails[$i],
						'Usuario'      => $Usuario);

					$insere = Email::cadastrar($arrayEmail);
				} // fecha for
			} // fecha try
			catch (Exception $e)
			{
				$this->view->message = "Erro ao salvar o componente: " . $e->getMessage();
			}
			// ========== FIM E-MAILS ==========



			// ========== IN�CIO DIRIGENTES ==========
			// busca os dados do associado ao dirigente (idVinculoPrincipal)
			//$buscarAgente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgenteGeral);

			// busca o dirigente vinculado ao cnpj/cpf
			//$dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados($buscarAgente[0]->CNPJCPF, null, $idAgente, $idAgenteGeral, $idAgenteGeral);
			$dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, $idAgente, $idAgenteGeral, $idAgenteGeral);

			// caso o agente n�o esteja vinculado, realizar� a vincula��o
			if (!$dadosDirigente)
			{
				// associa o dirigente ao cnpj/cpf
				$dadosVinculacao = array(
					'idAgente'           => $idAgente,
					'idVinculado'        => $idAgenteGeral,
					'idVinculoPrincipal' => $idAgenteGeral,
					'Usuario'            => $Usuario);
				$vincular = Agente_Model_ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);
			}
			// ========== FIM DIRIGENTES ==========



		} // fecha if ($this->getRequest()->isPost())

		parent::message("Cadastro realizado com sucesso!", "manteragentes/dirigentes?acao=cc&idAgenteGeral=".$idAgenteGeral, "CONFIRM");
	} // fecha m�todo gravaragentecompletoAction()

} // fecha class
