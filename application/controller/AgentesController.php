<?php

/**
 * AgentesController
 * @author Equipe RUP - Politec
 * @since 25/05/2011
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */
require_once "GenericControllerNew.php";

class AgentesController extends GenericControllerNew {

    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access private
     */
    private $getIdUsuario = 0;

    /**
     * @var integer (vari�vel para Parecerista)
     * @access private
     */
    private $getParecerista = 'N';

    /**
     * @var integer (vari�vel com o id do grupo ativo)
     * @access private
     */
    private $GrupoAtivoSalic = 0;
    private $combovisoes = array();
    private $modal = "n";
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init() {
        $this->view->comboestados = Estado::buscar();
        $this->view->combotiposenderecos = Tipoendereco::buscar();
        $this->view->combotiposlogradouros = Tipologradouro::buscar();
        $this->view->comboareasculturais = ManterAgentesDAO::buscarAreasCulturais();
        $this->view->combotipostelefones = Tipotelefone::buscar();
        $this->view->combotiposemails = Tipoemail::buscar();

        /*         * ****************************************************************************************************** */
        //Monta o combo das vis�es disponiveis

        $visoes = VisaoDAO::buscarVisao(null, null, true);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $a = 0;
        $select = null;
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        if (isset($auth->getIdentity()->Cpf)) {
            $select[$a]['idVerificacao'] = 144; //PROPONENTE
            $select[$a]['Descricao'] = 'Proponente';
        } else {
            foreach ($visoes as $visaoGrupo) {
                if ($GrupoAtivo == 93 and ($visaoGrupo->idVerificacao == 209 or $visaoGrupo->idVerificacao == 216)) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if ($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if ($GrupoAtivo == 137 and $visaoGrupo->idVerificacao == 209) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if ($GrupoAtivo == 97) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if ($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if ($GrupoAtivo == 121 and $visaoGrupo->idVerificacao == 145) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                /* if($GrupoAtivo == 122 and $visaoGrupo->idVerificacao == 145)
                  {
                  $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                  $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                  } */
                if ($GrupoAtivo == 123 and $visaoGrupo->idVerificacao == 145) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }


                if ($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }
                if (($GrupoAtivo == 122 or $GrupoAtivo == 123) and $visaoGrupo->idVerificacao == 145) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }

                if (($GrupoAtivo == 103 || $GrupoAtivo == 142) and $visaoGrupo->idVerificacao == 144) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }

                if (($GrupoAtivo == 97 || $GrupoAtivo == 120) and $visaoGrupo->idVerificacao == 217) {
                    $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                    $select[$a]['Descricao'] = $visaoGrupo->Descricao;
                }


                $a++;
            }
        }

        $this->view->combovisoes = $select;

        //verifica se a funcionadade devera abrir em modal
        if ($this->_request->getParam("modal") == "s") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            header("Content-Type: text/html; charset=ISO-8859-1");
            $this->modal = "s";
            $this->view->modal = "s";
        } else {
            $this->modal = "n";
            $this->view->modal = "n";
        }

        parent::init();
    }
    
    /**
     * Fun��o para tratar data
     * @access private
     * @param Date
     * @return Date
     */
    private function formatadata($data) {
        if ($data) {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $ano = substr($data, 6, 4);
            $dataformatada = $ano . "/" . $mes . "/" . $dia;
        } else {
            $dataformatada = null;
        }

        return $dataformatada;
    }

    private function autenticacao() {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 118; // Componente da Comiss�o
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 137; // Coordenador de PRONAC
        $PermissoesGrupo[] = 144; // Proponente
        //Perfis incluidos para cadastro de Agentes no ato do cadastro do projeto FNC.
        $PermissoesGrupo[] = 103; // Coordenador de Analise
        $PermissoesGrupo[] = 142; // Coordenador de Convenios

        if (isset($auth->getIdentity()->Cpf) &&
                !empty($auth->getIdentity()->Cpf) &&
                isset($_GET['acao']) && $_GET['acao'] == 'cc' &&
                isset($_GET['cpf']) &&
                !empty($_GET['cpf'])) { // pega do readequa��o
            parent::perfil(2); // scriptcase
        }

        if (isset($auth->getIdentity()->Cpf) &&
                !empty($auth->getIdentity()->Cpf) &&
                !isset($_GET['acao']) &&
                !isset($_GET['cpf']) &&
                empty($_GET['cpf'])) { // pega do readequa��o
            parent::perfil(4, $PermissoesGrupo); // migra��o e novo salic
        } elseif (isset($auth->getIdentity()->usu_codigo) && !empty($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo); // migra��o e novo salic
        } else {
            parent::perfil(4, $PermissoesGrupo); // migra��o e novo salic
        }

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        } else { // autenticacao scriptcase
            $this->getIdUsuario = (isset($_GET["idusuario"])) ? $_GET["idusuario"] : 0;
        }

        $Cpflogado = $this->getIdUsuario;

        $this->view->cpfLogado = $Cpflogado;
        $this->view->grupoativo = $GrupoAtivo->codGrupo;



        /*         * ****************************************************************************************************** */
        $this->GrupoAtivoSalic = $GrupoAtivo->codGrupo; // Grava o Id do Grupo Ativo
        /*         * ****************************************************************************************************** */



        /*         * ****************************************************************************************************** */
        // Controle para carregar o menu lateral ou n�o 

        $menuLateral = $this->_request->getParam("menuLateral");

        if (($menuLateral == 'true') || ($menuLateral == '')) {
            $this->view->menuLateral = 'true';
        } else {
            $this->view->menuLateral = 'false';
        }
        /*         * ****************************************************************************************************** */


        /* Monta os dados do Agente */

        $idAgente = $this->_request->getParam("id");

        if (($GrupoAtivo->codGrupo == 94) || ($GrupoAtivo->codGrupo == 118)) {
            $idAgente = $this->getIdUsuario;
        }


        $qtdDirigentes = '';
        if (isset($idAgente)) {

            $dados = ManterAgentesDAO::buscarAgentes(null, null, $idAgente);

            if (!$dados) {
                parent::message("Agente n�o encontrado!", "agentes/buscaragente", "ALERT");
            }


            $this->view->telefones = ManterAgentesDAO::buscarFones($idAgente);
            $this->view->emails = ManterAgentesDAO::buscarEmails($idAgente);
            $visoes = VisaoDAO::buscarVisao($idAgente);
            $this->view->visoes = $visoes;

            foreach ($visoes as $v) {
                if ($v->Visao == '209') {
                    $this->getParecerista = 'sim';
                }
            }

            if ($dados[0]->TipoPessoa == 1) {
                $dirigentes = ManterAgentesDAO::buscarVinculados(null, null, null, null, $idAgente);
                $qtdDirigentes = count($dirigentes);
                $this->view->dirigentes = $dirigentes;
            }

            $this->view->dados = $dados;
            $this->view->qtdDirigentes = $qtdDirigentes;
            $this->view->parecerista = $this->getParecerista;

            $this->view->id = $idAgente;
        }
    }

// fecha m�todo autenticacao

    private function incluir() {
        $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($this->_request->getParam("cpf")));

        $cpfMask = '';
        $tipoCpf = '';

        if (strlen($cpf) == 11) {
            $tipoCpf = 'cpf';
            $cpfMask = Mascara::addMaskCPF($this->_request->getParam("cpf"));
        }
        if (strlen($cpf) == 14) {
            $tipoCpf = 'cnpj';
            $cpfMask = Mascara::addMaskCNPJ($this->_request->getParam("cpf"));
        }

        $this->view->cpf = $cpfMask;
        $this->view->tipocpf = $tipoCpf;
        $this->view->idpronac = $this->_request->getParam('idpronac');
    }

    /**
     * M�todo responsavel por vincular o Responsavel logado a seu proprio perfil de Proponente
     */
    public function vincular($cpfCadastrado = null, $idAgenteCadastrado = null) {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao

        /**
         * O metodo so e executado quando o usuario logado for Responsavel.
         * So entra aqui quando o cpf do Proponente que esta sendo cadastrado for igual ao do Responsavel logado.
         */
        if (isset($auth->getIdentity()->Cpf) && !empty($cpfCadastrado) && !empty($idAgenteCadastrado) && ($auth->getIdentity()->Cpf == Mascara::delMaskCPFCNPJ($cpfCadastrado))) :

            /**
             * ==============================================================
             * INICIO DO VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE)
             * ==============================================================
             */
            /* ========== BUSCA O ID DO RESPONSAVEL ========== */
            $Sgcacesso = new Sgcacesso();
            $buscarResponsavel = $Sgcacesso->buscar(array('Cpf = ?' => $cpfCadastrado));

            /* ========== VINCULA O RESPONSAVEL A SEU PROPRIO PERFIL DE PROPONENTE ========== */
            if (count($buscarResponsavel) > 0) :
                $tbVinculo = new TbVinculo();

                $dadosVinculo = array(
                    'idAgenteProponente' => $idAgenteCadastrado
                    , 'dtVinculo' => new Zend_Db_Expr('GETDATE()')
                    , 'siVinculo' => 2
                    , 'idUsuarioResponsavel' => $buscarResponsavel[0]->IdUsuario);
                $tbVinculo->inserir($dadosVinculo);
            endif;

        /**
         * ==============================================================
         * FIM DO VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE)
         * ==============================================================
         */
        endif;
    }

// fecha metodo vincular()

    /**
     * M�todo index()
     * @access public
     * @param void
     * @return void
     */
    public function indexAction() {
        
    }


