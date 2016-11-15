<?php
/**
 * RecursoController
 * @author Equipe RUP - Politec
 * @since 20/07/2010 - Alterado em 17/09/2013 (Jefferson Alessandro)
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

class RecursoController extends MinC_Controller_Action_Abstract
{
	private $idUsuario = 0;
    private $idOrgao = 0;
    private $idPerfil = 0;
    private $intTamPag = 10;

	/**
	 * Reescreve o m�todo init()
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
	    $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $this->idUsuario = $auth->getIdentity()->usu_codigo; // usu�rio logado

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idOrgao = $GrupoAtivo->codOrgao;
        $this->idPerfil = $GrupoAtivo->codGrupo;

		// autentica��o e permiss�es zend (AMBIENTE MINC)
		$PermissoesGrupo = array();
		$PermissoesGrupo[] = 93; // Coordenador de Parecer
		$PermissoesGrupo[] = 94; // Parecerista
		$PermissoesGrupo[] = 103; // Coordenador de An�lise
		$PermissoesGrupo[] = 110; // T�cnico de An�lise
		$PermissoesGrupo[] = 118; // Componente da Comiss�o
		$PermissoesGrupo[] = 127; // Coordenador - Geral de An�lise (Ministro)
		parent::perfil(1, $PermissoesGrupo);

		parent::init();
	} // fecha m�todo init()


	/**
	 * Fluxo inicial
	 * @access public
	 * @param void
	 * @return void
	 */
	public function indexAction()
	{
        //FUN��O ACESSADA SOMENTE PELOS PERFIS DE COORD. GERAL DE AN�LISE E COORD. DE AN�LISE.
        if($this->idPerfil != 103 && $this->idPerfil != 127){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if($ordem == "ASC") {
                $novaOrdem = "DESC";
            }else {
                $novaOrdem = "ASC";
            }
        }else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;

        } else {
            $campo = null;
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) $pag = $get->pag;
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();

        if(isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])){
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso = ?'] = 1; // 1=Solicitado pelo proponente
                    break;
                case 'emanalise':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso in (?)'] = array(3,4,7); // // 3=Encaminhado do MinC para a  Unidade de An�lise; 4=Encaminhado para Parecerista /  T�cnico; 7=Encaminhado para o Componente da Comiss�o
                    $this->view->nmPagina = 'Em An�lise';
                    break;
                case 'analisados':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso in (?)'] = array(6,10); // 6=Devolvido da Unidade de Analise para o MinC; 10=Devolvido pelo Tecnico para o Coordenador
                    $this->view->nmPagina = 'Analisados';
                    break;
            }
        } else {
            $this->view->nmPagina = 'Aguardando An�lise';
            $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
            $where['a.siRecurso = ?'] = 1; // 1=Solicitado pelo proponente
        }

        if((isset($_GET['pronac']) && !empty($_GET['pronac']))){
            $where['b.AnoProjeto+b.Sequencial = ?'] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        $Orgaos = new Orgaos();
        $idSecretaria = $Orgaos->buscar(array('codigo = ?'=>$this->idOrgao))->current();

        if(isset($idSecretaria) && !empty($idSecretaria)){
            if($idSecretaria->idSecretaria == 251){
                $where['b.Area <> ?'] = 2;
            } else if($idSecretaria->idSecretaria == 160){
                $where['b.Area = ?'] = 2;
            } else {
                $where['b.Area = ?'] = 0;
            }
        }

        $tbRecurso = New tbRecurso();
        $total = $tbRecurso->painelRecursos($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbRecurso->painelRecursos($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitulares();

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
	}

    public function imprimirRecursosAction()
	{
        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if($ordem == "ASC") {
                $novaOrdem = "DESC";
            }else {
                $novaOrdem = "ASC";
            }
        }else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;

        } else {
            $campo = null;
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) $pag = $get->pag;
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();

        if(isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])){
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso = ?'] = 1; // 1=Solicitado pelo proponente
                    break;
                case 'emanalise':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso in (?)'] = array(3,4,7); // // 3=Encaminhado do MinC para a  Unidade de An�lise; 4=Encaminhado para Parecerista /  T�cnico; 7=Encaminhado para o Componente da Comiss�o
                    $this->view->nmPagina = 'Em An�lise';
                    break;
                case 'analisados':
                    $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
                    $where['a.siRecurso in (?)'] = array(6,10); // 6=Devolvido da Unidade de Analise para o MinC; 10=Devolvido pelo Tecnico para o Coordenador
                    $this->view->nmPagina = 'Analisados';
                    break;
            }
        } else {
            $this->view->nmPagina = 'Aguardando An�lise';
            $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
            $where['a.siRecurso = ?'] = 1; // 1=Solicitado pelo proponente
        }

        if((isset($_GET['pronac']) && !empty($_GET['pronac']))){
            $where['b.AnoProjeto+b.Sequencial = ?'] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        $tbRecurso = New tbRecurso();
        $total = $tbRecurso->painelRecursos($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbRecurso->painelRecursos($where, $order, $tamanho, $inicio);

        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
	}

    public function avaliarRecursoAction() {
        $idRecurso = $_GET['recurso'];

        $tbRecurso = new tbRecurso();
        $r = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$idRecurso))->current();
        if($r->tpSolicitacao == 'PI'){
            $Parecer = new Parecer();
            $dadosParecer = $Parecer->statusDeAvaliacao($r->IdPRONAC);
            $this->view->statusDeAvaliacao = $dadosParecer;
       }

        if($r){
            $Projetos = new Projetos();
            $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $r->IdPRONAC))->current();

            $this->view->recurso = $r;
            $this->view->projeto = $p;
        } else {
            parent::message('Nenhum registro encontrado.', "recurso", "ERROR");
        }
    }

    public function salvarAvaliacaoAction() {
        $idRecurso = $_POST['idRecurso'];

        $tbRecurso = new tbRecurso();
        $r = $tbRecurso->find(array('idRecurso = ?'=>$idRecurso))->current();
        $stEstado = 0;
        $stFecharAnalise = 0;

        if($r){
            $Projetos = new Projetos();
            $dp = $Projetos->buscar(array('IdPRONAC = ?'=>$r->IdPRONAC))->current();
            $pronac = $dp->AnoProjeto.$dp->Sequencial;

            $r->stAtendimento = $_POST['stAtendimento'];
            $r->dsAvaliacao = $_POST['dsAvaliacao'];
            $r->dtAvaliacao = new Zend_Db_Expr('GETDATE()');
            $r->idAgenteAvaliador = $this->idUsuario;

            if($_POST['stAtendimento'] == 'I'){
                $r->siRecurso = 2; //2=Solicita��o indeferida
                $r->stEstado = 1;

                //BUSCA A SITUA��O ANTERIOR DO PROJETO ANTES DA SOLICITA��O RECURSO
                $historicoSituacao = new HistoricoSituacao();
                $dadosHist = $historicoSituacao->buscarSituacaoAnterior($pronac);

                //ATUALIZA A SITUA��O DO PROJETO
                $w = array();
                $w['situacao'] = $dadosHist->Situacao;
                $w['ProvidenciaTomada'] = 'Recurso indeferido.';
                $w['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
                $w['Logon'] = $this->idUsuario;
                $where = "IdPRONAC = $dp->IdPRONAC";
                $Projetos->update($w, $where);

            } else {
                if($_POST['vinculada'] == 262){
                    $r->siRecurso = 4; //4=Enviado para An�lise T�cnica (SEFIC)

                } else if($_POST['vinculada'] == 400) {
                    $stEstado = 1;
                    $stFecharAnalise = 1;
                    $r->siRecurso = 7; //7=CNIC
                    $r->idAgenteAvaliador = $_POST['destinatario'];

                } else {
                    $r->siRecurso = 3; //3=Enviado para o coordenador de parecer

                    //ATUALIZA A SITUA��O DO PROJETO
                    $w = array();
                    $w['situacao'] = 'B11';
                    $w['ProvidenciaTomada'] = 'Recurso encaminhado para avalia��o da unidade vinculada.';
                    $w['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
                    $w['Logon'] = $this->idUsuario;
                    $where = "IdPRONAC = $dp->IdPRONAC";
                    $Projetos->update($w, $where);

                    //SE O RECURSO SE TRATAR DE PROJETO INDEFERIDO, OS DADOS DAS PLANILHAS ABAIXO DEVEM SER DELETADAS.
                    if($r->tpSolicitacao == 'PI'){
                        //DELETAR DADOS
                        $tbAnaliseAprovacao = new tbAnaliseAprovacao();
                        $tbAnaliseAprovacao->delete(array('IdPRONAC = ?' => $r->IdPRONAC, 'tpAnalise = ?' => 'CO'));

                        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                        $tbPlanilhaAprovacao->delete(array('IdPRONAC = ?' => $r->IdPRONAC, 'tpPlanilha = ?' => 'CO', 'stAtivo = ?' => 'S'));

                        $Parecer = new Parecer();
                        $Parecer->delete(array('IdPRONAC = ?' => $r->IdPRONAC, 'stAtivo = ?' => 1, 'idTipoAgente = ?' => 6));

                        $Enquadramento = new Enquadramento();
                        $Enquadramento->delete(array('IdPRONAC = ?' => $r->IdPRONAC));
                    }
                }
            }
            $r->save();

            if($_POST['stAtendimento'] == 'D'){
                $tbDistribuirProjeto = new tbDistribuirProjeto();
                $dados = array(
                    'IdPRONAC' => $r->IdPRONAC,
                    'idUnidade' => $_POST['vinculada'],
                    'dtEnvio' => new Zend_Db_Expr('GETDATE()'),
                    'idAvaliador' => isset($_POST['destinatario']) ? $_POST['destinatario'] : null,
                    'stEstado' => $stEstado,
                    'stFecharAnalise' => $stFecharAnalise,
                    'idUsuario' => $this->idUsuario
                );
                $tb = $tbDistribuirProjeto->inserir($dados);
            }
            parent::message('Dados salvos com sucesso!', "recurso", "CONFIRM");

        } else {
            parent::message('Nenhum registro encontrado.', "recurso", "ERROR");
        }

    }

    public function buscarDestinatariosAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $vinculada = $_POST['vinculada'];
        $idPronac = $_POST['idPronac'];

        $a = 0;
        $dadosUsuarios = array();

        if($vinculada == 262){
            $dados = array();
            $dados['sis_codigo = ?'] = 21;
            $dados['uog_status = ?'] = 1;
            $dados['gru_codigo = ?'] = 110;
            $dados['org_superior = ?'] = 251;

            $vw = new vwUsuariosOrgaosGrupos();
            $result = $vw->buscar($dados, array('usu_nome'));

            if(count($result) > 0){
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['usu_codigo'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['usu_nome']);
                    $a++;
                }
                $jsonEncode = json_encode($dadosUsuarios);
                echo json_encode(array('resposta'=>true,'conteudo'=>$dadosUsuarios));

            } else {
                echo json_encode(array('resposta'=>false));
            }

        } else { //CNIC
            $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
            $result = $tbTitulacaoConselheiro->buscarConselheirosTitulares();

            if(count($result) > 0){
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['id'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['nome']);
                    $a++;
                }
                $jsonEncode = json_encode($dadosUsuarios);
                echo json_encode(array('resposta'=>true,'conteudo'=>$dadosUsuarios));

            } else {
                echo json_encode(array('resposta'=>false));
            }
        }
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function painelRecursosAction() { //Tela do Coordenador de Parecer

        $auth = Zend_Auth::getInstance();
        $ag = new Agente_Model_DbTable_Agentes();
        $dadosAgente = $ag->buscar(array('CNPJCPF = ?'=>$auth->getIdentity()->usu_identificacao))->current();

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if($ordem == "ASC") {
                $novaOrdem = "DESC";
            }else {
                $novaOrdem = "ASC";
            }
        }else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;

        } else {
            $campo = null;
            $order = array(5); //Data de Envio
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) $pag = $get->pag;
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.stEstado = ?'] = 0;
        $where['a.stFecharAnalise = ?'] = 0;
        if($this->idOrgao == 160){
            $where['a.idUnidade in (?)'] = array(160,171);
        } else {
            $where['a.idUnidade = ?'] = $this->idOrgao;
        }
        $where['d.stEstado = ?'] = 0;

        if(isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])){
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    if($this->idPerfil == 93){ //Coord. de Parecer
                        $where['d.siRecurso = ?'] = 3;
                        $where['a.idAvaliador IS NULL'] = '';
                    } else if($this->idPerfil == 94){
                        $where['d.siRecurso = ?'] = 4;
                        $where['a.idAvaliador = ?'] = count($dadosAgente)>0 ? $dadosAgente->idAgente : 0;
                    }
                    break;
                case 'analisados':
                    $where['d.siRecurso = ?'] = 5;
                    $this->view->nmPagina = 'Analisados';
                    break;
            }
        } else {
            $this->view->nmPagina = 'Aguardando An�lise';
            if($this->idPerfil == 93){
                $where['d.siRecurso = ?'] = 3;
                $where['a.idAvaliador IS NULL'] = '';
            } else if($this->idPerfil == 94 || $this->idPerfil == 110){
                $where['d.siRecurso = ?'] = 4;

                if($this->idPerfil == 110){
                    $where['a.idAvaliador = ?'] = $this->idUsuario;
                } else {
                    $where['a.idAvaliador = ?'] = count($dadosAgente)>0 ? $dadosAgente->idAgente : 0;
                }
            }
        }

        if((isset($_GET['pronac']) && !empty($_GET['pronac']))){
            $where['b.AnoProjeto+b.Sequencial = ?'] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        $tbDistribuirProjeto = New tbDistribuirProjeto();
        $total = $tbDistribuirProjeto->painelRecursos($where, $order, null, null, true, $this->idPerfil);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbDistribuirProjeto->painelRecursos($where, $order, $tamanho, $inicio, false, $this->idPerfil);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
        $this->view->idPerfil      = $this->idPerfil;
        $this->view->idOrgao       = $this->idOrgao;
    }

    public function encaminharRecursoAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $vinculada = $this->idOrgao;

        $post = Zend_Registry::get('post');
        $idAvaliador = (int) $post->parecerista;
        $idDistProj = (int) $post->idDistProj;
        $idRecurso = (int) $post->idRecurso;

        //Atualiza a tabela tbDistribuirProjeto
        $dados = array();
        $dados['idAvaliador'] = $idAvaliador;
        $dados['idUsuario'] = $this->idUsuario;
        $dados['dtDistribuicao'] = new Zend_Db_Expr('GETDATE()');
        $where = "idDistribuirProjeto = $idDistProj";
        $tbDistribuirProjeto = new tbDistribuirProjeto();
        $return = $tbDistribuirProjeto->update($dados, $where);

        //Atualiza a tabela tbRecurso
        $dados = array();
        $dados['siRecurso'] = 4; // Enviado para an�lise t�cnica
        $where = "idRecurso = $idRecurso";
        $tbRecurso = new tbRecurso();
        $return2 = $tbRecurso->update($dados, $where);

        if($return && $return2){
            echo json_encode(array('resposta'=>true));
        } else {
            echo json_encode(array('resposta'=>false));
        }
        die();

    }

    public function visualizarRecursoAction(){


        if($this->idPerfil != 93 && $this->idPerfil != 94 && $this->idPerfil != 103 && $this->idPerfil != 127){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $get = Zend_Registry::get('get');
        $idRecurso = (int) $get->id;

        $tbRecurso = new tbRecurso();
        $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$idRecurso))->current();
        $this->view->dados = $dados;

        $this->view->nmPagina = '';

        if($dados->siFaseProjeto == 2){
            if($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Projeto Indeferido';
                if($dados->tpSolicitacao == 'EO'){
                    $this->view->nmPagina = 'Enquadramento e Or�amento';
                } else if($dados->tpSolicitacao == 'OR'){
                    $this->view->nmPagina = 'Or�amento';
                }

                //ATUALIZA OS DADOS DA TABELA tbAnaliseAprovacao
//                $e = array();
//                $e['stDistribuicao'] = 'I'; // I=Inativo
//                $w = "idPRONAC = $dados->IdPRONAC";
//                $tbDistribuicaoProjetoComissao = new tbDistribuicaoProjetoComissao();
//                $tbDistribuicaoProjetoComissao->update($e, $w);

                $PlanoDistribuicaoProduto = new Proposta_model_DbTable_PlanoDistribuicaoProduto();
                $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                $this->view->produtos = $dadosProdutos;

                $tipoDaPlanilha = 2; // 2=Planilha Aprovada Parecerista
                if($dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                    $tipoDaPlanilha = 4; // 4=Cortes Or�ament�rios Aprovados
                }
                $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, $tipoDaPlanilha);
                $this->view->planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoDaPlanilha);
            }
        }
        if($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI'){
            if($dados->tpSolicitacao == 'EN'){
                $this->view->nmPagina = 'Enquadramento';
            } else if($dados->tpSolicitacao == 'EO'){
                $this->view->nmPagina = 'Enquadramento e Or�amento';
            } else if($dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Or�amento';
            } else {
                $this->view->nmPagina = 'Projeto Indeferido';
            }

            $Projetos = new Projetos();
            $this->view->projetosEN = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

            $this->view->combosegmentosculturais = Segmentocultural::buscarSegmento($this->view->projetosEN->cdArea);

            $parecer = new Parecer();
            $this->view->Parecer = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer in (?)' => array(1,7), 'stAtivo = ?' => 1))->current();
        }

        //DADOS DO PROJETO
        $Projetos = new Projetos();
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->IdPRONAC))->current();
        $this->view->projeto = $p;
    }

    public function encaminharRecursoChecklistAction() {
        if($this->idPerfil != 93 && $this->idPerfil != 94 && $this->idPerfil != 103 && $this->idPerfil != 127){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $get = Zend_Registry::get('get');
        $idRecurso = (int) $get->id;

        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $idNrReuniao = $raberta['idNrReuniao'];

        //Atualiza a tabela tbRecurso
        $dados = array();
        $dados['siRecurso'] = 9; // Encaminhado pelo sistema para o Checklist de Publica��o
        $dados['idNrReuniao'] = $idNrReuniao;
        $where = "idRecurso = $idRecurso";
        $tbRecurso = new tbRecurso();
        $return = $tbRecurso->update($dados, $where);

        if(!$return){
            parent::message("N�o foi poss�vel encaminhar o recurso para o Checklist de Publica��o", "recurso?tipoFiltro=analisados", "ERROR");
        }
        parent::message("Recurso encaminhado com sucesso!", "recurso?tipoFiltro=analisados", "CONFIRM");
    }

    public function formAvaliarRecursoAction(){

        $mapperArea = new Agente_Model_AreaMapper();

        if($this->idPerfil != 94 && $this->idPerfil != 110){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $get = Zend_Registry::get('get');
        $idRecurso = (int) $get->id;

        $tbRecurso = new tbRecurso();
        $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$idRecurso))->current();
        $this->view->dados = $dados;

        $this->view->nmPagina = '';

        if($dados->siFaseProjeto == 2){
            if($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Projeto Indeferido';
                if($dados->tpSolicitacao == 'EO'){
                    $this->view->nmPagina = 'Enquadramento e Or�amento';
                } else if($dados->tpSolicitacao == 'OR'){
                    $this->view->nmPagina = 'Or�amento';
                }

                //ATUALIZA OS DADOS DA TABELA tbAnaliseAprovacao
                $e = array();
                $e['stDistribuicao'] = 'I'; // I=Inativo
                $w = "idPRONAC = $dados->IdPRONAC";
                $tbDistribuicaoProjetoComissao = new tbDistribuicaoProjetoComissao();
                $tbDistribuicaoProjetoComissao->update($e, $w);

                $PlanoDistribuicaoProduto = new Proposta_model_DbTable_PlanoDistribuicaoProduto();
                $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                $this->view->produtos = $dadosProdutos;

                $tipoDaPlanilha = 2; // 2=Planilha Aprovada Parecerista
                if($dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                    $tipoDaPlanilha = 4; // 4=Cortes Or�ament�rios Aprovados
                }
                $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, $tipoDaPlanilha);
                $this->view->planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoDaPlanilha);
            }
        }
        if($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI'){
            if($dados->tpSolicitacao == 'EN'){
                $this->view->nmPagina = 'Enquadramento';
            } else if($dados->tpSolicitacao == 'EO'){
                $this->view->nmPagina = 'Enquadramento e Or�amento';
            } else if($dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Or�amento';
            } else {
                $this->view->nmPagina = 'Projeto Indeferido';
            }

            $Projetos = new Projetos();
            $this->view->projetosEN = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

            $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo',  'descricao');
            $this->view->combosegmentosculturais = Segmentocultural::buscarSegmento($this->view->projetosEN->cdArea);

            $parecer = new Parecer();
            $this->view->Parecer = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer = ?' => 7, 'stAtivo = ?' => 1))->current();
        }

        //DADOS DO PROJETO
        $Projetos = new Projetos();
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->IdPRONAC))->current();
        $this->view->projeto = $p;
    }

    public function salvarEnquadramentoAction()
	{
        //ESSA FUNCAO TAMBEM E UTILIZADA A MESMA FUNCAO PARA AVALIAR O ENQUADRAMENTO DO PROJETO.
        if($this->idPerfil != 94 && $this->idPerfil != 110){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idPronac = $_POST['idPronac'];
        $idRecurso = $_POST['idRecurso'];
        $areaCultural = isset($_POST['areaCultural']) ? $_POST['areaCultural'] : null;
        $segmentoCultural = isset($_POST['segmentoCultural']) ?  $_POST['segmentoCultural'] : null;
        $enquadramentoProjeto = $_POST['enquadramentoProjeto'];
        $parecerProjeto = $_POST['parecerProjeto'];
        $dsParecer = $_POST['dsParecer'];

        try {
            //ATUALIAZA A �REA E SEGMENTO DO PROJETO
            $d = array();
            if(isset($_POST['areaCultural'])){
                $d['Area'] = $areaCultural;
            }
            if(isset($_POST['segmentoCultural'])){
                $d['Segmento'] = $segmentoCultural;
            }
            $where = "IdPRONAC = $idPronac";
            $Projetos = new Projetos();
            if($parecerProjeto == 2){
                $Projetos->update($d, $where);
            }

            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac));
            if(count($dadosProjeto)>0){
                //CADASTRA OU ATUALIZA O ENQUADRAMENTO DO PROJETO
                $enquadramentoDAO = new Enquadramento();
                $dadosEnquadramento = array(
                    'IdPRONAC'=> $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial'=> $dadosProjeto[0]->Sequencial,
                    'Enquadramento' => $enquadramentoProjeto,
                    'DtEnquadramento' => MinC_Db_Expr::date(),
                    'Observacao' => '',
                    'Logon' => $idusuario
                );
                $whereBuscarDados  = array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial);
                $buscarEnquadramento = $enquadramentoDAO->buscar($whereBuscarDados);
                if(count($buscarEnquadramento) > 0){
                    $buscarEnquadramento = $buscarEnquadramento->current();
                    $whereUpdate = 'IdEnquadramento = '.$buscarEnquadramento->IdEnquadramento;
                    $alteraEnquadramento = $enquadramentoDAO->alterar($dadosEnquadramento, $whereUpdate);
                } else {
                    $insereEnquadramento = $enquadramentoDAO->inserir($dadosEnquadramento);
                }
                $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

                //CADASTRA OU ATUALIZA O PARECER DO TECNICO
                $parecerDAO = new Parecer();
                $dadosParecer = array(
                    'idPRONAC' => $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial' => $dadosProjeto[0]->Sequencial,
                    'TipoParecer' => 7,
                    'ParecerFavoravel' => $parecerProjeto,
                    'DtParecer' => MinC_Db_Expr::date(),
                    'NumeroReuniao' => null,
                    'ResumoParecer' => $dsParecer,
                    'SugeridoReal' => 0,
                    'Atendimento' => 'S',
                    'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
                    'stAtivo' => 1,
                    'idTipoAgente' => 1,
                    'Logon' => $idusuario
                );

                foreach ($dadosParecer as $dp) {
                    $parecerAntigo = array(
                        'Atendimento' => 'S',
                        'stAtivo' => 0
                    );
                    $whereUpdateParecer = 'IdPRONAC = '.$idPronac;
                    $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);
                }

                $buscarParecer = $parecerDAO->buscar(array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial, 'TipoParecer = ?' => 7, 'idTipoAgente = ?' =>1));
                if(count($buscarParecer) > 0){
                    $buscarParecer = $buscarParecer->current();
                    $whereUpdateParecer = 'IdParecer = '.$buscarParecer->IdParecer;
                    $alteraParecer = $parecerDAO->alterar($dadosParecer, $whereUpdateParecer);
                } else {
                    $insereParecer = $parecerDAO->inserir($dadosParecer);
                }
            }

            if(isset($_POST['finalizarAvaliacao']) && $_POST['finalizarAvaliacao'] == 1){

                $tbDistribuirProjeto = new tbDistribuirProjeto();
                $dDP = $tbDistribuirProjeto->buscar(array('IdPRONAC = ?'=>$idPronac, 'stEstado = ?'=>0, 'tpDistribuicao = ?'=>'A'));

                if(count($dDP)>0){
                    //ATUALIZA A TABELA tbDistribuirProjeto
                    $dadosDP = array();
                    $dadosDP['dtDevolucao'] = new Zend_Db_Expr('GETDATE()');
                    $whereDP = "idDistribuirProjeto = ".$dDP[0]->idDistribuirProjeto;
                    $tbDistribuirProjeto = new tbDistribuirProjeto();
                    $x = $tbDistribuirProjeto->update($dadosDP, $whereDP);

                    $siRecurso = 5; //Devolvido da an�lise t�cnica
                    if($this->idPerfil == 110){
                        $siRecurso = 10; //Devolver para Coordenador do MinC
                    }
                    //ATUALIZA A TABELA tbRecurso
                    $dados = array();
                    $dados['siRecurso'] = $siRecurso;
                    $where = "idRecurso = $idRecurso";
                    $tbRecurso = new tbRecurso();
                    $tbRecurso->update($dados, $where);
                }
                parent::message("A avalia��o do recurso foi finalizada com sucesso! ", "recurso/painel-recursos", "CONFIRM");
            }

            parent::message("Dados salvos com sucesso!", "recurso/form-avaliar-recurso?id=$idRecurso", "CONFIRM");

        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "recurso/form-avaliar-recurso?id=$idRecurso", "ERROR");
        }
	}

    public function componenteComissaoSalvarEnquadramentoAction()
	{
        if($this->idPerfil != 118){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idPronac = $_POST['idPronac'];
        $idRecurso = $_POST['idRecurso'];
        $areaCultural = $_POST['areaCultural'];
        $segmentoCultural = $_POST['segmentoCultural'];
        $enquadramentoProjeto = $_POST['enquadramentoProjeto'];
        $parecerProjeto = $_POST['parecerProjeto'];
        $dsParecer = $_POST['dsParecer'];

        if($parecerProjeto == 1){ //1=N�o; 2=Sim
            $situacaoProjeto = 'D14';
            $providenciaProjeto = 'Recurso indeferido na CNIC pelo componente da comiss�o.';
            $stAnalise = 'IC';
        } else {
            $situacaoProjeto = 'D03';
            $providenciaProjeto = 'Recurso deferido na CNIC pelo componente da comiss�o.';
            $stAnalise = 'AC';
        }

        try {
            //ATUALIAZA A SITUA��O, �REA E SEGMENTO DO PROJETO
            $d = array();
            $d['situacao'] = $situacaoProjeto;
            $d['ProvidenciaTomada'] = $providenciaProjeto;
            if(isset($_POST['areaCultural'])){
                $d['Area'] = $areaCultural;
            }
            if(isset($_POST['segmentoCultural'])){
                $d['Segmento'] = $segmentoCultural;
            }
            $where = "IdPRONAC = $idPronac";
            $Projetos = new Projetos();
            $Projetos->update($d, $where);

	    $Projetos->alterarSituacao($idPronac, null, $d['situacao'], $d['ProvidenciaTomada']);

            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac));
            if(count($dadosProjeto)>0){
                //CADASTRA OU ATUALIZA O ENQUADRAMENTO DO PROJETO
                $enquadramentoDAO = new Enquadramento();
                $dadosEnquadramento = array(
                    'IdPRONAC'=> $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial'=> $dadosProjeto[0]->Sequencial,
                    'Enquadramento' => $enquadramentoProjeto,
                    'DtEnquadramento' => MinC_Db_Expr::date(),
                    'Observacao' => '',
                    'Logon' => $idusuario
                );
                $whereBuscarDados  = array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial);
                $buscarEnquadramento = $enquadramentoDAO->buscar($whereBuscarDados);
                if(count($buscarEnquadramento) > 0){
                    $buscarEnquadramento = $buscarEnquadramento->current();
                    $whereUpdate = 'IdEnquadramento = '.$buscarEnquadramento->IdEnquadramento;
                    $alteraEnquadramento = $enquadramentoDAO->alterar($dadosEnquadramento, $whereUpdate);
                } else {
                    $insereEnquadramento = $enquadramentoDAO->inserir($dadosEnquadramento);
                }
                $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

                //CADASTRA OU ATUALIZA O PARECER DO TECNICO
                $parecerDAO = new Parecer();
                $dadosParecer = array(
                    'idPRONAC' => $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial' => $dadosProjeto[0]->Sequencial,
                    'TipoParecer' => 7,
                    'ParecerFavoravel' => $parecerProjeto,
                    'DtParecer' => MinC_Db_Expr::date(),
                    'NumeroReuniao' => null,
                    'ResumoParecer' => $dsParecer,
                    'SugeridoReal' => 0,
                    'Atendimento' => 'S',
                    'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
                    'stAtivo' => 1,
                    'idTipoAgente' => 6,
                    'Logon' => $idusuario
                );

                $buscarParecer = $parecerDAO->buscar(array('IdPRONAC = ?' => $idPronac));
                foreach ($dadosParecer as $dp) {
                    $parecerAntigo = array(
                        'Atendimento' => 'S',
                        'stAtivo' => 0
                    );
                    $whereUpdateParecer = 'IdPRONAC = '.$idPronac;
                    $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);
                }
                
                $buscarParecer = $parecerDAO->buscar(array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial, 'TipoParecer = ?' => 7, 'idTipoAgente = ?' =>6));
                if(count($buscarParecer) > 0){
                    $buscarParecer = $buscarParecer->current();
                    $whereUpdateParecer = 'IdParecer = '.$buscarParecer->IdParecer;
                    $alteraParecer = $parecerDAO->alterar($dadosParecer, $whereUpdateParecer);
                } else {
                    $insereParecer = $parecerDAO->inserir($dadosParecer);
                }
            }

            if(isset($_POST['finalizarAvaliacao']) && $_POST['finalizarAvaliacao'] == 1){

                $tbDistribuirProjeto = new tbDistribuirProjeto();
                $dDP = $tbDistribuirProjeto->buscar(array('IdPRONAC = ?'=>$idPronac, 'stEstado = ?'=>1, 'stFecharAnalise = ?'=>1, 'tpDistribuicao = ?'=>'A'));

                if(count($dDP)>0){
                    //ATUALIZA A TABELA tbDistribuirProjeto
                    $dadosDP = array();
                    $dadosDP['dtDevolucao'] = new Zend_Db_Expr('GETDATE()');
                    $whereDP = "idDistribuirProjeto = ".$dDP[0]->idDistribuirProjeto;
                    $tbDistribuirProjeto = new tbDistribuirProjeto();
                    $x = $tbDistribuirProjeto->update($dadosDP, $whereDP);

                    $reuniao = new Reuniao();
                    $raberta = $reuniao->buscarReuniaoAberta();
                    $idNrReuniao = $raberta['idNrReuniao'];

                    if($_POST['plenaria']){
                        $campoSiRecurso = 8; // 8=Enviado � Plen�ria
                    } else {
                        $campoSiRecurso = 9; // 9=Enviado para Checklist Publica��o
                    }

                    //ATUALIZA A TABELA tbRecurso
                    $dados = array();
                    $dados['siRecurso'] = $campoSiRecurso; // Devolvido da an�lise t�cnica
                    $dados['idNrReuniao'] = $idNrReuniao;
                    $dados['stAnalise'] = $stAnalise;
                    $where = "idRecurso = $idRecurso";
                    $tbRecurso = new tbRecurso();
                    $tbRecurso->update($dados, $where);
                }
                parent::message("A avalia��o do recurso foi finalizada com sucesso!", "recurso/analisar-recursos-cnic", "CONFIRM");
            }

            parent::message("Dados salvos com sucesso!", "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "CONFIRM");

        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "ERROR");
        }
	}

    public function coordParecerFinalizarRecursoAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idRecurso = (int) $post->idRecurso;

        //Atualiza a tabela tbRecurso
        $dados = array();
        $dados['siRecurso'] = 6; // Devolvido para o coordenador geral de an�lise
        $where = "idRecurso = $idRecurso";
        $tbRecurso = new tbRecurso();
        $return = $tbRecurso->update($dados, $where);

        if($return){
            echo json_encode(array('resposta'=>true));
        } else {
            echo json_encode(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function coordAnaliseFinalizarRecursoAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $vinculada = $this->idOrgao;

        $post = Zend_Registry::get('post');
        $idComponente = (int) $post->componente;
        $idRecurso = (int) $post->idRecurso;

        $tbRecurso = new tbRecurso();
        $dadosRecurso = $tbRecurso->buscar(array('idRecurso = ?'=>$post->idRecurso))->current();

        $idPronac = $dadosRecurso->IdPRONAC;
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $tbAnaliseAprovacao = new tbAnaliseAprovacao();

        //VERIFICA SE J� POSSUI AS PLANILHA DO TIPO 'CO'. SE N�O, INSERE FAZENDO A C�PIA DOS DADOS
        $verificaPlanilhaAprovacao = $tbPlanilhaAprovacao->buscar(array('tpPlanilha=?'=>'CO', 'stAtivo=?'=>'S', 'IdPRONAC=?'=>$idPronac));
        if(count($verificaPlanilhaAprovacao)==0){
            $tbPlanilhaAprovacao->copiandoPlanilhaRecurso($idPronac);
        }

        //VERIFICA SE J� POSSUI AS PLANILHA DO TIPO 'CO'. SE N�O, INSERE FAZENDO A C�PIA DOS DADOS
        $verificaAnaliseAprovacao = $tbAnaliseAprovacao->buscar(array('tpAnalise=?'=>'CO', 'IdPRONAC=?'=>$idPronac));
        if(count($verificaAnaliseAprovacao)==0){
            $tbAnaliseAprovacao->copiandoPlanilhaRecurso($idPronac);
        }

        $tbDistribuirProjeto = new tbDistribuirProjeto();
        $dadosDistProj = $tbDistribuirProjeto->buscar(array('IdPRONAC=?'=>$idPronac, 'tpDistribuicao=?'=>'A', 'stFecharAnalise=?'=>0, 'stEstado=?'=>0));
        if(count($dadosDistProj)>0){
            //Atualiza a tabela tbDistribuirProjeto
            $dadosDP = array();
            $dadosDP['idUsuario'] = $this->idUsuario;
            $dadosDP['dtFechamento'] =  new Zend_Db_Expr('GETDATE()');
            $dadosDP['stFecharAnalise'] =  1;
            $dadosDP['stEstado'] =  1;
            $whereDP = "idDistribuirProjeto = ".$dadosDistProj[0]->idDistribuirProjeto;
            $tbDistribuirProjeto = new tbDistribuirProjeto();
            $tbDistribuirProjeto->update($dadosDP, $whereDP);
        }

        //ATUALIZA A SITUA��O DO PROJETO
        $Projetos = new Projetos();
        $w = array();
        $w['situacao'] = 'D20';
        $w['ProvidenciaTomada'] = 'Recurso encaminhado � reuni�o da CNIC para avalia��o do componente da comiss�o.';
        $Projetos->alterarSituacao($idPronac, null, $w['situacao'], $w['ProvidenciaTomada']);

        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();

        //Atualiza a tabela tbRecurso
        $dados = array();
        $dados['idAgenteAvaliador'] = $idComponente; // Enviado para CNIC
        $dados['siRecurso'] = 7; // Enviado para CNIC
        $dados['idNrReuniao'] = $raberta['idNrReuniao'];
        $where = "idRecurso = $idRecurso";
        $return = $tbRecurso->update($dados, $where);

        if($return){
            echo json_encode(array('resposta'=>true));
        } else {
            echo json_encode(array('resposta'=>false));
        }
        die();

    }

    public function devolverRecursoAction() {

        $dados = array();
        $get = Zend_Registry::get('get');
        $idRecurso = (int) $get->id;

        $tbRecurso = new tbRecurso();
        $dadosRecurso = $tbRecurso->find(array('idRecurso=?'=>$idRecurso))->current();

        $siRecurso = $dadosRecurso->siRecurso;

        //RECURSOS TRATADOS POR PARECERISTA
        if($siRecurso == 6){
            //Atualiza a tabela tbRecurso
            $dados['siRecurso'] = 3; // Encaminhado do MinC para Unidade de An�lise
            $where = "idRecurso = $idRecurso";
        } else {
            $dados['siRecurso'] = 4; // Encaminhado para o T�cnico
            $where = "idRecurso = $idRecurso";
        }
        $return = $tbRecurso->update($dados, $where);

        parent::message("Recurso devolvido com sucesso!", "recurso?tipoFiltro=analisados", "CONFIRM");
    }

    public function analisarRecursosCnicAction()
	{
        if($this->idPerfil != 118){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if($ordem == "ASC") {
                $novaOrdem = "DESC";
            }else {
                $novaOrdem = "ASC";
            }
        }else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;

        } else {
            $campo = null;
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) $pag = $get->pag;
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($this->idUsuario);
        $idagente = $idagente['idAgente'];

//        $reuniao = new Reuniao();
//        $raberta = $reuniao->buscarReuniaoAberta();

        $where = array();
        $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
        $where['a.siRecurso = ?'] = 7; // 7=Encaminhar para ao Componente da Comiss�o
        $where['a.idAgenteAvaliador = ?'] = $idagente;
//        $where['a.idNrReuniao = ?'] = $raberta['idNrReuniao'];

        if((isset($_GET['pronac']) && !empty($_GET['pronac']))){
            $where['b.AnoProjeto+b.Sequencial = ?'] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        $tbRecurso = New tbRecurso();
        $total = $tbRecurso->painelRecursos($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbRecurso->painelRecursos($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
	}


    public function formAvaliarRecursoCnicAction() {

        $mapperArea = new Agente_Model_AreaMapper();

        if($this->idPerfil != 118){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $get = Zend_Registry::get('get');
        $idRecurso = (int) $get->recurso;

        $tbRecurso = new tbRecurso();
        $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$idRecurso))->current();
        $this->view->dados = $dados;

        $this->view->nmPagina = '';

        if($dados->siFaseProjeto == 2){
            if($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Projeto Indeferido';
                if($dados->tpSolicitacao == 'EO'){
                    $this->view->nmPagina = 'Enquadramento e Or�amento';
                } else if($dados->tpSolicitacao == 'OR'){
                    $this->view->nmPagina = 'Or�amento';
                }

                $PlanoDistribuicaoProduto = new Proposta_model_DbTable_PlanoDistribuicaoProduto();
                $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                $this->view->produtos = $dadosProdutos;

                $tipoDaPlanilha = 3; // 3=Planilha Or�ament�ria Aprovada
                if($dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
                    $tipoDaPlanilha = 4; // 4=Cortes Or�ament�rios Aprovados
                }
                $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, $tipoDaPlanilha);
                $this->view->planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoDaPlanilha);
            }
        }
        if($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI'){
            if($dados->tpSolicitacao == 'EN'){
                $this->view->nmPagina = 'Enquadramento';
            } else if($dados->tpSolicitacao == 'EO'){
                $this->view->nmPagina = 'Enquadramento e Or�amento';
            } else if($dados->tpSolicitacao == 'OR'){
                $this->view->nmPagina = 'Or�amento';
            } else {
                $this->view->nmPagina = 'Projeto Indeferido';
            }

            $Projetos = new Projetos();
            $this->view->projetosEN = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

            $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo',  'descricao');
            $this->view->combosegmentosculturais = Segmentocultural::buscarSegmento($this->view->projetosEN->cdArea);

            $parecer = new Parecer();
            $this->view->Parecer = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer = ?' => 7, 'stAtivo = ?' => 1))->current();
        }

        //DADOS DO PROJETO
        $Projetos = new Projetos();
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->IdPRONAC))->current();
        $this->view->projeto = $p;
    }

    public function cnicSalvarEnquadramentoAction()
	{
        if($this->idPerfil != 118){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idPronac = $_POST['idPronac'];
        $idRecurso = $_POST['idRecurso'];
        $areaCultural = $_POST['areaCultural'];
        $segmentoCultural = $_POST['segmentoCultural'];
        $enquadramentoProjeto = $_POST['enquadramentoProjeto'];
        $parecerProjeto = $_POST['parecerProjeto'];
        $dsParecer = $_POST['dsParecer'];

        try {
            //ATUALIAZA A SITUA��O, �REA E SEGMENTO DO PROJETO
            $d = array();
            $d['situacao'] = 'D20';
            $d['ProvidenciaTomada'] = 'Recurso em an�lise pela Comiss�o Nacional de Incentivo � Cultura - CNIC.';
            $d['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
            $d['Area'] = $areaCultural;
            $d['Segmento'] = $segmentoCultural;
            $where = "IdPRONAC = $idPronac";
            $Projetos = new Projetos();
            $Projetos->update($d, $where);

            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac));
            if(count($dadosProjeto)>0){
                //CADASTRA OU ATUALIZA O ENQUADRAMENTO DO PROJETO
                $enquadramentoDAO = new Enquadramento();
                $dadosEnquadramento = array(
                    'IdPRONAC'=> $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial'=> $dadosProjeto[0]->Sequencial,
                    'Enquadramento' => $enquadramentoProjeto,
                    'DtEnquadramento' => MinC_Db_Expr::date(),
                    'Observacao' => '',
                    'Logon' => $idusuario
                );
                $whereBuscarDados  = array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial);
                $buscarEnquadramento = $enquadramentoDAO->buscar($whereBuscarDados);
                if(count($buscarEnquadramento) > 0){
                    $buscarEnquadramento = $buscarEnquadramento->current();
                    $whereUpdate = 'IdEnquadramento = '.$buscarEnquadramento->IdEnquadramento;
                    $alteraEnquadramento = $enquadramentoDAO->alterar($dadosEnquadramento, $whereUpdate);
                } else {
                    $insereEnquadramento = $enquadramentoDAO->inserir($dadosEnquadramento);
                }
                $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

                //CADASTRA OU ATUALIZA O PARECER DO COMPONENTE DA COMISS�O
                $parecerDAO = new Parecer();
                $dadosParecer = array(
                    'idPRONAC' => $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial' => $dadosProjeto[0]->Sequencial,
                    'TipoParecer' => 7,
                    'ParecerFavoravel' => $parecerProjeto,
                    'DtParecer' => MinC_Db_Expr::date(),
                    'NumeroReuniao' => null,
                    'ResumoParecer' => $dsParecer,
                    'SugeridoReal' => 0,
                    'Atendimento' => 'S',
                    'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
                    'stAtivo' => 1,
                    'idTipoAgente' => 6,
                    'Logon' => $idusuario
                );

                $buscarParecer = $parecerDAO->buscar(array('IdPRONAC = ?' => $idPronac));
                foreach ($dadosParecer as $dp) {
                    $parecerAntigo = array(
                        'Atendimento' => 'S',
                        'stAtivo' => 0
                    );
                    $whereUpdateParecer = 'IdPRONAC = '.$idPronac;
                    $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);
                }

                $buscarParecer = $parecerDAO->buscar(array('IdPRONAC = ?' => $idPronac, 'AnoProjeto = ?' => $dadosProjeto[0]->AnoProjeto, 'Sequencial = ?' => $dadosProjeto[0]->Sequencial, 'TipoParecer = ?' => 7, 'idTipoAgente = ?' =>6));
                if(count($buscarParecer) > 0){
                    $buscarParecer = $buscarParecer->current();
                    $whereUpdateParecer = 'IdParecer = '.$buscarParecer->IdParecer;
                    $alteraParecer = $parecerDAO->update($dadosParecer, $whereUpdateParecer);
                } else {
                    $insereParecer = $parecerDAO->inserir($dadosParecer);
                }
            }

            if(isset($_POST['finalizarAvaliacao']) && $_POST['finalizarAvaliacao'] == 1){

                $idNrReuniao = null;
                if($_POST['plenaria']){
                    $campoSiRecurso = 8; // 8=Enviado � Plen�ria

                    $reuniao = new Reuniao();
                    $raberta = $reuniao->buscarReuniaoAberta();
                    $idNrReuniao = $raberta['idNrReuniao'];
                } else {
                    $campoSiRecurso = 9; // 9=Enviado para Checklist Publica��o
                }

                //ATUALIZA A TABELA tbRecurso
                $dados = array();
                $dados['siRecurso'] = $campoSiRecurso;
                $dados['idNrReuniao'] = $idNrReuniao;
                $dados['stAnalise'] = ($parecerProjeto == 1) ? 'IC' : 'AC';
                $where = "idRecurso = $idRecurso";
                $tbRecurso = new tbRecurso();
                $tbRecurso->update($dados, $where);
                parent::message("A avalia��o do recurso foi finalizada com sucesso! ", "recurso/analisar-recursos-cnic", "CONFIRM");
            }

            parent::message("Dados salvos com sucesso!", "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "CONFIRM");

        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "ERROR");
        }
	}

    public function salvarAnaliseDeConteudoAction()
	{
        if($this->idPerfil != 94 && $this->idPerfil != 110){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        $idPronac = $_POST['idPronac'];
        $idProduto = $_POST['idProduto'];
        $idRecurso = $_POST['idRecurso'];

        try {
//            if (!$_POST['ParecerFavoravel_'.$idProduto]) {
//                $planilhaProjeto = new PlanilhaProjeto();
//                $atualizar = array('idUnidade' => 1, 'Quantidade' => 0, 'Ocorrencia' => 0, 'ValorUnitario' => 0, 'QtdeDias' => 0, 'idUsuario' => $idusuario, 'Justificativa' => '');
//                $planilhaProjeto->alterar($atualizar, array('idPRONAC = ?' => $idPronac, 'idProduto = ?' => $idProduto));
//
//            } else {
//                $analisedeConteudoDAO = new Analisedeconteudo();
//                $whereB['idPronac  = ?'] = $idPronac;
//                $whereB['idProduto = ?'] = $idProduto;
//                $busca = $analisedeConteudoDAO->buscar($whereB);
//
//                if($busca[0]->ParecerFavoravel == 0) {
//                    $copiaPlanilha = PlanilhaPropostaDAO::parecerFavoravel($idPronac, $idProduto);
//                }
//            }

            $artigo18 = 0;
            $artigo26 = 0;
            if(isset($_POST['ArtigoEnquadramento_'.$idProduto]) && !empty($_POST['ArtigoEnquadramento_'.$idProduto])){
                if($_POST['ArtigoEnquadramento_'.$idProduto] == 18){
                    $artigo18 = 1;
                    $artigo26 = 0;
                } else {
                    $artigo26 = 1;
                    $artigo18 = 0;
                }
            }
            $dados = array(
                'Lei8313'           => isset($_POST['Lei8313_'.$idProduto]) ? $_POST['Lei8313_'.$idProduto] : 0,
                'Artigo3'           => isset($_POST['Artigo3_'.$idProduto]) ? $_POST['Artigo3_'.$idProduto] : 0,
                'IncisoArtigo3' 	=> isset($_POST['IncisoArtigo3_'.$idProduto]) ? $_POST['IncisoArtigo3_'.$idProduto] : 0,
                'AlineaArtigo3' 	=> isset($_POST['AlineaArtigo3_'.$idProduto]) ? $_POST['AlineaArtigo3_'.$idProduto] : '',
                'Artigo18'          => $artigo18,
                'AlineaArtigo18' 	=> isset($_POST['AlineaArtigo18_'.$idProduto]) ? $_POST['AlineaArtigo18_'.$idProduto] : '',
                'Artigo26'          => $artigo26,
                'Lei5761'           => isset($_POST['Lei5761_'.$idProduto]) ? $_POST['Lei5761_'.$idProduto] : 0,
                'Artigo27'          => isset($_POST['Artigo27_'.$idProduto]) ? $_POST['Artigo27_'.$idProduto] : 0,
                'IncisoArtigo27_I' 	=> isset($_POST['IncisoArtigo27_I_'.$idProduto]) ? $_POST['IncisoArtigo27_I_'.$idProduto] : 0,
                'IncisoArtigo27_II' => isset($_POST['IncisoArtigo27_II_'.$idProduto]) ? $_POST['IncisoArtigo27_II_'.$idProduto] : 0,
                'IncisoArtigo27_III'=> isset($_POST['IncisoArtigo27_III_'.$idProduto]) ? $_POST['IncisoArtigo27_III_'.$idProduto] : 0,
                'IncisoArtigo27_IV' => isset($_POST['IncisoArtigo27_IV_'.$idProduto]) ? $_POST['IncisoArtigo27_IV_'.$idProduto] : 0,
                'TipoParecer' 		=> 1,
                'ParecerFavoravel' 	=> $_POST['ParecerFavoravel_'.$idProduto],
                'ParecerDeConteudo' => isset($_POST['ParecerDeConteudo_'.$idProduto]) ? $_POST['ParecerDeConteudo_'.$idProduto] : '',
                'idUsuario' 		=> $this->idUsuario,
            );
            $analisedeConteudoDAO = new Analisedeconteudo();
            $where['idPRONAC = ?']  = $idPronac;

            // Quando o parecer do produto principal � desfavor�vel, o parecer dos produtos secund�rios tamb�m devem ser desfavor�veis.
            if( (!$_POST['stPrincipal']) || ($_POST['stPrincipal'] && $_POST['ParecerFavoravel_'.$idProduto])){
                $where['idProduto = ?'] = $idProduto;
            }
            $analisedeConteudoDAO->update($dados,$where);

            parent::message("Dados salvos com sucesso!", "recurso/form-avaliar-recurso?id=$idRecurso", "CONFIRM");

        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "recurso/form-avaliar-recurso?id=$idRecurso", "ERROR");
        }
	}


    //COMPONENTE DA COMISSAO SALVANDOS OS DADOS DA ANALISE DE CONTEUDO
    public function cnicSalvarAnaliseDeConteudoAction()
	{
        if($this->idPerfil != 118){
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
        $idPronac = $_POST['idPronac'];
        $idProduto = $_POST['idProduto'];
        $idRecurso = $_POST['idRecurso'];

        try {
            $artigo18 = 0;
            $artigo26 = 0;
            if(isset($_POST['ArtigoEnquadramento_'.$idProduto]) && !empty($_POST['ArtigoEnquadramento_'.$idProduto])){
                if($_POST['ArtigoEnquadramento_'.$idProduto] == 18){
                    $artigo18 = 1;
                    $artigo26 = 0;
                } else {
                    $artigo26 = 1;
                    $artigo18 = 0;
                }
            }
            $dados = array(
                'Lei8313'           => isset($_POST['Lei8313_'.$idProduto]) ? $_POST['Lei8313_'.$idProduto] : 0,
                'Artigo3'           => isset($_POST['Artigo3_'.$idProduto]) ? $_POST['Artigo3_'.$idProduto] : 0,
                'IncisoArtigo3' 	=> isset($_POST['IncisoArtigo3_'.$idProduto]) ? $_POST['IncisoArtigo3_'.$idProduto] : 0,
                'AlineaArtigo3' 	=> isset($_POST['AlineaArtigo3_'.$idProduto]) ? $_POST['AlineaArtigo3_'.$idProduto] : '',
                'Artigo18'          => $artigo18,
                'AlineaArtigo18' 	=> isset($_POST['AlineaArtigo18_'.$idProduto]) ? $_POST['AlineaArtigo18_'.$idProduto] : '',
                'Artigo26'          => $artigo26,
                'Lei5761'           => isset($_POST['Lei5761_'.$idProduto]) ? $_POST['Lei5761_'.$idProduto] : 0,
                'Artigo27'          => isset($_POST['Artigo27_'.$idProduto]) ? $_POST['Artigo27_'.$idProduto] : 0,
                'IncisoArtigo27_I' 	=> isset($_POST['IncisoArtigo27_I_'.$idProduto]) ? $_POST['IncisoArtigo27_I_'.$idProduto] : 0,
                'IncisoArtigo27_II' => isset($_POST['IncisoArtigo27_II_'.$idProduto]) ? $_POST['IncisoArtigo27_II_'.$idProduto] : 0,
                'IncisoArtigo27_III'=> isset($_POST['IncisoArtigo27_III_'.$idProduto]) ? $_POST['IncisoArtigo27_III_'.$idProduto] : 0,
                'IncisoArtigo27_IV' => isset($_POST['IncisoArtigo27_IV_'.$idProduto]) ? $_POST['IncisoArtigo27_IV_'.$idProduto] : 0,
                'TipoParecer' 		=> 1,
                'ParecerFavoravel' 	=> $_POST['ParecerFavoravel_'.$idProduto],
                'ParecerDeConteudo' => isset($_POST['ParecerDeConteudo_'.$idProduto]) ? $_POST['ParecerDeConteudo_'.$idProduto] : '',
                'idUsuario' 		=> $this->idUsuario,
            );
            $analisedeConteudoDAO = new Analisedeconteudo();
            $where['idPRONAC = ?']  = $idPronac;

            // Quando o parecer do produto principal � desfavor�vel, o parecer dos produtos secund�rios tamb�m devem ser desfavor�veis.
            if( (!$_POST['stPrincipal']) || ($_POST['stPrincipal'] && $_POST['ParecerFavoravel_'.$idProduto])){
                $where['idProduto = ?'] = $idProduto;
            }
            $analisedeConteudoDAO->update($dados,$where);

            parent::message("Dados salvos com sucesso!", "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "CONFIRM");

        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "recurso/form-avaliar-recurso-cnic?recurso=$idRecurso", "ERROR");
        }
	}

    /**
     * M�todo alterarItem()
     * Altera os itens da planilha
     * @param idPlanilha
     * @return void
     */
    public function alterarItemAction() {
        $this->_helper->layout->disableLayout();
        $idPlanilhaProjeto = $this->_request->getParam("idPlanilha");

        /* ITEM */
        $PlanilhaProjeto = new PlanilhaProjeto();
        $planilha = $PlanilhaProjeto->buscarDadosAvaliacaoDeItem($idPlanilhaProjeto);

        $dadosPlanilha = array();
        if(count($planilha) > 0){
            /* PROJETO */
            $Projetos = new Projetos();
            $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $planilha[0]->idPRONAC))->current();
            $dadosProjeto = array(
                'IdPRONAC' => $projeto->IdPRONAC,
                'PRONAC' => $projeto->AnoProjeto.$projeto->Sequencial,
                'NomeProjeto' => utf8_encode($projeto->NomeProjeto)
            );

            $PlanilhaProposta = new PlanilhaProposta();
            $dadosSolicitados = $PlanilhaProposta->buscarDadosAvaliacaoDeItem($planilha[0]->idPlanilhaProposta)->current();
            $dadosPlanilhaProposta = array();
            $dadosPlanilhaProposta['Unidade'] = utf8_encode($dadosSolicitados->descUnidade);
            $dadosPlanilhaProposta['Quantidade'] = $dadosSolicitados->Quantidade;
            $dadosPlanilhaProposta['Ocorrencia'] = $dadosSolicitados->Ocorrencia;
            $dadosPlanilhaProposta['ValorUnitario'] = utf8_encode('R$ '.number_format($dadosSolicitados->ValorUnitario, 2, ',', '.'));
            $dadosPlanilhaProposta['QtdeDias'] = $dadosSolicitados->QtdeDias;
            $dadosPlanilhaProposta['TotalSolicitado'] = utf8_encode('R$ '.number_format(($dadosSolicitados->Quantidade*$dadosSolicitados->Ocorrencia*$dadosSolicitados->ValorUnitario), 2, ',', '.'));
            $dadosPlanilhaProposta['TotalSolicitadoValidacao'] = utf8_encode(number_format(($dadosSolicitados->Quantidade*$dadosSolicitados->Ocorrencia*$dadosSolicitados->ValorUnitario), 2, '', ''));

            foreach ($planilha as $registro) {
                $dadosPlanilhaProjeto['idPlanilhaProjeto'] = $registro['idPlanilhaProjeto'];
                $dadosPlanilhaProjeto['idProduto'] = $registro['idProduto'];
                $dadosPlanilhaProjeto['descProduto'] = utf8_encode(!empty($registro['descProduto']) ? $registro['descProduto'] : 'Administra��o do Projeto');
                $dadosPlanilhaProjeto['idEtapa'] = $registro['idEtapa'];
                $dadosPlanilhaProjeto['descEtapa'] = utf8_encode($registro['descEtapa']);
                $dadosPlanilhaProjeto['idPlanilhaItem'] = $registro['idPlanilhaItem'];
                $dadosPlanilhaProjeto['descItem'] = utf8_encode($registro['descItem']);
                $dadosPlanilhaProjeto['idUnidade'] = $registro['idUnidade'];
                $dadosPlanilhaProjeto['descUnidade'] = utf8_encode($registro['descUnidade']);
                $dadosPlanilhaProjeto['Quantidade'] = $registro['Quantidade'];
                $dadosPlanilhaProjeto['Ocorrencia'] = $registro['Ocorrencia'];
                $dadosPlanilhaProjeto['ValorUnitario'] = utf8_encode('R$ '.number_format($registro['ValorUnitario'], 2, ',', '.'));
                $dadosPlanilhaProjeto['QtdeDias'] = $registro['QtdeDias'];
                $dadosPlanilhaProjeto['TotalSolicitado'] = utf8_encode('R$ '.number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']), 2, ',', '.'));
                $dadosPlanilhaProjeto['Justificativa'] = utf8_encode($registro['Justificativa']);
            }
            //$jsonEncode = json_encode($dadosPlanilha);
            echo json_encode(array('resposta'=>true, 'dadosPlanilhaProposta'=>$dadosPlanilhaProposta, 'dadosPlanilhaProjeto'=>$dadosPlanilhaProjeto, 'dadosProjeto'=>$dadosProjeto));

        } else {
            echo json_encode(array('resposta'=>false));
        }
        die();

    }

    /**
     * M�todo alterarItem()
     * Altera os itens da planilha
     * @param idPronac
     * @param idProduto
     * @param stPrincipal
     * @param idPlanilhaProjeto
     * @return void
     */
    public function cnicAlterarItemAction() {
        $this->_helper->layout->disableLayout();
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");

        /* ITEM */
        $PlanilhaAprovacao = new PlanilhaAprovacao();
        $planilha = $PlanilhaAprovacao->buscarDadosAvaliacaoDeItem($idPlanilhaAprovacao);

        $dadosPlanilhaAprovada = array();
        if(count($planilha) > 0){
            /* PROJETO */
            $Projetos = new Projetos();
            $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $planilha[0]->idPRONAC))->current();
            $dadosProjeto = array(
                'IdPRONAC' => $projeto->IdPRONAC,
                'PRONAC' => $projeto->AnoProjeto.$projeto->Sequencial,
                'NomeProjeto' => utf8_encode($projeto->NomeProjeto)
            );

            $PlanilhaProposta = new PlanilhaProposta();
            $dadosSolicitados = $PlanilhaProposta->buscarDadosAvaliacaoDeItem($planilha[0]->idPlanilhaProposta)->current();
            $dadosPlanilhaProposta = array();
            $dadosPlanilhaProposta['Unidade'] = utf8_encode($dadosSolicitados->descUnidade);
            $dadosPlanilhaProposta['Quantidade'] = $dadosSolicitados->Quantidade;
            $dadosPlanilhaProposta['Ocorrencia'] = $dadosSolicitados->Ocorrencia;
            $dadosPlanilhaProposta['ValorUnitario'] = utf8_encode('R$ '.number_format($dadosSolicitados->ValorUnitario, 2, ',', '.'));
            $dadosPlanilhaProposta['QtdeDias'] = $dadosSolicitados->QtdeDias;
            $dadosPlanilhaProposta['TotalSolicitado'] = utf8_encode('R$ '.number_format(($dadosSolicitados->Quantidade*$dadosSolicitados->Ocorrencia*$dadosSolicitados->ValorUnitario), 2, ',', '.'));
            $dadosPlanilhaProposta['TotalSolicitadoValidacao'] = utf8_encode(number_format(($dadosSolicitados->Quantidade*$dadosSolicitados->Ocorrencia*$dadosSolicitados->ValorUnitario), 2, '', ''));

            $PlanilhaProjeto = new PlanilhaProjeto();
            $dadosSugeridos = $PlanilhaProjeto->buscarDadosAvaliacaoDeItem($planilha[0]->idPlanilhaProjeto)->current();
            $dadosPlanilhaProjeto = array();
            $dadosPlanilhaProjeto['Unidade'] = utf8_encode($dadosSugeridos->descUnidade);
            $dadosPlanilhaProjeto['Quantidade'] = $dadosSugeridos->Quantidade;
            $dadosPlanilhaProjeto['Ocorrencia'] = $dadosSugeridos->Ocorrencia;
            $dadosPlanilhaProjeto['ValorUnitario'] = utf8_encode('R$ '.number_format($dadosSugeridos->ValorUnitario, 2, ',', '.'));
            $dadosPlanilhaProjeto['QtdeDias'] = $dadosSugeridos->QtdeDias;
            $dadosPlanilhaProjeto['TotalSolicitado'] = utf8_encode('R$ '.number_format(($dadosSugeridos->Quantidade*$dadosSugeridos->Ocorrencia*$dadosSugeridos->ValorUnitario), 2, ',', '.'));

            foreach ($planilha as $registro) {
                $dadosPlanilhaAprovada['idPlanilhaAprovacao'] = $registro['idPlanilhaAprovacao'];
                $dadosPlanilhaAprovada['idProduto'] = $registro['idProduto'];
                $dadosPlanilhaAprovada['descProduto'] = utf8_encode($registro['descProduto']);
                $dadosPlanilhaAprovada['idEtapa'] = $registro['idEtapa'];
                $dadosPlanilhaAprovada['descEtapa'] = utf8_encode($registro['descEtapa']);
                $dadosPlanilhaAprovada['idPlanilhaItem'] = $registro['idPlanilhaItem'];
                $dadosPlanilhaAprovada['descItem'] = utf8_encode($registro['descItem']);
                $dadosPlanilhaAprovada['idUnidade'] = $registro['idUnidade'];
                $dadosPlanilhaAprovada['descUnidade'] = utf8_encode($registro['descUnidade']);
                $dadosPlanilhaAprovada['Quantidade'] = $registro['Quantidade'];
                $dadosPlanilhaAprovada['Ocorrencia'] = $registro['Ocorrencia'];
                $dadosPlanilhaAprovada['ValorUnitario'] = utf8_encode('R$ '.number_format($registro['ValorUnitario'], 2, ',', '.'));
                $dadosPlanilhaAprovada['QtdeDias'] = $registro['QtdeDias'];
                $dadosPlanilhaAprovada['TotalSolicitado'] = utf8_encode('R$ '.number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']), 2, ',', '.'));
                $dadosPlanilhaAprovada['dsJustificativa'] = utf8_encode($registro['dsJustificativa']);
            }
//            $jsonEncode = json_encode($dadosPlanilhaAprovada);
            echo json_encode(array('resposta'=>true, 'dadosPlanilhaProposta'=>$dadosPlanilhaProposta, 'dadosPlanilhaProjeto'=>$dadosPlanilhaProjeto, 'dadosPlanilhaAprovada'=>$dadosPlanilhaAprovada, 'dadosProjeto'=>$dadosProjeto));

        } else {
            echo json_encode(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function salvarAvaliacaoDoItemAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $auth = Zend_Auth::getInstance(); // pega a autentica��o

        $dados = array();
        $dados['idUnidade'] = $_POST['Unidade'];
        $dados['Quantidade'] = $_POST['Quantidade'];
        $dados['Ocorrencia'] = $_POST['Ocorrencia'];
        $dados['ValorUnitario'] = str_replace('.', '', $_POST['ValorUnitario']);
        $dados['ValorUnitario'] = str_replace(',', '.', $dados['ValorUnitario']);
        $dados['QtdeDias'] = $_POST['QtdeDias'];
        $dados['Justificativa'] = utf8_decode($_POST['Justificativa']);
        $dados['idUsuario'] = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : 0;
        $vlTotal = @number_format(($_POST['Quantidade']*$_POST['Ocorrencia']*$dados['vlUnitario']), 2, '', '');

        //O valor total dos valores n�o podem ultrapassar o valor solicitado na proposta.
        if($vlTotal > $_POST['valorSolicitado']){
            echo json_encode(array('resposta'=>false, 'msg'=> utf8_decode('O valor total n&atilde;o pode ser maior do que '.$_POST['valorSolicitado'].'.')));
        } else {
            $where = array('idPlanilhaProjeto = ?' => $_POST['idPlanilha']);
            $PlanilhaProjeto = new PlanilhaProjeto();
            if($PlanilhaProjeto->alterar($dados, $where)){
                echo json_encode(array('resposta'=>true, 'msg'=>'Dados salvos com sucesso!'));
            } else {
                echo json_encode(array('resposta'=>true, 'msg'=>'Erro ao salvar os dados!'));
            }
        }
        die();
    }

    //Criado no dia 07/10/2013 - Jefferson Alessandro
    public function cnicSalvarAvaliacaoDoItemAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($this->idUsuario);
        $idagente = $idagente['idAgente'];

        $dados = array();
        $dados['idUnidade'] = $_POST['Unidade'];
        $dados['qtItem'] = $_POST['Quantidade'];
        $dados['nrOcorrencia'] = $_POST['Ocorrencia'];
        $dados['vlUnitario'] = str_replace('.', '', $_POST['ValorUnitario']);
        $dados['vlUnitario'] = str_replace(',', '.', $dados['vlUnitario']);
        $dados['qtDias'] = $_POST['QtdeDias'];
        $dados['dsJustificativa'] = utf8_decode($_POST['Justificativa']);
        $dados['idAgente'] = $idagente;
        $vlTotal = @number_format(($_POST['Quantidade']*$_POST['Ocorrencia']*$dados['vlUnitario']), 2, '', '');

        //O valor total dos valores n�o podem ultrapassar o valor solicitado na proposta.
        if($vlTotal > $_POST['valorSolicitado']){
            echo json_encode(array('resposta'=>false, 'msg'=> utf8_decode('O valor total n&atilde;o pode ser maior do que '.$_POST['valorSolicitado'].'.')));
        } else {
            $where = array('idPlanilhaAprovacao = ?' => $_POST['idPlanilha']);
            $PlanilhaAprovacao = new PlanilhaAprovacao();
            if($PlanilhaAprovacao->alterar($dados, $where)){
                echo json_encode(array('resposta'=>true, 'msg'=>'Dados salvos com sucesso!'));
            } else {
                echo json_encode(array('resposta'=>true, 'msg'=>'Erro ao salvar os dados!'));
            }
        }
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }





	/**
	 * M�todo com a Solicita��o de Recurso
	 * @access public
	 * @param void
	 * @return void
	 */
	public function recursoAction()
	{
		$get = Zend_Registry::get('get');
		$idPronac = $get->idPronac;

		$tbinferidos = RecursoDAO::buscarRecursoProjetosIndeferidos();
		$this->view->recursoindeferidos = $tbinferidos;

		$tborcamento = RecursoDAO::buscarRecursoOrcamento();
		$this->view->recursoorcamento = $tborcamento;

		$tbreenquadramento = RecursoDAO::buscarRecursoReenquadramento();
		$this->view->recursoreenquadramento = $tbreenquadramento;
	} // fecha m�todo recursoAction()



	/**
	 * M�todo com os Projetos Indeferidos
	 * @access public
	 * @param void
	 * @return void
	 */
	public function indeferidosAction()
	{
		$get = Zend_Registry::get('get');
		$idPronac = $get->idPronac;

		$tbinferidos = RecursoDAO::buscarRecursoProjetosIndeferidos($idPronac);
		$this->view->recursoindeferidos = $tbinferidos;

		// caso o formul�rio seja enviado via post
		if ($this->getRequest()->isPost())
		{
			// recebe os dados via post
			$post          = Zend_Registry::get('post');

			$justificativa 			= Seguranca::tratarVarEditor($_POST['justificativa']); // recebe os dados do editor
			$stAtendimento   		= $post->stAtendimento;
			$idPronac      			= $post->idPronac;
			$idRecurso           	= $post->idRecurso;
			$dtAvaliacao           	= new Zend_Db_Expr('GETDATE()');
			$idAgenteAvaliador   	= $this->getIdUsuario;

			try
			{
				$dados = array(
					'dtAvaliacao'       => new Zend_Db_Expr('GETDATE()'),
					'dsAvaliacao' 		=> Seguranca::tratarVarEditor($_POST['justificativa']),
					'stAtendimento'   	=> $stAtendimento,
					'dsAvaliacao'       => $justificativa,
					'idAgenteAvaliador' => $idAgenteAvaliador);

				// valida os dados
				if (empty($idPronac))
				{
					throw new Exception("Por favor, informe o PRONAC!");
				}
				else if (empty($stAtendimento))
				{
					throw new Exception("Por favor, selecione um Tipo de Parecer!");
				}
				else if (empty($justificativa))
				{
					throw new Exception("Por favor, informe a justificativa!");
				}
				else if (strlen($post->justificativa) > 1000)
				{
					throw new Exception("A justificativa n�o pode conter mais de 1000 caracteres!");
				}
				else
				{
					if ($stAtendimento == 'D') // cadastra a reitegra��o (planilha de aprovacao)
					{
						$msg = "Deferir";
					}
					else if ($stAtendimento == 'I')
					{
						$msg = "Indeferir";
					}
					$alterarAtendimento = RecursoDAO::avaliarRecurso($dados, $idRecurso);
					if ($alterarAtendimento) // caso tenha sido alterado com sucesso
					{
						parent::message("Solicita��o enviada com sucesso!", "recurso", "CONFIRM");
					}
					else
					{
						throw new Exception("Erro ao $msg recurso!");
					}
				} // fecha else
			} // fecha try
			catch (Exception $e)
			{
				parent::message($e->getMessage(), "recurso/indeferidos?idPronac=" . $idPronac, "ERROR");
			}
		} // fecha if

	} // fecha m�todo indeferidosAction()



	/**
	 * M�todo com os Projetos Deferidos - Reenquadramento
	 * @access public
	 * @param void
	 * @return void
	 */
	public function reenquadramentoAction()
	{
		if ($this->getRequest()->isPost())
		{
			// recebe os dados via post
			$post          				= Zend_Registry::get('post');
			$stAtendimento				= $post->stAtendimento;
			$idPronac     				= $post->idPronac;
			$idRecurso     				= $post->idRecurso;
			$AnoProjeto   				= $post->AnoProjeto;
			$Sequencial   				= $post->Sequencial;
			$enquadramento      		= (int) $post->enquadramento;
			$justificativa 				= Seguranca::tratarVarEditor($_POST['dsRecurso']); // recebe os dados do editor
			$idAgenteAvaliador   	    = $this->getIdUsuario;
			$idEnquadramento            = $post->idEnquadramento;

			try
			{
				// dados recurso
				$dadosRecurso = array(
					'dtAvaliacao'       => new Zend_Db_Expr('GETDATE()'),
					'dsAvaliacao' 		=> $justificativa,
					'stAtendimento'   	=> $stAtendimento,
					'dsAvaliacao'       => $justificativa,
					'idAgenteAvaliador' => $idAgenteAvaliador);

				// dados enquadramento
				$dadosEnquadramento = array(
					'IdPRONAC'      					=> $idPronac,
					'AnoProjeto'      					=> $AnoProjeto,
					'Sequencial'           				=> $Sequencial,
					'Enquadramento'           			=> $enquadramento,
					'DtEnquadramento'           		=> new Zend_Db_Expr('GETDATE()'),
					'Observacao' 						=> $justificativa,
					'Logon'           			 		=> $idAgenteAvaliador);

				// valida os dados
				if (empty($idPronac))
				{
					throw new Exception("Por favor, informe o PRONAC!");
				}
				else if (empty($stAtendimento))
				{
					throw new Exception("Por favor, selecione um Tipo de Parecer!");
				}
				else if (empty($justificativa))
				{
					throw new Exception("Por favor, informe a justificativa!");
				}
				else if (strlen($post->justificativa) > 1000)
				{
					throw new Exception("A justificativa n�o pode conter mais de 1000 caracteres!");
				}
				else if (empty($enquadramento))
				{
					throw new Exception("Por favor,selecione o tipo de Enquadramento!");
				}
				else
				{
					if ($stAtendimento == 'D') // cadastra a reitegra��o (planilha de aprovacao)
					{
						$msg = "Deferir";
					}
					else if ($stAtendimento == 'I')
					{
						$msg = "Indeferir";
					}

					// realiza o update na tabela recurso
					$alterarAtendimento = RecursoDAO::avaliarRecurso($dadosRecurso, $idRecurso);

					// realiza o update na tabela de enquadramento
					$alterarEnquadramento = RecursoDAO::recursoReenquadramento($dadosEnquadramento, $idEnquadramento);

					if ($alterarAtendimento && $alterarEnquadramento) // caso tenha sido alterado com sucesso
					{
						parent::message("Solicita��o enviada com sucesso!", "recurso", "CONFIRM");
					}
					else
					{
						throw new Exception("Erro ao $msg recurso!");
					}
				} // fecha else
			} // fecha try
			catch (Exception $e)
			{
				parent::message($e->getMessage(), "recurso/reenquadramento?idPronac=" . $idPronac, "ERROR");
			}
		} // fecha if
		else
		{
			$get = Zend_Registry::get('get');
			$idPronac = $get->idPronac;

			$tbreenquadramento = RecursoDAO::buscarRecursoReenquadramento($idPronac);
			$this->view->recursoreenquadramento = $tbreenquadramento;
		}

	}// fecha m�todo reenquadramentoAction()






	/**
	 * M�todo com os Projetos Deferidos - Or�amento
	 * @access public
	 * @param void
	 * @return void
	 */



	/**
	 * M�todo com os Projetos Deferidos - Or�amento (Parecer Consolidado)
	 * @access public
	 * @param void
	 * @return void
	 */
	public function deferidosAction()
	{
		$tborcamento = RecursoDAO::buscarRecursoProjetosDeferidos();
		$this->view->deferido = $tbdeferido;
	} // fecha m�todo deferidosAction()



	/**
	 * M�todo com os Projetos Deferidos com Solicita��o de Reenquadramento - Or�amento (Parecer Consolidado)
	 * @access public
	 * @param void
	 * @return void
	 */
	public function parecerAction()
	{
		$get = Zend_Registry::get('get');
		$idPronac = $get->idPronac;
		$idRecurso = $get->idRecurso;

		// caso o formul�rio seja enviado via post
		if ($this->getRequest()->isPost())
		{
			// pega o pronac
			$pronac = ProjetoDAO::buscarPronac($idPronac);
			$pronac = $pronac['pronac'];

			// pega a pen�ltima situa��o do projeto
			$situacoes = ProjetoDAO::buscarSituacoesProjeto($pronac);
			$situacao  = $situacoes[1]->Situacao;

			// altera a situa��o do projeto
			$alterarSituacao = ProjetoDAO::alterarSituacao($idPronac, $situacao);

			parent::message("Projeto consolidado com sucesso!", "recurso", "CONFIRM");
		}
		else
		{
			$tborcamento = RecursoDAO::buscarRecursoOrcamento($idPronac, $idRecurso);
			$this->view->tbrecurso = $tborcamento;

			$buscarParecer = RecursoDAO::buscarParecer($this->getIdUsuario, $idPronac);
			$this->view->parecer = $buscarParecer;
		} // feche else

	} // fecha m�todo parecerAction()






	public function orcamentoAction()
	{

		$get = Zend_Registry::get('get');
		$idPronac = $get->idPronac;
		$idRecurso = $get->idRecurso;

		$tborcamento = RecursoDAO::buscarRecursoOrcamento($idPronac, $idRecurso);
		$this->view->recursoorcamento = $tborcamento;

		$buscarProdutos = SolicitarRecursoDecisaoDAO::analiseDeCustosBuscarProduto($idPronac);

		///$buscarRecursos = RecursoDAO::buscarIdRecurso();




		// busca a planilha com as unidades
		$buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();

		// busca a planilha com as etapas
		$buscarPlanilhaEtapa = PlanilhaEtapaDAO::buscar();



		// busca o pronac
		$pronac = ProjetoDAO::buscarPronac($idPronac);
		$pronac = $pronac['pronac'];
		$buscarPronac = ProjetoDAO::buscar($pronac);

		// manda os dados para a vis�o
		$this->view->analise         = RealizarAnaliseProjetoDAO::analiseDeConta($pronac);
		$this->view->buscarProd      = $buscarProdutos;

		$this->view->planilhaUnidade = $buscarPlanilhaUnidade;
		$this->view->planilhaEtapa   = $buscarPlanilhaEtapa;
		$this->view->pronac          = $buscarPronac;


		$this->view->qtdItens        = count(RealizarAnaliseProjetoDAO::analiseDeConta($pronac)); // quantidade de �tens



		// caso o formul�rio seja enviado via post
		if ($this->getRequest()->isPost())
		{
			$post          				= Zend_Registry::get('post');
			$idPlanilha                 = $post->idPlanilha;
			$idPronac                   = $post->idPronac;
			$idRecurso                  = $post->idRecurso;
			$justificativa             	= $post->justificativa;
			$stAtendimento              = $post->stAtendimento;

			try
			{
				// faz o update na tabela recurso
				$dadosRecurso = array('stAtendimento' => $stAtendimento);
				$alterarRecurso = RecursoDAO::avaliarRecurso($dadosRecurso, $idRecurso);

				// desativa a planilha
				$dadosDesativar = array('stAtivo' => 'N');
				$desativar = RecursoDAO::desativarPlanilhaAprovacao($dadosDesativar, $idPlanilha);

				// busca todos os dados da planilha
				$buscar = RecursoDAO::buscarPlanilhaAprovacao($idPlanilha);

				// insere o novo registro na planilha de aprova��o (Ministro)
				$dadosPlanilha = array(
					'tpPlanilha'             => 'MI',
					'dtPlanilha'             => new Zend_Db_Expr('GETDATE()'),
					'idPlanilhaProjeto'      => $buscar[0]->idPlanilhaProjeto,
					'idPlanilhaProposta'     => $buscar[0]->idPlanilhaProposta,
					'IdPRONAC'               => $buscar[0]->IdPRONAC,
					'idProduto'              => $buscar[0]->idProduto,
					'idEtapa'                => $buscar[0]->idEtapa,
					'idPlanilhaItem'         => $buscar[0]->idPlanilhaItem,
					'dsItem'                 => $buscar[0]->dsItem,
					'idUnidade'              => $buscar[0]->idUnidade,
					'qtItem'                 => $buscar[0]->qtItem,
					'nrOcorrencia'           => $buscar[0]->nrOcorrencia,
					'vlUnitario'             => $buscar[0]->vlUnitario,
					'qtDias'                 => $buscar[0]->qtDias,
					'tpDespesa'              => $buscar[0]->tpDespesa,
					'tpPessoa'               => $buscar[0]->tpPessoa,
					'nrContraPartida'        => $buscar[0]->nrContraPartida,
					'nrFonteRecurso'         => $buscar[0]->nrFonteRecurso,
					'idUFDespesa'            => $buscar[0]->idUFDespesa,
					'idMunicipioDespesa'     => $buscar[0]->idMunicipioDespesa,
					'dsJustificativa'        => $justificativa,
					'idAgente'               => $this->getIdUsuario,
					'idPlanilhaAprovacaoPai' => $idPlanilha,
					'idPedidoAlteracao'      => $buscar[0]->idPedidoAlteracao,
					'tpAcao'                 => 'N',
					'idRecursoDecisao'       => $buscar[0]->idRecursoDecisao,
					'stAtivo'                => 'S');

					$cadastrarPlanilha = RecursoDAO::cadastrarPlanilhaAprovacao($dadosPlanilha);

					if ($cadastrarPlanilha)
					{
						parent::message("Dados inseridos com sucesso!", "recurso/orcamento?idPronac=".$idPronac."&idRecurso=".$idRecurso, "CONFIRM");
					}
					else
					{
						throw new Exception("Erro ao alterar planilha!");
					}

			} // fecha try
			catch(Exception $e)
			{
				parent::message($e->getMessage(), "recurso/orcamento?idPronac=".$idPronac."&idRecurso=".$idRecurso, "ERROR");
			}

		}// fecha if
		else
		{
			// recebe os dados via get
			$get       = Zend_Registry::get('get');
			$idPronac  = $get->idPronac;
			$idRecurso = $get->idRecurso;

			try
			{
				if (!isset($idPronac) || empty($idPronac))
				{
					JS::exibirMSG("� necess�rio o n�mero do PRONAC para acessar essa p�gina!");
					JS::redirecionarURL("../");
				}
				else
				{

				} // fecha else
			} // fecha try
			catch (Exception $e)
			{
				parent::message($e->getMessage(), "solicitarrecursodecisao/planilhaorcamentoaprovada?idPronac=".$idPronac."&idRecurso=".$idRecurso, "ERROR");
			}
		} // fecha else
	} // fecha m�todo planilhaorcamentoaprovadaAction()


    public function detalharRecursoAction() {
        $idPronac = $this->_request->getParam("idPronac");

        $tbRecurso = new tbRecurso();
        $dadosRecurso = $tbRecurso->buscarRecursoProjeto($idPronac);
        $this->view->dadosRecurso = $dadosRecurso;
    }

} // fecha class