// fecha m�todo indexAction()

    public function buscaPessoaAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        #Instancia a Classe de Servi�o do WebService da Receita Federal
        $wsServico = new ServicosReceitaFederal();

        $retorno        = array();
        $cpf            = str_replace('.', '', str_replace('-', '', $this->getRequest()->getParam('cpf')));
        $teste          = $this->getRequest()->getParam('teste');
        $tipoPessoa     = $this->getRequest()->getParam('tipoPessoa');
        $erro           = 0;

        try {
            if(11 == strlen( $cpf )) {
                if (!validaCPF($cpf)) {
                    $retorno['error'] = utf8_encode('CPF inv�lido');
                    $erro = 1;
                } else {
                    $arrResultado = $wsServico->consultarPessoaFisicaReceitaFederal($cpf);

                    if (empty($arrResultado)) {
                        $retorno['error'] = utf8_encode('Pessoa n�o encontrada!');
                        $erro = 1;
                    }
                    if ($erro == 0 && count($arrResultado) > 0) {
                        #xd( $arrResultado );
                        $retorno['dados']['idPessoa'] = $arrResultado['idPessoaFisica'];
                        $retorno['dados']['nome'] = utf8_encode($arrResultado['nmPessoaFisica']);
                        $retorno['dados']['cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                        $retorno['error'] = '';

                    } else {
                        $retorno['error'] = utf8_encode('Pessoa n�o encontrada!!');
                    }
                }
            } else if(15 == strlen($cpf)){
                if (!isCnpjValid($cpf)) {
                    $retorno['error'] = utf8_encode('CNPJ inv�lido');
                    $erro = 1;
                } else {
                    $arrResultado = $wsServico->consultarPessoaJuridicaReceitaFederal($cpf);
                    if (empty($arrResultado)) {
                        $retorno['error'] = utf8_encode('Pessoa n�o encontrada!!');
                        $erro = 1;
                    }
                    if ($erro == 0 && count($arrResultado) > 0) {
                        #xd( $arrResultado );
                        $retorno['dados']['idPessoa'] = $arrResultado['idPessoaJuridica'];
                        $retorno['dados']['nome'] = utf8_encode($arrResultado['nmRazaoSocial']);
                        $retorno['dados']['cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                        $retorno['error'] = '';

                    } else {
                        $retorno['error'] = utf8_encode('Pessoa n�o encontrada!');
                    }
                }
            }

        } catch (InvalidArgumentException $exc) {
            $retorno['error'] = utf8_encode('Pessoa n�o encontrada!');
        } catch (Exception $exc) {
            $retorno['error'] = utf8_encode('Pessoa n�o encontrada!');
        }

        echo json_encode($retorno);
    }

    /**
     * M�todo incluiragente()
     * @access public
     * @param void
     * @return void
     */
    public function incluiragenteAction() {
        $this->autenticacao();

        #$wsServico = new ServicosReceitaFederal();
        #$arrResultado = $wsServico->consultarPessoaFisicaReceitaFederal('11010601660' );
        #xd( $arrResultado );


        #$wsWebServiceSEI = new ServicosSEI();
        #$arrRetornoGerarProcedimento = $wsWebServiceSEI->wsGerarProcedimento();
        #x($arrRetornoGerarProcedimento->ProcedimentoFormatado);
        #$chars = array(".","/","-");
        #$nrProcessoSemFormatacao = str_replace($chars,"",$arrRetornoGerarProcedimento->ProcedimentoFormatado);
        #x($nrProcessoSemFormatacao);
        #xd($arrRetornoGerarProcedimento->ProcedimentoFormatado);

        $modal  = $this->_request->getParam("modal");
        $modulo = $this->_request->getParam("modulo");
        $this->view->modulo = $modulo;
        
        if (!empty($modal)) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            header("Content-Type: text/html; charset=ISO-8859-1");
            $this->view->modal = "s";
            $this->view->cpf = $this->_request->getParam("cpfCnpj");
            $this->view->caminho = $this->_request->getParam("caminho");            
        } else {
            $this->view->modal = "n";
            $this->view->caminho = "";
        }
        $this->incluir();
    }

    /**
     * M�todo incluirfonecedor()
     * @access public
     * @param void
     * @return void
     */
    public function incluirfornecedorAction() {
        $this->autenticacao();

        $modal  = $this->_request->getParam("modal");
//        $modulo = $this->_request->getParam("modulo");
//        $this->view->modulo = $modulo;

        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        header("Content-Type: text/html; charset=ISO-8859-1");
        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");
        
        $this->incluir();
    }

    /**
     * M�todo incluirprocurador()
     * @access public
     * @param void
     * @return void
     */
    public function incluirprocuradorAction() {
        $this->autenticacao();

        $modal  = $this->_request->getParam("modal");
//        $modulo = $this->_request->getParam("modulo");
//        $this->view->modulo = $modulo;

        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        header("Content-Type: text/html; charset=ISO-8859-1");
        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");

        $this->incluir();
    }

    /**
     * M�todo incluirbeneficiario()
     * @access public
     * @param void
     * @return void
     */
    public function incluirbeneficiarioAction() {
        $this->autenticacao();

        $modal  = $this->_request->getParam("modal");
//        $modulo = $this->_request->getParam("modulo");
//        $this->view->modulo = $modulo;

        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        header("Content-Type: text/html; charset=ISO-8859-1");
        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");

        $this->incluir();
    }

    public function incluiragenteexternoAction() {
        Zend_Layout::startMvc(array('layout' => 'layout_login'));
        $this->incluir();
    }

    /**
     * M�todo para visualiza��o dos dados do agente
     * @access public
     * @param void
     * @return List
     */
    public function agentesAction() {
//        $this->verificarPermissaoAcesso(false, false, true);

        $this->autenticacao();

        if (($this->GrupoAtivoSalic != 1111) &&
                ($this->GrupoAtivoSalic != 118) &&
                ($this->GrupoAtivoSalic != 144) &&
                ($this->GrupoAtivoSalic != 137) &&
                ($this->GrupoAtivoSalic != 121) &&
                ($this->GrupoAtivoSalic != 122) &&
                ($this->GrupoAtivoSalic != 123) &&
                ($this->GrupoAtivoSalic != 97) &&
                ($this->GrupoAtivoSalic != 93) &&
                ($this->GrupoAtivoSalic != 120) &&
                ($this->GrupoAtivoSalic != 94)) {
            parent::message("Voc� n�o tem permiss�o para essa funcionalidade!", "agentes/sempermissao", "ALERT");
        }
        
        /* Teste atestados
          $projetoDAO = new projetos();
          $projeto = $projetoDAO->buscaProjetosProdutosAnaliseInicial(array('idAgenteParecerista = ?' => 9626, 'DtDistribuicao >= ?' => '2011/05/15', 'DtDistribuicao <= ?' => '2011/05/26'));
         */

        $idAgente = $this->_request->getParam("id");

        if (($this->GrupoAtivoSalic == 94) || ($this->GrupoAtivoSalic == 118)) {
            $idAgente = $this->getIdUsuario;
        }

        if (($this->GrupoAtivoSalic == 93) && ($idAgente == '')) {
            $this->_redirect('agentes/buscaragente');
        }

        if (($idAgente == '')) {
            $this->_redirect('agentes/incluiragente');
        }
        //xd($idAgente);

        $tbInfo = new TbInformacaoProfissional();
        $this->view->formacoes = $tbInfo->BuscarInfo($idAgente, null);

        $ano = date('Y');
        $mes = date('m');

        $tbAusencia = new TbAusencia();
        $dados = $tbAusencia->BuscarAusencia($idAgente, $ano, 2, $mes);

        $totalDias = 0;
        foreach ($dados as $d) {
            if (($d->siAusencia == 0) OR ($d->siAusencia == 1)) {
                $totalDias = $totalDias + $d->qtdDias;
            }
        }

        $tbCredenciamentoParecerista = new TbCredenciamentoParecerista();
        $credenciados = $tbCredenciamentoParecerista->BuscarCredenciamentos($idAgente);

        $this->view->credenciados = $credenciados;
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
        $this->view->id = $idAgente;
    }

    /**
     * M�todo dirigentes()
     * @access public
     * @param void
     * @return void
     */
    public function dirigentesAction() {
        $this->autenticacao();
    }

    /**
     * M�todo visualizadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function visualizadirigenteAction() {
        $this->autenticacao();

        $idAgente       = $this->_request->getParam("id");
        $idDirigente    = $this->_request->getParam("idDirigente");

        if (isset($idAgente)) {

            $dadosDirigenteD = ManterAgentesDAO::buscarVinculados(null, null, $idDirigente, null, $idAgente);
            $dados = ManterAgentesDAO::buscarAgentes(null, null, $idDirigente);
            $this->view->dadosD = $dados;

            if (!$dados) {
                parent::message("Agente n�o encontrado!", "agentes/buscaragente", "ALERT");
            }

            $this->view->telefonesD = ManterAgentesDAO::buscarFones($idDirigente);
            $this->view->emailsD = ManterAgentesDAO::buscarEmails($idDirigente);
            $this->view->visoesD = VisaoDAO::buscarVisao($idDirigente);
            $this->view->Instituicao = "sim";
            $this->view->id = $this->_request->getParam("id");
            $this->view->idDirigente = $this->_request->getParam("idDirigente");

            if ($dadosDirigenteD) {
                $this->view->vinculado = "sim";
            }
            $tbTipodeDocumento = new VerificacaoAGENTES();
            $whereLista['idTipo = ?'] = 5;
            $rsTipodeDocumento = $tbTipodeDocumento->buscar($whereLista);
            $this->view->tipoDocumento = $rsTipodeDocumento;
//            xd($rsTipodeDocumento);

            $tbDirigenteMandato = new tbAgentesxVerificacao();
            $buscarMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idAgente, 'idDirigente = ?' => $idDirigente, 'stMandato = ?' => 0));
            $this->view->mandatos = $buscarMandato;
            $mandatoAtual = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idAgente,'idDirigente = ?' => $idDirigente, 'stMandato = ?' => 0), array('dtFimMandato DESC'))->current();
            $this->view->mandatosAtual = $mandatoAtual;
        }
    } 
    /*
     * M�todo mandato()
     * @access public
     * @param void
     * @return void
     */

    public function mandatoAction() {

        $this->autenticacao();

        if (!empty($_POST)) {
            $idAgente = $this->_request->getParam("id");
            $idDirigente = $this->_request->getParam("idDirigente");
                        
            $tbDirigenteMandato = new tbAgentesxVerificacao();

            $idVerificacao          = $this->_request->getParam("idVerificacao");
            $dsNumeroDocumento      = $this->_request->getParam("dsNumeroDocumento");
            $idDirigente            = $this->_request->getParam("idDirigente");
            $dtInicioVigencia       = $this->_request->getParam("dtInicioVigencia");
            $dtInicioVigencia       = Data::dataAmericana($dtInicioVigencia);
            $dtTerminoVigencia      = $this->_request->getParam("dtTerminoVigencia");
            $dtTerminoVigencia      = Data::dataAmericana($dtTerminoVigencia);
            $stMandato              = 0;
            $idArquivo              = $this->_request->getParam("idArquivo");

            //valida��o data do mandato            
            $buscarMandato = $tbDirigenteMandato->mandatoRepetido($idAgente, $dtInicioVigencia, $dtTerminoVigencia);

            if (count($buscarMandato) > 0) {
                parent::message("N�o poder� inserir um novo mandato, pois j� existe um mandato em vigor para esse dirigente!mandatos", "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
            }

            if(count($_FILES) > 0) {
            $ERROR = ''; //Criado para corrigir erro.
            foreach ($_FILES['arquivo']['name'] as $key=>$val) {
                $arquivoNome     = $_FILES['arquivo']['name'][$key];
                $arquivoTemp     = $_FILES['arquivo']['tmp_name'][$key];
                $arquivoTipo     = $_FILES['arquivo']['type'][$key];
                $arquivoTamanho  = $_FILES['arquivo']['size'][$key];
                
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o

                if($arquivoExtensao != "doc" && $arquivoExtensao != "docx"){
                    if(!empty($arquivoTemp)) {
                        $idArquivo = $this->cadastraranexo($arquivoNome,$arquivoTemp,$arquivoTipo,$arquivoTamanho);

                        $dados = array(
                             'idArquivo'        => $idArquivo,
                             'idTipoDocumento'  => 0,
                        );

                        $tabela      = new tbDocumento();
                        $idDocumento = $tabela->inserir($dados);
                            if($idDocumento){
                               $idDocumento = $tabela->ultimodocumento(array('idArquivo = ? '=>$idArquivo));
                               $idDocumento = $idDocumento->idDocumento;
                            }else{
                               $ERROR .= "Erro no anexo";
                               $idDocumento = 0;
                               $erro = true;
                            }

                    }
                } else {
                    parent::message("N&atilde;o s&atilde;o permitidos documentos de texto doc/docx!", "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
                    }
                }
            }
            try {
                $arrayMandato = array(
                    'idVerificacao'         => $idVerificacao,
                    'dsNumeroDocumento'     => $dsNumeroDocumento,
                    'dtInicioMandato'       => $dtInicioVigencia,
                    'dtFimMandato'          => $dtTerminoVigencia,
                    'stMandato'             => $stMandato,
                    'idEmpresa'             => $idAgente,
                    'idDirigente'           => $idDirigente
                );
                
                if($idArquivo > 0){ $arrayMandato['idArquivo'] = $idArquivo;}
                
                $salvarMandato = $tbDirigenteMandato->inserir($arrayMandato);

                $buscarMandato = $tbDirigenteMandato->buscar(array('idAgentexVerificacao = ?' => $salvarMandato));

                if (!empty($buscarMandato)) {

                    $dadosBuscar['idVerificacao']       = $buscarMandato[0]->idVerificacao;
                    $dadosBuscar['dsNumeroDocumento']   = $buscarMandato[0]->dsNumeroDocumento;
                    $dadosBuscar['dtInicioMandato']     = $buscarMandato[0]->dtInicioMandato;
                    $dadosBuscar['dtFimMandato']        = $buscarMandato[0]->dtFimMandato;
                    $dadosBuscar['stMandato']           = $buscarMandato[0]->stMandato;
                    $dadosBuscar['idEmpresa']           = $buscarMandato[0]->idEmpresa;
                    $dadosBuscar['idDirigente']         = $buscarMandato[0]->idDirigente;
                    $dadosBuscar['idArquivo']           = $buscarMandato[0]->idArquivo;
//                echo json_encode($dadosBuscar);
//                exit();
                }                     

                parent::message("Cadastro realizado com sucesso!", "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao salvar o mandato:" . $e->getMessage(), "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
            }
        }
    }

    /*
     * M�todo excluirmandato()
     * @access public
     * @param void
     * @return void
     */

    public function excluirmandatoAction() {

        $this->autenticacao();

        $tbDirigenteMandato = new tbAgentesxVerificacao();

        $idAgente = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");
        $idMandato = $this->_request->getParam("idAgentexVerificacao");

        try {
            $arrayMandato = array('stMandato' => 1);

            $whereMandato['idAgentexVerificacao = ?'] = $idMandato;
            $tbDirigenteMandato->alterar($arrayMandato, $whereMandato);

            parent::message("Exclus�o realizada com sucesso!", "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao excluir o mandato:" . $e->getMessage(), "agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }
    
    
    public function cadastraranexo($arquivoNome,$arquivoTemp,$arquivoTipo,$arquivoTamanho) {
        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
            $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
            $arquivoHash     = Upload::setHash($arquivoTemp); // hash
        }

        // cadastra dados do arquivo
        $dadosArquivo = array(
                'nmArquivo'         => $arquivoNome,
                'sgExtensao'        => $arquivoExtensao,
                'dsTipoPadronizado' => $arquivoTipo,
                'nrTamanho'         => $arquivoTamanho,
                'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                'dsHash'            => $arquivoHash,
                'stAtivo'           => 'A');
        $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

        // pega o id do �ltimo arquivo cadastrado
        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
        $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

        // cadastra o bin�rio do arquivo
        $dadosBinario = array(
                'idArquivo' => $idUltimoArquivo,
                'biArquivo' => $arquivoBinario);
        $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

        return $idUltimoArquivo;
    }


    /*     * ** ENDERE�OS ********************************************************************************** */

    /**
     * M�todo enderecos()
     * Visualiza, exclui e altera os endere�os do agente
     * @access public
     * @param void
     * @return void
     */
    public function enderecosAction() {
        $this->autenticacao();

        $idAgente = $this->_request->getParam("id");
        $lista = ManterAgentesDAO::buscarEnderecos($idAgente);

        $this->view->endereco = $lista;
        $this->view->qtdEndereco = count($lista);
    }

    /**
     * M�todo salvaendereco()
     * Salva o endere�o do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaenderecoAction() {
        $this->autenticacao();

        $Usuario = $this->getIdUsuario; // id do usu�rio logado
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $this->_request->getParam("id");

        $cepEndereco = $this->_request->getParam("cep");
        $tipoEndereco = $this->_request->getParam("tipoEndereco");
        $ufEndereco = $this->_request->getParam("uf");
        $CidadeEndereco = $this->_request->getParam("cidade");
        $Endereco = $this->_request->getParam("logradouro");
        $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
        $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
        $numero = $this->_request->getParam("numero");
        $complemento = $this->_request->getParam("complemento");
        $bairro = $this->_request->getParam("bairro");
        $enderecoCorrespodencia = $this->_request->getParam("enderecoCorrespodencia");

        try {

            $arrayEnderecos = array(
                'idAgente' => $idAgente,
                'Cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                'TipoEndereco' => $tipoEndereco,
                'UF' => $ufEndereco,
                'Cidade' => $CidadeEndereco,
                'Logradouro' => $Endereco,
                'Divulgar' => $divulgarEndereco,
                'TipoLogradouro' => $tipoLogradouro,
                'Numero' => $numero,
                'Complemento' => $complemento,
                'Bairro' => $bairro,
                'Status' => $enderecoCorrespodencia,
                'Usuario' => $Usuario
            );


            if ($enderecoCorrespodencia == "1") {
                $alteraEnderecoCorrespondencia = EnderecoNacionalDAO::mudaCorrespondencia($idAgente);
            }

            $insere = EnderecoNacionalDAO::gravarEnderecoNacional($arrayEnderecos);
            parent::message("Cadastro realizado com sucesso!", "agentes/enderecos/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar o endere�o: " . $e->getMessage(), "agentes/enderecos/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo excluiendereco()
     * Exclui o endere�o do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluienderecoAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idEndereco = $this->_request->getParam("idEndereco");
        $qtdEndereco = $this->_request->getParam("qtdEndereco");

        $enderecoCorrespondencia = $this->_request->getParam("enderecoCorrespondencia");

        if ($qtdEndereco <= 1) {
            parent::message("Voc� tem que ter pelo menos um endere�o cadastrado!", "agentes/enderecos/id/" . $idAgente, "ALERT");
        }

        try {
            $excluir = EnderecoNacionalDAO::deletarEnderecoNacional($idEndereco);

            if ($enderecoCorrespondencia == "1") {
                $novaCorrespondencia = EnderecoNacionalDAO::novaCorrespondencia($idAgente);
            }

            parent::message("Exclus�o realizada com sucesso!", "agentes/enderecos/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao excluir o ender�o: " . $e->getMessage(), "agentes/enderecos/id/" . $idAgente, "ERROR");
        }
    }

    /*     * ** FIM ENDERE�OS ****************************************************************************** */

    /*     * ** TELEFONES ********************************************************************************** */

    /**
     * M�todo telefones()
     * @access public
     * @param void
     * @return void
     */
    public function telefonesAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $lista = ManterAgentesDAO::buscarFones($idAgente);

        $this->view->telefones = $lista;
        $this->view->qtdTel = count($lista);
    }

    /**
     * M�todo salvatelefone()
     * Salva o tefefone do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvatelefoneAction(){
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usu�rio logado

        $idAgente = $this->_request->getParam("id");
        $tipoFone = $this->_request->getParam("tipoFone");
        $ufFone = $this->_request->getParam("ufFone");
        $dddFone = $this->_request->getParam("dddFone");
        $Fone = $this->_request->getParam("fone");
        $divulgarFone = $this->_request->getParam("divulgarFone");

        try {
            $arrayTelefones = array(
                'idAgente' => $idAgente,
                'TipoTelefone' => $tipoFone,
                'UF' => $ufFone,
                'DDD' => $dddFone,
                'Numero' => $Fone,
                'Divulgar' => $divulgarFone,
                'Usuario' => $Usuario
            );
            Telefone::cadastrar($arrayTelefones);
            parent::message("Cadastro realizado com sucesso!", "agentes/telefones/id/" . $idAgente, "CONFIRM");
            
        } catch (Exception $e) {
            parent::message("Erro ao salvar o Telefone: " . $e->getMessage(), "agentes/telefones/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo excluitelefone()
     * Exclui o tefefone do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluitelefoneAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idTelefone = $this->_request->getParam("idTelefone");
        $qtdTel = $this->_request->getParam("qtdTel");

        if ($qtdTel <= 1) {
            parent::message("Voc� tem que ter pelo menos um telefone cadastrado!", "agentes/telefones/id/" . $idAgente, "ALERT");
        } else {

            try {

                $excluir = Telefone::excluir($idTelefone);

                parent::message("Exclus�o realizada com sucesso!", "agentes/telefones/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao excluir o telefone: " . $e->getMessage(), "agentes/telefones/id/" . $idAgente, "ERROR");
            }
        }
    }

    /*     * ** FIM TELEFONES ****************************************************************************** */

    /*     * ** E-MAILS ************************************************************************************ */

    /**
     * M�todo emails()
     * @access public
     * @param void
     * @return void
     */
    public function emailsAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $lista = ManterAgentesDAO::buscarEmails($idAgente);

        $this->view->emails = $lista;
        $this->view->qtdEmail = count($lista);
    }

    /**
     * M�todo salvaemail()
     * Salva o email do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaemailAction() {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usu�rio logado

        $idAgente = $this->_request->getParam("id");
        $tipoEmail = $this->_request->getParam("tipoEmail");
        $Email = $this->_request->getParam("email");
        $divulgarEmail = $this->_request->getParam("divulgarEmail");
        $enviarEmail = $this->_request->getParam("enviarEmail");

        try {
            $arrayEmail = array(
                'idAgente' => $idAgente,
                'TipoInternet' => $tipoEmail,
                'Descricao' => $Email,
                'Status' => $enviarEmail,
                'Divulgar' => $divulgarEmail,
                'Usuario' => $Usuario
            );

            $insere = Email::cadastrar($arrayEmail);

            parent::message("Cadastro realizado com sucesso!", "agentes/emails/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agentes/emails/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo excluiemail()
     * Exclui o email do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluiemailAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idInternet = $this->_request->getParam("idInternet");
        $qtdEmail = $this->_request->getParam("qtdEmail");

        if ($qtdEmail <= 1) {
            parent::message("Voc� tem que ter pelo menos um email cadastrado!", "agentes/emails/id/" . $idAgente, "ALERT");
        } else {
            try {
                $excluir = Email::excluir($idInternet);
                parent::message("Exclus�o realizada com sucesso!", "agentes/emails/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao excluir o e-mail: " . $e->getMessage(), "agentes/emails/id/" . $idAgente, "ERROR");
            }
        }
    }

    /*     * ** FIM E-MAILS ******************************************************************************** */

    /*     * ** ESCOLARIDADE ******************************************************************************* */

    /**
     * M�todo escolaridade()
     * @access public
     * @param void
     * @return void
     */
    public function escolaridadeAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $escolaridade = new TbEscolaridade();

        $escolaridades = $escolaridade->BuscarEscolaridades($idAgente);
        $tipoEscolaridade = $escolaridade->BuscarTipoEscolaridade();
        $pais = $escolaridade->BuscarPais();

        $this->view->tipoEscolaridade = $tipoEscolaridade;
        $this->view->escolaridades = $escolaridades;
        $this->view->pais = $pais;
        $this->view->id = $this->_request->getParam("id");
    }

    /**
     * M�todo salvaescolaridade()
     * Salva a escolaridade do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaescolaridadeAction() {
        $this->autenticacao();
        $dtAtual = date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $idDocumento = null;

        $tipoEscolaridade = $this->_request->getParam('tipoEscolaridade');
        $instituicao = $this->_request->getParam('instituicao');
        $curso = $this->_request->getParam('curso');
        $dtEntrada = $this->formatadata($this->_request->getParam('dtentrada'));
        $dtSaida = $this->formatadata($this->_request->getParam('dtsaida'));
        $pais = $this->_request->getParam('pais');
        $tipoDocumento = $this->_request->getParam('tipoDocumento');

        $tbEscolaridade = new TbEscolaridade();

        if (!empty($_FILES['arquivo'])):
            $tbDocumento = new tbDocumento();
            $tbArquivo = new tbArquivo();
            $tbArquivoImagem = new tbArquivoImagem();

            // pega as informa��es do arquivo
            $arquivoNome = $_FILES['arquivo']['name']; // nome
            $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

            if (!empty($arquivoNome)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
            }
            if (!empty($arquivoTemp)) {
                $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash = Upload::setHash($arquivoTemp); // hash
            }
        endif;

        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            if (!empty($_FILES['arquivo'])):
                $dadosArquivo = array('nmArquivo' => $arquivoNome,
                    'sgExtensao' => $arquivoExtensao,
                    'stAtivo' => 'A',
                    'dsHash' => $arquivoHash,
                    'dtEnvio' => $dtAtual
                );

                $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
                $idArquivo = $tbArquivo->buscarUltimo();

                $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                    'biArquivo' => $arquivoBinario
                );

                $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem 
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

                $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

                $dadosDocumento = array('idTipoDocumento' => 0,
                    'idArquivo' => $idArquivo['idArquivo']
                );

                $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
                $ultimoDocumento = $tbDocumento->ultimodocumento();

                $idDocumento = $ultimoDocumento['idDocumento'];
            endif;


            $arrayDados = array('idAgente' => $idAgente,
                'idTipoEscolaridade' => $tipoEscolaridade,
                'nmCurso' => $curso,
                'nmInstituicao' => $instituicao,
                'dtInicioCurso' => $dtEntrada,
                'dtFimCurso' => $dtSaida,
                'idDocumento' => $idDocumento,
                'idPais' => $pais
            );

            $salvarInfo = $tbEscolaridade->inserirEscolaridade($arrayDados);

            $db->commit();
            parent::message("Cadastrado realizado com sucesso!", "agentes/escolaridade/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
//            xd($e->getMessage());
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/escolaridade/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo formacao()
     * @access public
     * @param void
     * @return void
     */
    public function formacaoAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $escolaridade = new TbEscolaridade();

        $tipoEscolaridade = $escolaridade->BuscarTipoEscolaridade();
        $this->view->tipoEscolaridade = $tipoEscolaridade;

        $pais = $escolaridade->BuscarPais();
        $this->view->pais = $pais;

        $tipoDocumento = $escolaridade->BuscarTipoDocumento();
        $this->view->tipoDocumento = $tipoDocumento;

        $tbInfo = new TbInformacaoProfissional();

        $this->view->formacoes = $tbInfo->BuscarInfo($idAgente, null);

        $this->view->id = $this->_request->getParam("id");
        $this->view->visoes = VisaoDAO::buscarVisao($idAgente);
    }

    /**
     * M�todo salvaformacao()
     * Salva a forma��o do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaformacaoAction() {
        $this->autenticacao();
        $dtAtual = date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $profissao = $this->_request->getParam('profissao');
        $cargo = $this->_request->getParam('cargo');
        $endereco = $this->_request->getParam('endereco');
        $dtEntrada = $this->formatadata($this->_request->getParam('dtentrada'));
        $dtSaida = $this->formatadata($this->_request->getParam('dtsaida'));
        $tipoDocumento = $this->_request->getParam('tipoDocumento');

        $tbInfoPro = new TbInformacaoProfissional();
        $tbDocumento = new tbDocumento();
        $tbArquivo = new tbArquivo();
        $tbArquivoImagem = new tbArquivoImagem();


        // pega as informa��es do arquivo
        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if (!empty($arquivoNome)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
        }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $dadosArquivo = array('nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'stAtivo' => 'A',
                'dsHash' => $arquivoHash,
                'dtEnvio' => $dtAtual
            );

            $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
            $idArquivo = $tbArquivo->buscarUltimo();

            $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                'biArquivo' => $arquivoBinario
            );

            $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem 
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

            $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

            $dadosDocumento = array('idTipoDocumento' => 0,
                'idArquivo' => $idArquivo['idArquivo']
            );

            $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
            $ultimoDocumento = $tbDocumento->ultimodocumento();

            $arrayDados = array('idAgente' => $idAgente,
                'nmProfissao' => $profissao,
                'nmCargo' => $cargo,
                'dsEndereco' => $endereco,
                'dtInicioVinculo' => $dtEntrada,
                'dtFimVinculo' => $dtSaida,
                'idDocumento' => $ultimoDocumento['idDocumento'],
                'siInformacao' => 0
            );

            $salvarInfo = $tbInfoPro->inserirInfo($arrayDados);

            $db->commit();
            parent::message("Cadastrado realizado com sucesso!", "agentes/formacao/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/formacao/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo ferias()
     * @access public
     * @param void
     * @return void
     */
    public function feriasAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $ano = date('Y');

        $tbAusencia = new TbAusencia();

        $dados = $tbAusencia->BuscarAusencia($idAgente, $ano, 2, null);

        $totalDias = 0;

        foreach ($dados as $d) {
            if (($d->siAusencia == 0) OR ($d->siAusencia == 1)) {
                $totalDias = $totalDias + $d->qtdDias;
            }
        }

        $this->view->id = $this->_request->getParam("id");
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
    }

    /*
      /**
     * M�todo salvaferias()
     * Salva as f�rias do Parecerista
     * @access public
     * @param void
     * @return void

      public function ausenciarepetidaAction()
      {
      $idAgente   = $this->_request->getParam("id");
      $dtInicio   = $this->formatadata($this->_request->getParam("dtinicio"));
      $dtFim   	= $this->formatadata($this->_request->getParam("dtfim"));

      $tbAusencia = new TbAusencia();

      $repetida = $tbAusencia->BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim);

      if(count($repetida) > 0)
      {
      parent::message("N�o foi poss�vel agendar suas f�rias!", "agentes/ferias/id/".$idAgente."?dtInicio=".$dtInicio."&dtFim".$dtFim, "ALERT");
      }

      }
     */

    /**
     * M�todo salvaferias()
     * Salva as f�rias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaferiasAction() {
        $this->autenticacao();

        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicio"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfim"));

        $tbAusencia = new TbAusencia();

        $repetida = $tbAusencia->BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim);

        if (count($repetida) > 0) {
            parent::message("J� existe agendamento de f�rias para o per�odo solicitado!", "agentes/ferias/id/" . $idAgente . "?dtInicio=" . $this->_request->getParam("dtinicio") . "&dtFim=" . $this->_request->getParam("dtfim"), "ALERT");
        }

        try {
            $dados = array('idTipoAusencia' => 2,
                'idAgente' => $idAgente,
                'dtInicioAusencia' => $dtInicio,
                'dtFimAusencia' => $dtFim,
                'stImpacto' => 0,
                'siAusencia' => 0,
                'dsJustificativaAusencia' => '',
                'dtCadastroAusencia' => $dtAtual
            );

            // salva o novo registro			
            $salvar = $tbAusencia->inserir($dados);

            // Busca o id do registro cadastrado
            $ultimoRegistro = $tbAusencia->UltimoRegistro();

            // Monta o array com o id do ultimo registro cadastrado
            $dados = array('idAlteracao' => $ultimoRegistro[0]->id);

            // Alterar o ultimo registro cadastrado colocando o seu pr�prio id no campo idaltera��o
            $altera = $tbAusencia->alteraAusencia($dados, $ultimoRegistro[0]->id);

            parent::message("Suas f�rias foram agendas para " . $dtInicio . " � " . $dtFim . ". Aguarde Aprova��o do Coordenador. 
							<br /> Caso n�o tenha resposta favor entre em contato com o mesmo!", "agentes/ferias/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Error! " . $e->getMessage(), "agentes/ferias/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo alterarferias()
     * Altera as f�rias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function alterarferiasAction() {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicioalteracao"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfimalteracao"));
        $justificativa = trim(strip_tags($this->_request->getParam("justificativa")));
        $tipoAlteracao = $this->_request->getParam("tipoalteracao");
        $idferias = $this->_request->getParam("idferias");
        $stAusencia = 0;

        if ($tipoAlteracao == 0) {
            $stAusencia = 2;
        } else {
            $stAusencia = 3;
        }

        try {

            $tbAusencia = new TbAusencia();

            if ($tipoAlteracao == 1) {
                $dados = array('dsJustificativaAusencia' => $justificativa,
                    'siAusencia' => 3
                );

                $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            } else {

                $repetida = $tbAusencia->BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim);

                if (count($repetida) > 0) {
                    parent::message("J� existe f�rias marcada dentro desse per�odo!", "agentes/ferias/id/" . $idAgente, "ALERT");
                }


                $dados = array('dsJustificativaAusencia' => $justificativa,
                    'siAusencia' => 2
                );

                $altera = $tbAusencia->alteraAusencia($dados, $idferias);

                $dados = array('idTipoAusencia' => 2,
                    'idAgente' => $idAgente,
                    'dtInicioAusencia' => $dtInicio,
                    'dtFimAusencia' => $dtFim,
                    'stImpacto' => 0,
                    'siAusencia' => 0,
                    'dsJustificativaAusencia' => '',
                    'idAlteracao' => $idferias,
                    'dtCadastroAusencia' => $dtAtual
                );

                $insere = $tbAusencia->inserirAusencia($dados);
            }

            parent::message("Altera��o realizada com sucesso!", "agentes/ferias/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/ferias/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo cancelaferias()
     * Cancela as f�rias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function cancelaferiasAction() {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $justificativa = trim(strip_tags($this->_request->getParam("justificativa")));
        $idferias = $this->_request->getParam("idferias");

        try {
            $tbAusencia = new TbAusencia();

            $dados = array('dsJustificativaAusencia' => $justificativa,
                'siAusencia' => 3
            );

            $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            parent::message("Exclus�o realizada com sucesso!", "agentes/painelferias", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/painelferias", "ERROR");
        }
    }

    /**
     * M�todo confirmaferias()
     * Confirma as f�rias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function confirmaferiasAction() {
        $this->autenticacao();
        $idferias = $this->_request->getParam("idferias");

        try {
            $tbAusencia = new TbAusencia();

            $dados = array('siAusencia' => 1);

            $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            parent::message("Aprovado com sucesso!", "agentes/painelferias", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/painelferias", "ERROR");
        }
    }

    /**
     * M�todo atestados()
     * @access public
     * @param void
     * @return void
     */
    public function atestadosAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $ano = date('Y');

        $tbAusencia = new TbAusencia();
        $atestados = $tbAusencia->BuscarAusencia($idAgente, $ano, 1, null);

        $this->view->atestados = $atestados;
    }

    /**
     * M�todo salvaatestado()
     * Salva o atestado do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaatestadoAction() {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d h:i:s");

        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicio"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfim"));
        $impacto = $this->_request->getParam("impacto");

        $tbAusencia = new TbAusencia();
        $tbDocumento = new tbDocumento();
        $tbArquivo = new tbArquivo();
        $tbArquivoImagem = new tbArquivoImagem();

        // pega as informa��es do arquivo
        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if (!empty($arquivoNome)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
        }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $dadosArquivo = array('nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'stAtivo' => 'A',
                'dsHash' => $arquivoHash,
                'dtEnvio' => $dtAtual
            );

            $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
            $idArquivo = $tbArquivo->buscarUltimo();

            $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                'biArquivo' => $arquivoBinario
            );

            $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem 
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

            $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

            $dadosDocumento = array('idTipoDocumento' => 0,
                'idArquivo' => $idArquivo['idArquivo']
            );

            $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
            $ultimoDocumento = $tbDocumento->ultimodocumento();

            $dadosAusencia = array('idTipoAusencia' => 1,
                'idAgente' => $idAgente,
                'dtInicioAusencia' => $dtInicio,
                'dtFimAusencia' => $dtFim,
                'stImpacto' => $impacto,
                'idDocumento' => $ultimoDocumento['idDocumento'],
                'siAusencia' => 0,
                'dsJustificativaAusencia' => '',
                'dtCadastroAusencia' => $dtAtual
            );


            // salva o novo registro			
            $salvar = $tbAusencia->inserir($dadosAusencia);

            // Busca o id do registro cadastrado
            $ultimoRegistro = $tbAusencia->UltimoRegistro();

            // Monta o array com o id do ultimo registro cadastrado
            $dados = array('idAlteracao' => $ultimoRegistro[0]->id);

            // Alterar o ultimo registro cadastrado colocando o seu pr�prio id no campo idaltera��o	
            $altera = $tbAusencia->alteraAusencia($dados, $ultimoRegistro[0]->id);


            /* ********************************************************************************************** */
            if ($impacto = 1) {
                // Tem que pegar todos os produtos que est�o como Parecerista e devolver para o Coord.
                // Criar uma fun��o para isso!


                $tbDistribuirParecer = new tbDistribuirParecer();
                $projetoDAO = new Projetos();
                $projetos = $projetoDAO->buscaProjetosProdutosAnaliseInicial(array('idAgenteParecerista = ?' => $idAgente, 'DtDistribuicao >= ?' => '' . $dtInicio . '', 'DtDistribuicao <= ?' => '' . $dtFim . ''));

                foreach ($projetos as $p) {
                    $dados = array('Observacao' => 'Devolvido por motivo de atestado m�dico.',
                            'idUsuario' => $this->getIdUsuario,
                            'DtDevolucao' => $dtAtual
                    );
                    $salvar = $tbDistribuirParecer->atualizarParecer($dados, $p->idDistribuirParecer);
                }
            }
            /* ********************************************************************************************** */

            $db->commit();
            parent::message("Cadastro realizado com sucesso!", "agentes/atestados/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/atestados/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo credenciamento()
     * @access public
     * @param void
     * @return void
     */
    public function credenciamentoAction() {
        $this->autenticacao();

        if (($this->GrupoAtivoSalic != 137) || ($this->getParecerista != 'sim')) {
            parent::message("Voc� n�o tem permiss�o para essa funcionalidade!", "agentes/sempermissao", "ALERT");
        }

        $idAgente = $this->_request->getParam("id");

        $tbCredenciamentoParecerista = new TbCredenciamentoParecerista();
        $credenciados = $tbCredenciamentoParecerista->BuscarCredenciamentos($idAgente);

        $tbInformacaoProfissional = new TbInformacaoProfissional();
        $buscaAnos = $tbInformacaoProfissional->AnosExperiencia($idAgente);

        $anos = 0;

        foreach ($buscaAnos as $a) {
            $anos = $anos + $a->qtdAnos;
        }

        $Verificacao = new VerificacaoAGENTES();
        $buscaNivel = $Verificacao->buscar(array('idtipo=?' => 25), 'Descricao');

        $this->view->anosexperiencia = $anos;
        $this->view->credenciados = $credenciados;
        $this->view->Niveis = $buscaNivel;
    }

    /**
     * M�todo descredenciamento()
     * @access public
     * @param void
     * @return void
     */
    public function descredenciamentoAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idCredenciamento = $this->_request->getParam("idcredenciamento");
        $siCredenciamento = $this->_request->getParam("sicredenciamento");
        $novoSiCredenciamento = 1;

        //Caso o credenciamento esteja ativo ele vai desativar
        //Caso contrario, ele ativa
        if ($siCredenciamento == 1) {
            $novoSiCredenciamento = 0;
        }

        $tbCredenciamentoParecerista = new TbCredenciamentoParecerista();

        try {
            $dados = array('siCredenciamento' => $novoSiCredenciamento);

            $exclui = $tbCredenciamentoParecerista->excluiCredenciamento($idCredenciamento);
            //$altera = $tbCredenciamentoParecerista->alteraCredenciamento($dados, $idCredenciamento);
            parent::message("Descredenciamento realizado com sucesso!", "agentes/credenciamento/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao alterar o credenciamento! " . $e->getMessage(), "agentes/credenciamento/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo salvacredenciamento()
     * Salva o credenciamento do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvacredenciamentoAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $areaCultural = $this->_request->getParam("areaCultural");
        $segmentoCultural = $this->_request->getParam("segmentoCultural");
        $nivel = $this->_request->getParam("nivel");

        $tbCredenciamentoParecerista = new TbCredenciamentoParecerista();

        $idArea = substr($areaCultural, 0, 1);
        $idSegmento = substr($segmentoCultural, 0, 1);

        $qtdSegmento = $tbCredenciamentoParecerista->QtdSegmento($idAgente, $idArea);
//        if (($qtdSegmento[0]->qtd) >= 3) {
//            parent::message("Voc� s� pode credenciar  3 (tr�s) �reas culturais!", "agentes/credenciamento/id/" . $idAgente, "ALERT");
//        }

        $qtdArea = $tbCredenciamentoParecerista->QtdArea($idAgente);
        if ((($qtdArea[0]->qtd) >= 3) and (($qtdSegmento[0]->qtd) == 0)) {
            parent::message("Voc� s� pode credenciar  3 (tr�s) �reas culturais!", "agentes/credenciamento/id/" . $idAgente, "ALERT");
        }

        $verificarCadastrado = $tbCredenciamentoParecerista->verificarCadastrado($idAgente, $segmentoCultural, $areaCultural);

        if (count($verificarCadastrado) > 0) {
            parent::message("�rea e segmento j� credenciado!", "agentes/credenciamento/id/" . $idAgente, "ALERT");
        }

        try {
            $dados = array('idAgente' => $idAgente,
                'idCodigoArea' => $areaCultural,
                'idCodigoSegmento' => $segmentoCultural,
                'idVerificacao' => $nivel,
                'siCredenciamento' => 1
            );
            $credenciados = $tbCredenciamentoParecerista->inserirCredenciamento($dados);

            parent::message("Credenciamento realizado com sucesso!", "agentes/credenciamento/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agentes/credenciamento/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * M�todo agentecadastrado()
     * @access public
     * @param void
     * @return void
     */
    public function agentecadastradoAction() {
        //$this->autenticacao();
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);
        #$cpf = $_REQUEST['cpf'];
        $cpf = preg_replace('/\.|-|\//','',$_REQUEST['cpf']);

        $novos_valores = array();

        $dados = ManterAgentesDAO::buscarAgentes($cpf);

        #xd( (strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)) );
        if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf))) {
            $novos_valores[0]['msgCPF'] = utf8_encode('invalido');
        } else {
            if (count($dados) != 0) {
                foreach ($dados as $dado) {
                    $dado = (array)$dado;
                    array_walk($dado, function($value, $key) use (&$dado){
                        $dado[$key] = utf8_encode($value);
                    });
                    $novos_valores[0]['msgCPF'] = utf8_encode('cadastrado');
                    $novos_valores[0]['idAgente'] = utf8_encode($dado['idAgente']);
                    $novos_valores[0]['Nome'] = utf8_encode($dado['Nome']);
                    $novos_valores[0]['agente'] = $dado;
                }
            } else {
                #Instancia a Classe de Servi�o do WebService da Receita Federal
                $wsServico = new ServicosReceitaFederal();
                if(11 == strlen( $cpf )) {
                        $arrResultado = $wsServico->consultarPessoaFisicaReceitaFederal($cpf);
                        if (count($arrResultado) > 0) {
                            $novos_valores[0]['msgCPF'] = utf8_encode('novo');
                            $novos_valores[0]['idAgente'] = $arrResultado['idPessoaFisica'];
                            $novos_valores[0]['Nome'] = utf8_encode($arrResultado['nmPessoaFisica']);
                            $novos_valores[0]['Cep'] = $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'];
                            #$novos_valores[0]['agente'] = $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'];
                        }
                } else if(15 == strlen($cpf)){
                        $arrResultado = $wsServico->consultarPessoaJuridicaReceitaFederal($cpf);
                        if (count($arrResultado) > 0) {
                            $novos_valores[0]['msgCPF'] = utf8_encode('novo');
                            $novos_valores[0]['idAgente'] = $arrResultado['idPessoaJuridica'];
                            $novos_valores[0]['Nome'] = utf8_encode($arrResultado['nmRazaoSocial']);
                            $novos_valores[0]['Cep'] = $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'];
                            #$novos_valores[0]['agente'] = $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'];
                        }
                }

            }
        }

        echo json_encode($novos_valores);
    }

    /**
     * 
     */
    private function salvaragente()
    {
        /**/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = isset($auth->getIdentity()->IdUsuario) ? $auth->getIdentity()->IdUsuario : $auth->getIdentity()->usu_codigo;
        // =============================================== IN�CIO SALVAR CPF/CNPJ ==================================================

        $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($this->_request->getParam("cpf"))); // retira as m�scaras
        $Tipo = $this->_request->getParam("Tipo");


        $arrayAgente = array('CNPJCPF' => $cpf,
            'TipoPessoa' => $Tipo,
            'Status' => 0,
            'Usuario' => $Usuario
        );

        $Agentes = new Agentes();
        
        $salvaAgente = $Agentes->inserirAgentes($arrayAgente);

        $Agente = $Agentes->BuscaAgente($cpf);

        $idAgente = $Agente[0]->idAgente;


        // ================================================ FIM SALVAR CPF/CNPJ =====================================================			
        // ================================================ IN�CIO SALVAR NOME ======================================================

        $nome = $this->_request->getParam("nome");
        $TipoNome = (strlen($cpf) == 11 ? 18 : 19); // 18 = pessoa f�sica e 19 = pessoa jur�dica
        
        if($this->modal == "s"){
            $nome = Seguranca::tratarVarAjaxUFT8($nome);
        }
        
        try {
            $gravarNome = NomesDAO::gravarNome($idAgente, $TipoNome, $nome, 0, $Usuario);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o nome: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
        }

        // ================================================ FIM SALVAR NOME ======================================================
        // ================================================ INICIO SALVAR VIS�O ======================================================
        $Visao = $this->_request->getParam("visao");

        $grupologado = $this->_request->getParam("grupologado");

        /*
         * Valida��o - Se for componente da comiss�o ele n�o salva a vis�o
         * Regra o componente da comiss�o n�o pode alterar sua vis�o.
         */

        if ($grupologado != 118):

            $GravarVisao = array(// insert
                'idAgente' => $idAgente,
                'Visao' => $Visao,
                'Usuario' => $Usuario,
                'stAtivo' => 'A');

            try {
                $busca = VisaoDAO::buscarVisao($idAgente, $Visao);

                if (!$busca) {
                    $i = VisaoDAO::cadastrarVisao($GravarVisao);
                }
            } catch (Exception $e) {
                parent::message("Erro ao salvar a vis�o: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
            }


            // ================================================ FIM SALVAR vis�o ======================================================
            // ===================== IN�CIO SALVAR TITULA��O (�rea/SEGMENTO DO COMPONENTE DA COMISS�O) ================================


            $titular = $this->_request->getParam("titular");
            $areaCultural = $this->_request->getParam("areaCultural");
            $segmentoCultural = $this->_request->getParam("segmentoCultural");

            // s� salva �rea e segmento para a vis�o de Componente da Comiss�o e se os campos titular e areaCultural forem informados
            if ((int) $Visao == 210 && ((int) $titular == 0 || (int) $titular == 1) && !empty($areaCultural)) {
                $GravarComponente = array(// insert
                    'idAgente' => $idAgente,
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular,
                    'stConselheiro' => 'A');

                $AtualizarComponente = array(// update
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular
                        //'stConselheiro' => 'A' -- Qual caso de uso vai ativa e desativar?										
                );

                try {
                    // busca a titula��o do agente (titular/suplente de �rea cultural)
                    $busca = TitulacaoConselheiroDAO::buscarComponente($idAgente, $Visao);

                    if (!$busca) {
                        $i = TitulacaoConselheiroDAO::gravarComponente($GravarComponente);
                    } else {
                        $i = TitulacaoConselheiroDAO::atualizaComponente($idAgente, $AtualizarComponente);
                    }
                } catch (Exception $e) {
                    parent::message("Erro ao salvar a �rea e segmento: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
                }
            }

        // ============================= FIM SALVAR TITULA��O (�rea/SEGMENTO DO COMPONENTE DA COMISS�O) ===========================

        endif; // Fecha o if da regra do componente da comiss�o
        // =========================================== INICIO SALVAR ENDERE�OS ====================================================

        $cepEndereco = $this->_request->getParam("cep");
        $tipoEndereco = $this->_request->getParam("tipoEndereco");
        $ufEndereco = $this->_request->getParam("uf");
        $CidadeEndereco = $this->_request->getParam("cidade");
        $Endereco = $this->_request->getParam("logradouro");
        $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
        $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
        $numero = $this->_request->getParam("numero");
        $complemento = $this->_request->getParam("complemento");
        $bairro = $this->_request->getParam("bairro");
        $enderecoCorrespodencia = 1;

        try {

            $arrayEnderecos = array(
                'idAgente' => $idAgente,
                'Cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                'TipoEndereco' => $tipoEndereco,
                'UF' => $ufEndereco,
                'Cidade' => $CidadeEndereco,
                'Logradouro' => $Endereco,
                'Divulgar' => $divulgarEndereco,
                'TipoLogradouro' => $tipoLogradouro,
                'Numero' => $numero,
                'Complemento' => $complemento,
                'Bairro' => $bairro,
                'Status' => $enderecoCorrespodencia,
                'Usuario' => $Usuario
            );


            $insere = EnderecoNacionalDAO::gravarEnderecoNacional($arrayEnderecos);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o endere�o: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
        }


        // ============================================= FIM SALVAR ENDERE�OS ====================================================
        // =========================================== INICIO SALVAR TELEFONES ====================================================

        $movimentacacaobancaria = $this->_request->getParam('movimentacaobancaria');
        if (empty($movimentacacaobancaria)) {
            $tipoFone = $this->_request->getParam("tipoFone");
            $ufFone = $this->_request->getParam("ufFone");
            $dddFone = $this->_request->getParam("dddFone");
            $Fone = $this->_request->getParam("fone");
            $divulgarFone = $this->_request->getParam("divulgarFone");

            try {
                $arrayTelefones = array(
                    'idAgente' => $idAgente,
                    'TipoTelefone' => $tipoFone,
                    'UF' => $ufFone,
                    'DDD' => $dddFone,
                    'Numero' => $Fone,
                    'Divulgar' => $divulgarFone,
                    'Usuario' => $Usuario
                );



                $insere = Telefone::cadastrar($arrayTelefones);
            } catch (Exception $e) {
                parent::message("Erro ao salvar o telefone: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
            }
        }

        // =========================================== FIM SALVAR TELEFONES ====================================================
        // =========================================== INICIO SALVAR EMAILS ====================================================			


        if (empty($movimentacacaobancaria)) {
            $tipoEmail = $this->_request->getParam("tipoEmail");
            $Email = $this->_request->getParam("email");
            $divulgarEmail = $this->_request->getParam("divulgarEmail");
            $enviarEmail = 1;

            try {
                $arrayEmail = array(
                    'idAgente' => $idAgente,
                    'TipoInternet' => $tipoEmail,
                    'Descricao' => $Email,
                    'Status' => $enviarEmail,
                    'Divulgar' => $divulgarEmail,
                    'Usuario' => $Usuario
                );

                $insere = Email::cadastrar($arrayEmail);
            } catch (Exception $e) {
                parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
            }
        }
        // =========================================== FIM SALVAR EMAILS ====================================================
        // ================ INICIO SALVAR VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE) ================
        $acao = null;
        if (empty($movimentacacaobancaria)) {
            try {
                $this->vincular($cpf, $idAgente);
            } catch (Exception $e) {
                parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agentes/incluiragente", "ERROR");
            }
            // ================ FIM SALVAR VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE) ================
            // Caso venha do UC 89 Solicitar Vinculo
            $acao = $this->_request->getParam('acao');
            #$idResponsavel = $this->idResponsavel;
            $idResponsavel = 0;

            // ============== VINCULA O RESPONSAVEL COM O PROPONENTE CADASTRADO =============================

            if ((!empty($acao)) && (!empty($idResponsavel))):

                $tbVinculo = new TbVinculo();

                $dadosVinculo = array(
                    'idAgenteProponente' => $idAgente,
                    'dtVinculo' => new Zend_Db_Expr('GETDATE()'),
                    'siVinculo' => 0,
                    'idUsuarioResponsavel' => $idResponsavel
                );

                $tbVinculo->inserir($dadosVinculo);

            endif;
        }
        //================ FIM VINCULA O RESPONSAVEL COM O PROPONENTE CADASTRADO ========================
    	if (isset($acao) && $acao != '') {
    		// Retorna para o listar propostas
    		$tbVinculo = new TbVinculo();
    		$dadosVinculo = array(
    				'idAgenteProponente' => $idAgente,
    				'dtVinculo' => new Zend_Db_Expr('GETDATE()'),
    				'siVinculo' => 0,
    				'idUsuarioResponsavel' => $auth->getIdentity()->IdUsuario
    		);
    		$tbVinculo->inserir($dadosVinculo);
    	}
        /**/

        // Se vim do UC 10 - solicitar altera��o no Projeto
        // Chega aqui com o idpronac
        $idpronac = $this->_request->getParam('idpronac');
        // Se vim do UC38 - Movimenta��o bancaria - Capta��o
        $projetofnc = $this->_request->getParam('cadastrarprojeto');

        # tratamento para disparar "js custom event" no dispatch
        $agente = ManterAgentesDAO::buscarAgentes($cpf);
        $agente = $agente[0];
        $agente->id = $agente->idAgente;
        $agente->nome = $agente->Nome;
        $agente->cpfCnpj = $agente->CNPJCPF;
        
        $agenteArray = (array) $agente;
        array_walk($agenteArray, function($value, $key) use ($agente){
            $agente->$key = utf8_encode($value);
        });
        
        $this->salvarAgenteRedirect($agente, $idpronac, $projetofnc, $movimentacacaobancaria, $acao);
    }

    /**
     * M�todo salvaagentegeral()
     * Salva os dados do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaagentegeralAction() {
        $this->autenticacao();
        $this->salvaragente();
    }

    public function salvaagentegeralexternoAction() {
        $this->salvaragente();
    }

    /**
     * Metodo para efetuar o redirecionamento apos o cadastro de agentes
     */
    private function salvarAgenteRedirect($agente, $idpronac = null, $projetofnc = null, $movimentacacaobancaria = null, $acao = null)
    {
        // Se vim do UC 10 - solicitar altera��o no Projeto
        $idpronac = $this->_request->getParam('idpronac');
        // Se vim do UC38 - Movimenta��o bancaria - Capta��o
        $projetofnc = $this->_request->getParam('cadastrarprojeto');

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (!empty($idpronac)) {
            if (!$this->getRequest()->isXmlHttpRequest()) {
                // Retorna para a url anterior do UC10
                parent::message(
                    'Cadastro realizado com sucesso!',
                    "solicitaralteracao/acaoprojeto?idpronac={$idpronac}&cpf={$agente->cpfCnpj}",
                    'CONFIRM'
                );
            }
            echo '<script>';
            echo 'var event = new CustomEvent("agenteCadastrar_POST", { "detail": ' , json_encode($agente) , ' });';
            echo 'document.dispatchEvent(event)';
            echo '</script>';
            echo '<br/><br/><br/><br/><center><font color="green">Cadastrado com sucesso!</font></center>';
        } else if (!empty($movimentacacaobancaria)) {
            echo '<script>';
            echo 'var event = new CustomEvent("agenteCadastrar_POST", { "detail": ' , json_encode($agente) , ' });';
            echo 'document.dispatchEvent(event)';
            echo '</script>';
            echo '<br/><br/><br/><br/><center><font color="green">Cadastrado com sucesso!</font></center>';
            // Retorna para a url anterior do UC116
            parent::message("Cadastro realizado com sucesso.", "cadastrarprojeto", "CONFIRM");
        } else if (($acao != '')) {
            parent::message(
                'Proponente cadastrado com sucesso. Uma solicita��o de v�nculo foi enviada a ele.',
                'manterpropostaincentivofiscal/listarproposta',
                'CONFIRM'
            );
        } else {
            // Caso n�o seja ele retorna para a visualiza��o dos dados cadastrados do agente
            # editado para atender
            parent::message('Cadastro realizado com sucesso!', "agentes/agentes/id/{$agente->id}", 'CONFIRM');
        }
    }

    /*     * ** DIRIGENTES ******************************************************************************** */

    /**
     * M�todo incluirdirigente()
     * @access public
     * @param void
     * @return void
     */
    public function incluirdirigenteAction() {
        $this->autenticacao();
    }

    /**
     * M�todo salvadirigentegeral()
     * @access public
     * @param void
     * @return void
     */
    public function salvadirigentegeralAction() {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usu�rio logado
        $idAgenteGeral = $this->_request->getParam("id"); // id da instituicao
        // =============================================== IN�CIO SALVAR CPF/CNPJ ==================================================

        $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($this->_request->getParam("cpf"))); // retira as m�scaras
        $Tipo = $this->_request->getParam("Tipo");


        $arrayAgente = array('CNPJCPF' => $cpf,
            'TipoPessoa' => $Tipo,
            'Status' => 0,
            'Usuario' => $Usuario
        );

        // Retorna o idAgente cadastrado

        $Agentes = new Agentes();

        $salvaAgente = $Agentes->inserirAgentes($arrayAgente);

        $Agente = $Agentes->BuscaAgente($cpf);

        $idAgente = $Agente[0]->idAgente;


        // ================================================ FIM SALVAR CPF/CNPJ =====================================================			
        // ================================================ IN�CIO SALVAR NOME ======================================================

        $nome = $this->_request->getParam("nome");
        $TipoNome = (strlen($cpf) == 11 ? 18 : 19); // 18 = pessoa f�sica e 19 = pessoa jur�dica

        try {
            $gravarNome = NomesDAO::gravarNome($idAgente, $TipoNome, $nome, 0, $Usuario);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o nome: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }

        // ================================================ FIM SALVAR NOME ======================================================
        // ================================================ INICIO SALVAR VIS�O ======================================================
        $Visao = $this->_request->getParam("visao");

        $grupologado = $this->_request->getParam("grupologado");

        /*
         * Valida��o - Se for componente da comiss�o ele n�o salva a vis�o
         * Regra o componente da comiss�o n�o pode alterar sua vis�o.
         */

        if ($grupologado != 118):

            $GravarVisao = array(// insert
                'idAgente' => $idAgente,
                'Visao' => $Visao,
                'Usuario' => $Usuario,
                'stAtivo' => 'A');

            try {
                $busca = VisaoDAO::buscarVisao($idAgente, $Visao);

                if (!$busca) {
                    $i = VisaoDAO::cadastrarVisao($GravarVisao);
                }
            } catch (Exception $e) {
                parent::message("Erro ao salvar a vis�o: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
            }


            // ================================================ FIM SALVAR vis�o ======================================================
            // ===================== IN�CIO SALVAR TITULA��O (�rea/SEGMENTO DO COMPONENTE DA COMISS�O) ================================


            $titular = $this->_request->getParam("titular");
            $areaCultural = $this->_request->getParam("areaCultural");
            $segmentoCultural = $this->_request->getParam("segmentoCultural");

            // s� salva �rea e segmento para a vis�o de Componente da Comiss�o e se os campos titular e areaCultural forem informados
            if ((int) $Visao == 210 && ((int) $titular == 0 || (int) $titular == 1) && !empty($areaCultural)) {
                $GravarComponente = array(// insert
                    'idAgente' => $idAgente,
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular,
                    'stConselheiro' => 'A');

                $AtualizarComponente = array(// update
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular
                        //'stConselheiro' => 'A' -- Qual caso de uso vai ativa e desativar?										
                );

                try {
                    // busca a titula��o do agente (titular/suplente de �rea cultural)
                    $busca = TitulacaoConselheiroDAO::buscarComponente($idAgente, $Visao);

                    if (!$busca) {
                        $i = TitulacaoConselheiroDAO::gravarComponente($GravarComponente);
                    } else {
                        $i = TitulacaoConselheiroDAO::atualizaComponente($idAgente, $AtualizarComponente);
                    }
                } catch (Exception $e) {
                    parent::message("Erro ao salvar a �rea e segmento: " . $e->getMessage(), $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
                }
            }

        // ============================= FIM SALVAR TITULA��O (�rea/SEGMENTO DO COMPONENTE DA COMISS�O) ===========================

        endif; // Fecha o if da regra do componente da comiss�o
        // =========================================== INICIO SALVAR ENDERE�OS ====================================================

        $cepEndereco = $this->_request->getParam("cep");
        $tipoEndereco = $this->_request->getParam("tipoEndereco");
        $ufEndereco = $this->_request->getParam("uf");
        $CidadeEndereco = $this->_request->getParam("cidade");
        $Endereco = $this->_request->getParam("logradouro");
        $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
        $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
        $numero = $this->_request->getParam("numero");
        $complemento = $this->_request->getParam("complemento");
        $bairro = $this->_request->getParam("bairro");
        $enderecoCorrespodencia = 1;

        try {

            $arrayEnderecos = array(
                'idAgente' => $idAgente,
                'Cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                'TipoEndereco' => $tipoEndereco,
                'UF' => $ufEndereco,
                'Cidade' => $CidadeEndereco,
                'Logradouro' => $Endereco,
                'Divulgar' => $divulgarEndereco,
                'TipoLogradouro' => $tipoLogradouro,
                'Numero' => $numero,
                'Complemento' => $complemento,
                'Bairro' => $bairro,
                'Status' => $enderecoCorrespodencia,
                'Usuario' => $Usuario
            );


            $insere = EnderecoNacionalDAO::gravarEnderecoNacional($arrayEnderecos);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o endere�o: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        // ============================================= FIM SALVAR ENDERE�OS ====================================================
        // =========================================== INICIO SALVAR TELEFONES ====================================================

        $tipoFone = $this->_request->getParam("tipoFone");
        $ufFone = $this->_request->getParam("ufFone");
        $dddFone = $this->_request->getParam("dddFone");
        $Fone = $this->_request->getParam("fone");
        $divulgarFone = $this->_request->getParam("divulgarFone");

        try {
            $arrayTelefones = array(
                'idAgente' => $idAgente,
                'TipoTelefone' => $tipoFone,
                'UF' => $ufFone,
                'DDD' => $dddFone,
                'Numero' => $Fone,
                'Divulgar' => $divulgarFone,
                'Usuario' => $Usuario
            );



            $insere = Telefone::cadastrar($arrayTelefones);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o telefone: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        // =========================================== FIM SALVAR TELEFONES ====================================================
        // =========================================== INICIO SALVAR EMAILS ====================================================			

        $tipoEmail = $this->_request->getParam("tipoEmail");
        $Email = $this->_request->getParam("email");
        $divulgarEmail = $this->_request->getParam("divulgarEmail");
        $enviarEmail = 1;

        try {
            $arrayEmail = array(
                'idAgente' => $idAgente,
                'TipoInternet' => $tipoEmail,
                'Descricao' => $Email,
                'Status' => $enviarEmail,
                'Divulgar' => $divulgarEmail,
                'Usuario' => $Usuario
            );

            $insere = Email::cadastrar($arrayEmail);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }

        // =========================================== FIM SALVAR EMAILS ====================================================
        // =========================================== INICIO SALVAR VINCULO ====================================================					
        try {
            // busca o dirigente vinculado ao cnpj/cpf
            $dadosDirigente = ManterAgentesDAO::buscarVinculados(null, null, $idAgente, $idAgenteGeral, $idAgenteGeral);

            // caso o agente n�o esteja vinculado, realizar� a vincula��o
            if (!$dadosDirigente) {
                // associa o dirigente ao cnpj/cpf
                $dadosVinculacao = array(
                    'idAgente' => $idAgente,
                    'idVinculado' => $idAgenteGeral,
                    'idVinculoPrincipal' => $idAgenteGeral,
                    'Usuario' => $Usuario
                );

                $vincular = ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);
            }
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        parent::message("Cadastro realizado com sucesso!", "agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
    }

// Final da fun��o salvar dirigentes

    /**
     * M�todo vinculadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function vinculadirigenteAction() {
        $this->autenticacao();
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = isset($auth->getIdentity()->IdUsuario) ? $auth->getIdentity()->IdUsuario : $auth->getIdentity()->usu_codigo ; // id do usu�rio logado
        $idAgenteGeral = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");
        
        try {
            // busca o dirigente vinculado ao cnpj/cpf
            $dadosDirigente = ManterAgentesDAO::buscarVinculados(null, null, $idDirigente, $idAgenteGeral, $idAgenteGeral);

            // caso o agente n�o esteja vinculado, realizar� a vincula��o
            if (count($dadosDirigente) == 0) {
                // associa o dirigente ao cnpj/cpf
                $dadosVinculacao = array(
                    'idAgente' => $idDirigente,
                    'idVinculado' => $idAgenteGeral,
                    'idVinculoPrincipal' => $idAgenteGeral,
                    'Usuario' => $Usuario
                );
                $vincular = ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);

                $Visao = 198; //Dirigente
                $GravarVisao = array(// insert
                    'idAgente' => $idDirigente,
                    'Visao' => $Visao,
                    'Usuario' => $Usuario,
                    'stAtivo' => 'A');
                $busca = VisaoDAO::buscarVisao($idDirigente, $Visao);
                if (!$busca){
                    $i = VisaoDAO::cadastrarVisao($GravarVisao);
                }
            }

            parent::message("Cadastrado realizado com sucesso! ", "agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agentes/visualizadirigente/id/" . $idAgenteGeral . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }

    /**
     * M�todo desvinculadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function desvinculadirigenteAction() {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usu�rio logado
        $idAgenteGeral = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");

        try {

            $vincular = new Vinculacao();

            #$where = "Idcaptacao = " . $where;

            $where = array('idAgente = ' . $idDirigente,
                'idVinculado = ' . $idAgenteGeral);

            $desvincula = $vincular->Desvincular($where);

            parent::message("Exclus�o realizada com sucesso! ", "agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agentes/visualizadirigente/id/" . $idAgenteGeral . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }

    /*     * ** FIM DIRIGENTES **************************************************************************** */

    /**
     * M�todo para realizar a buscar de agentes por cpf/cnpj ou por nome
     * @access public
     * @param void
     * @return void
     */
    public function buscaragenteAction() {
        $this->autenticacao();
// caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // deleta a m�scara
            $nome = $post->nome;

            try {
                // valida��o dos campos
                if (empty($cpf) && empty($nome)) {
                    throw new Exception("Dados obrigat�rios n�o informados:<br /><br />� necess�rio informar o CPF/CNPJ ou o Nome!");
                } else if (!empty($cpf) && strlen($cpf) != 11 && strlen($cpf) != 14) { // valida cnpj/cpf
                    throw new Exception("O CPF/CNPJ informado � inv�lido!");
                } else if (!empty($cpf) && strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) { // valida cpf
                    throw new Exception("O CPF informado � inv�lido!");
                } else if (!empty($cpf) && strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)) { // valida cnpj
                    throw new Exception("O CNPJ informado � inv�lido!");
                } else {
                    // redireciona para a p�gina com a busca dos dados com pagina��o
                    $this->_redirect("agentes/listaragente?cpf=" . $cpf . "&nome=" . $nome);
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->cpf = !empty($cpf) ? Validacao::mascaraCPFCNPJ($cpf) : ''; // caso exista, adiciona a m�scara
                $this->view->nome = $nome;
            }
        } // fecha if
    }

// fecha m�todo buscaragenteAction()

    /**
     * M�todo listaragente()
     * @access public
     * @param void
     * @return List
     */
    public function listaragenteAction() {
        $this->autenticacao();
        // recebe os dados via get
        $get = Zend_Registry::get('get');
        $cpf = $get->cpf;
        $nome = $get->nome;

        // realiza a busca por cpf e/ou nome
        $buscar = ManterAgentesDAO::buscarAgentes($cpf, $nome);

        if (!$buscar) {
            // redireciona para a p�gina de cadastro de agentes, e, exibe uma notifica��o relativa ao cadastro
            parent::message("Agente n�o cadastrado!<br /><br />Por favor, cadastre o mesmo no formul�rio abaixo!", "manteragentes/agentes?acao=cc&cpf=" . $cpf . "&nome=" . $nome, "ALERT");
        } else {
            // ========== IN�CIO PAGINA��O ==========
            // criando a pagina��o
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
            $paginator = Zend_Paginator::factory($buscar); // dados a serem paginados
            // p�gina atual e quantidade de �tens por p�gina
            $currentPage = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10); // 10 por p�gina
            // ========== FIM PAGINA��O ==========

            $this->view->buscar = $paginator;
            $this->view->qtdAgentes = count($buscar); // quantidade de agentes
        } // fecha else
    }

// fecha m�todo listaragenteAction()

    /**
     * M�todo abrirarquivo()
     * Abrir arquivo em bin�rio
     * @access public
     * @param void
     * @return void
     */
    public function abrirarquivoAction() {
        $this->autenticacao();
        $id = $this->_request->getParam("id");

        // Configura��o o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        $tbArquivo = new tbArquivo();

        // busca o arquivo
        $resultado = $tbArquivo->buscarArquivo($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->view->message = 'N�o foi poss�vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // l� os cabe�alhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private");

                $this->getResponse()->setBody($r->biArquivo);
            }
        }
    }

    /**
     * M�todo painelcredenciamento()
     * Painel do Coordenador de Pronac
     * @access public
     * @param void
     * @return void
     */
    public function painelcredenciamentoAction() {
        $this->autenticacao();
        $agentes = new Agentes();

        $nome = $this->_request->getParam('nome');
        $cpf = Mascara::delMaskCPF($this->_request->getParam('cpf'));

        // ========== IN�CIO PAGINA��O ==========
        // criando a pagina��o
        $buscar = $agentes->consultaPareceristasPainel($nome, $cpf);

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($buscar); // dados a serem paginados
        // p�gina atual e quantidade de �tens por p�gina
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(15);
        // ========== FIM PAGINA��O ==========

        $this->view->qtdpareceristas = count($buscar);
        $this->view->pareceristas = $paginator;

        $orgaos = new Orgaos();
        $this->view->orgaos = $orgaos->pesquisarTodosOrgaos();
    }

    /**
     * M�todo painelferias()
     * Painel do Coordenador de Parecer
     * @access public
     * @param void
     * @return void
     */
    public function painelferiasAction() {
        $this->autenticacao();
        $ano = date('Y');

        $tbAusencia = new TbAusencia();

        $dados = $tbAusencia->BuscarAusenciaPainel($ano);

        $totalDias = 0;

        foreach ($dados as $d) {
            if (($d->siAusencia == 0) OR ($d->siAusencia == 1)) {
                $totalDias = $totalDias + $d->qtdDias;
            }
        }

        $this->view->id = $this->_request->getParam("id");
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
    }

    /**
     * M�todo sempermissao()
     * @access public
     * @param void
     * @return void
     */
    public function sempermissaoAction() {
        $this->autenticacao();
    }

    /**
     * M�todo com a p�gina de altera��o de vis�o
     * @access public
     * @param void
     * @return void
     */
    public function alterarvisaoAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam('id');
        $GrupoAtivo = $this->GrupoAtivoSalic;

        // busca todas as vis�es
        $visoes = VisaoDAO::buscarVisao(null, null, true);
        $a = 0;
        $select = null;
        foreach ($visoes as $visaoGrupo) {
            if ($GrupoAtivo == 93 and ($visaoGrupo->idVerificacao == 209 or $visaoGrupo->idVerificacao == 216)) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 137 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 122 and ($visaoGrupo->idVerificacao == 210 or $visaoGrupo->idVerificacao == 216 or $GrupoAtivo == 123)) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 121) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }

        if ($GrupoAtivo == 1111) {
            $select[0]['idVerificacao'] = 144;
            $select[0]['Descricao'] = 'Proponente';
        }
        $this->view->visao = $select;

        // busca todas as vis�es do agente
        $visoesAgente = VisaoDAO::buscarVisao($idAgente);
        $b = 0;
        $selectAgente = null;
        foreach ($visoesAgente as $visaoGrupo) {
            if ($GrupoAtivo == 93 and ($visaoGrupo->idVerificacao == 209 or $visaoGrupo->idVerificacao == 216)) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 137 and $visaoGrupo->idVerificacao == 209) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 122 and ($visaoGrupo->idVerificacao == 210 or $visaoGrupo->idVerificacao == 216 or $GrupoAtivo == 123)) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 121) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 1111) {
                $selectAgente[$b]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaoGrupo->Descricao;
            }
            $b++;
        }
        $this->view->visaoAgente = $selectAgente;

        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $visaoAgente = $post->visaoAgente;

            try {

                // exclui todas as vis�es do agente
                VisaoDAO::excluirVisao($idAgente);

                // cadastra todas as vis�es do agente
                foreach ($visaoAgente as $visao) {
                    $dados = array(
                        'idAgente' => $idAgente,
                        'Visao' => $visao,
                        'Usuario' => $this->getIdUsuario, // c�digo do usu�rio logado
                        'stAtivo' => 'A');
                    VisaoDAO::cadastrarVisao($dados);
                }

                parent::message("Altera��o realizada com sucesso!", "agentes/alterarvisao/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao efetuar altera��o das vis�es do agente! " . $e->getMessage(), "agentes/alterarvisao/id/" . $idAgente, "ERROR");
            }
        }

        $this->view->id = $idAgente;
    }

    public function infoAdicionaisAction() {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo; // Busca o perfil do usu�rio
        
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $tbAgenteFisico = new tbAgenteFisico();
        $result = $tbAgenteFisico->buscar(array('idAgente = ?'=>$idAgente))->current();
        $this->view->dadosAdicionais = $result;
    }

    public function salvarInfoAdicionaisAction() {
        $post = Zend_Registry::get('post');

        $data = explode('/', $post->dtNascimento);
        $dtNascimento = $data[2].'-'.$data[1].'-'.$data[0];
        
        $processo = Mascara::delMaskProcesso($post->processo);
        
        $dados = array(
            'idAgente'                  => $post->agente,
            'stSexo'                    => $post->sexo,
            'stEstadoCivil'             => $post->estadoCivil,
            'stNecessidadeEspecial'     => $post->necEspecial,
            'nmMae'                     => $post->nomeMae,
            'nmPai'                     => $post->nomePai,
            'dtNascimento'              => $dtNascimento,
            'stCorRaca'                 => $post->raca,
            'nrIdentificadorProcessual' => $processo
        );
        
        $tbAgenteFisico = new tbAgenteFisico();
        
        $result = $tbAgenteFisico->buscar(array('idAgente = ?'=>$post->agente));
        
        try {
            
            if(count($result) > 0){
                $msg = 'alterados';
                $tbAgenteFisico->alterarDados($dados, $post->agente);
            } else {
                $msg = 'cadastrados';
                $tbAgenteFisico->inserir($dados);
            }
            
            parent::message("Dados $msg com sucesso!", "agentes/info-adicionais/id/" . $post->agente, "CONFIRM");
            
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera��o!", "agentes/info-adicionais/id/" . $post->agente, "ERROR");
        }
        
    }

    public function naturezaAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $tbVerificacao = new Verificacao();
        $direito = $tbVerificacao->combosNatureza(7);
        $this->view->direito = $direito;

        $esfera = $tbVerificacao->combosNatureza(8);
        $this->view->esfera = $esfera;

        $poder = $tbVerificacao->combosNatureza(9);
        $this->view->poder = $poder;

        $administracao = $tbVerificacao->combosNatureza(10);
        $this->view->administracao = $administracao;

        $Natureza = new Natureza();
        $result = $Natureza->buscar(array('idAgente = ?'=>$idAgente))->current();
        $this->view->dadosNatureza = $result;
    }

    public function salvarNaturezaAction() {
        $post = Zend_Registry::get('post');
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idUsuario = isset($auth->getIdentity()->IdUsuario) ? $auth->getIdentity()->IdUsuario : $auth->getIdentity()->usu_codigo;

        $dados = array(
//            'idNatureza' => $post->agente,
            'idAgente' => $post->agente,
            'Direito' => isset($post->direito) ? $post->direito : 0,
            'Esfera' => isset($post->esfera) ? $post->esfera : 0,
            'Poder' => isset($post->poder) ? $post->poder : 0,
            'Administracao' => isset($post->administracao) ? $post->administracao : 0,
            'Usuario' => $idUsuario
        );
        
        $Natureza = new Natureza();
        $result = $Natureza->buscar(array('idAgente = ?'=>$post->agente));
        try {
            if(count($result) > 0){
                $result = $result->current();
                $msg = 'alterados';
                $Natureza->alterarDados($dados, $result->idNatureza);
            } else {
                $msg = 'cadastrados';
                $Natureza->inserir($dados);
            }
            parent::message("Dados $msg com sucesso!", "agentes/natureza/id/" . $post->agente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera��o!", "agentes/natureza/id/" . $post->agente, "ERROR");
        }
    }

    public function areaCulturalAction() {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $Area = new Area();
        $areas = $Area->buscar(array(), array('Descricao'));
        $this->view->Areas = $areas;

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $areaCadastrada = $tbTitulacaoConselheiro->buscar(array('idAgente = ?'=>$idAgente));
        $this->view->AreaCadastrada = $areaCadastrada;
    }

    public function salvarAreaCulturalAction() {
        $post = Zend_Registry::get('post');

        $dados = array(
            'cdArea' => $post->area,
            'cdSegmento' => !empty($post->segmento) ? $post->segmento : 0
        );

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $result = $tbTitulacaoConselheiro->buscar(array('idAgente = ?'=>$post->agente));

        try {
            if(count($result) > 0){
                $result = $result->current();
                $msg = 'alterados';
                $tbTitulacaoConselheiro->alterarDados($dados, $result->idAgente);
            } else {
                $msg = 'cadastrados';
                $tbTitulacaoConselheiro->inserir($dados);
            }
            parent::message("Dados $msg com sucesso!", "agentes/area-cultural/id/" . $post->agente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera��o!", "agentes/area-cultural/id/" . $post->agente, "ERROR");
        }
    }

}

// fecha class
