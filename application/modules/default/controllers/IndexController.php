<?php
/**
 * Login e autentica��o
 * @author Equipe RUP - Politec
 * @since 20/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

require_once "GenericControllerNew.php";

class IndexController extends GenericControllerNew
{
    /**
     * M�todo principal
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $this->_forward("index", "login");
    }

    /*
     * Pega o IP do usuario
     *
     * @return string
     */
    protected function buscarIp()
    {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ip = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = 'DESCONHECIDO';

        return $ip;
    }

    public function loginUsuarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->renderScript("/index/index.phtml");
    }

    /**
     * Efetua o login no sistema
     * @access public
     * @param void
     * @return void
     */
    public function loginAction(){
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout

        // recebe os dados do formul�rio via post
        $post     = Zend_Registry::get('post');
        $username = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->Login)); // recebe o login sem m�scaras
        $password = $post->Senha; // recebe a senha

        //Pega o IP do usuario

        $ip = $this->buscarIp();

        $tbLoginTentativasAcesso = new tbLoginTentativasAcesso();
        $LoginAttempt = $tbLoginTentativasAcesso->consultarAcessoCpf($username,$ip);


        //Pega timestamp atual
        $data = new Zend_Date();
        $timestamp = $data->getTimestamp();

       // VERIFICA SE USUARIO ESTA BANIDO
        if(isset($LoginAttempt)){

            $TempoBan = $timestamp - strtotime($LoginAttempt->dtTentativa);

            if($TempoBan <= 300 && $LoginAttempt->nrTentativa >= 4)
            {
                parent::message('Acesso bloqueado, aguarde '.gmdate("i", (305 - $TempoBan) ).' minuto(s) e tente novamente!', "/", "ERROR");

            }else{

                try {
                    // valida os dados
                    if (empty($username) || empty($password)) // verifica se os campos foram preenchidos
                    {
                        throw new Exception("Loginf ou Senha inv�lidos!");
                    }
                    else if (strlen($username) == 11 && !Validacao::validarCPF($username)) // verifica se o CPF � v�lido
                    {
                        throw new Exception("O CPF informado � invalido!");
                    }
                    else if (strlen($username) == 14 && !Validacao::validarCNPJ($username)) // verifica se o CNPJ � v�lido
                    {
                        throw new Exception("O CNPJ informado � invalido!");
                    }
                    else {
                        // realiza a busca do usu�rio no banco, fazendo a autentica��o do mesmo
                        $Usuario = new Usuario();
                        $buscar = $Usuario->login($username, $password);



                        if ($buscar) // acesso permitido
                        {
                            $tbLoginTentativasAcesso->removeTentativa($username,$ip);

                            $auth = Zend_Auth::getInstance(); // instancia da autentica��o

                            // registra o primeiro grupo do usu�rio (pega unidade autorizada, org�o e grupo do usu�rio)
                            $Grupo   = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21); // busca todos os grupos do usu�rio

                            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
                            $GrupoAtivo->codGrupo = $Grupo[0]->gru_codigo; // armazena o grupo na sess�o
                            $GrupoAtivo->codOrgao = $Grupo[0]->uog_orgao; // armazena o �rg�o na sess�o
                            $this->orgaoAtivo = $GrupoAtivo->codOrgao;

                            // redireciona para o Controller protegido
                            return $this->_helper->redirector->goToRoute(array('controller' => 'principal'), null, true);
                        } // fecha if
                        else {
                            if($TempoBan > 300){
//                                xd('Remover Tentativa');
                                $tbLoginTentativasAcesso->removeTentativa($username,$ip);
                            }
                            $LoginAttempt = $tbLoginTentativasAcesso->consultarAcessoCpf($username,$ip);

                            //se nenhum registro foi encontrado na tabela Usuario, ele passa a tentar se logar como proponente.
                            //neste ponto o _forward encaminha o processamento para o metodo login do controller login, que recebe
                            //o post igualmente e tenta encontrar usuario cadastrado em SGCAcesso

                            //INSERE OU ATUALIZA O ATUAL ATTEMPT
                            if(!$LoginAttempt) {
                                $tbLoginTentativasAcesso->insereTentativa($username,$ip,$data->get('YYYY-MM-dd HH:mm:ss'));
                            }else{
                                $tbLoginTentativasAcesso->atualizaTentativa($username,$ip,$LoginAttempt->nrTentativa,$data->get('YYYY-MM-dd HH:mm:ss'));
                            }

                            $this->_forward("login", "login");
                            //throw new Exception("Usu�rio inexistente!");
                        }
                    } // fecha else
                } // fecha try
                catch (Exception $e) {
                    parent::message($e->getMessage(), "index", "ERROR");
                }
            }
        }

    } // fecha loginAction



    /**
     * Efetua o logout do sistema
     * @access public
     * @param void
     * @return void
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity(); // limpa a autentica��o
        Zend_Session::destroy();
        unset($_SESSION);
        $this->_redirect('index');
    } // fecha logoutAction



    /**
     * Altera o pefil do usu�rio
     * @access public
     * @param void
     * @return void
     */
    public function alterarperfilAction()
    {
        $get      = Zend_Registry::get('get');
        $codGrupo = $get->codGrupo; // grupo do usu�rio logado
        $codOrgao = $get->codOrgao; // �rg�o do usu�rio logado

        $auth       = Zend_Auth::getInstance(); // pega a autentica��o
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo->codGrupo = $codGrupo; // armazena o grupo ativo na sess�o
        $GrupoAtivo->codOrgao = $codOrgao; // armazena o �rg�o ativo na sess�o

        if($GrupoAtivo->codGrupo == "1111" && $GrupoAtivo->codOrgao == "2222"){
            $auth   = Zend_Auth::getInstance();
            $tblSGCacesso = new Sgcacesso();
            $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? "=>$auth->getIdentity()->usu_identificacao))->current()->toArray();
            $objAuth = $auth->getStorage()->write((object)$rsSGCacesso);

            $_SESSION["GrupoAtivo"]["codGrupo"] = $GrupoAtivo->codGrupo;
            $_SESSION["GrupoAtivo"]["codOrgao"] = $GrupoAtivo->codOrgao;
            parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principalproponente", "ALERT");
        }

        //Reescreve a sessao com o novo orgao superior
        $tblUsuario = new Usuario();
        $codOrgaoMaxSuperior = $tblUsuario->recuperarOrgaoMaxSuperior($codOrgao);
        $_SESSION['Zend_Auth']['storage']->usu_org_max_superior = $codOrgaoMaxSuperior;

        // redireciona para a p�gina inicial do sistema
        parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principal", "ALERT");
    } // fecha alterarPerfilAction()


    public function verificamensagemusuarioAction(){
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $usuario = new Usuario();
        $pr = new Projetos();
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Agente = $usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idAgente = $Agente['idAgente'];
        $camMensagem = getcwd().'/public/mensagem/mensagem-destinatario-'.$idAgente.'.txt';
        $verificarmensagem = array();
        if (file_exists($camMensagem)) {
            $read = fopen($camMensagem, 'r');
            if ($read) {
                while (($buffer = fgets($read, 4096)) !== false) {
                    $verificarmensagem[] = json_decode($buffer, true);
                }
                fclose($read);
            }
        }
        $qtdmensagem = count($verificarmensagem);
//                xd($verificarmensagem);
        if($qtdmensagem > 0){
            $a = 0;
            $idpronac = 0;
            $mensagem = array();
            foreach($verificarmensagem as $resu){
                if($resu['status']== 'N' and $resu['idpronac'] != $idpronac and $GrupoAtivo->codGrupo == $resu['perfilDestinatario']){
                    $mensagem[$a]['idpronac'] = $resu['idpronac'];
                    $buscarpronac = $pr->buscar(array('IdPRONAC = ?'=>$resu['idpronac']))->current();
                    $mensagem[$a]['pronac'] = $buscarpronac->AnoProjeto.$buscarpronac->Sequencial;
                    $a++;
                    $idpronac = $resu['idpronac'];
                }
            }
            echo count($mensagem) > 0 ? json_encode($mensagem) : json_encode(array('error'=>true));
        }
        else{
            echo json_encode(array('error'=>true));
        }
        exit();
    }


    public function montarPlanilhaOrcamentariaAction() {

        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $get = Zend_Registry::get('get');

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $this->view->idPronac = $get->idPronac;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($get->idPronac, $get->tipoPlanilha);
        $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $get->tipoPlanilha);

        // tipoPlanilha = 0 : Planilha Or�ament�ria da Proposta
        // tipoPlanilha = 1 : Planilha Or�ament�ria do Proponente
        // tipoPlanilha = 2 : Planilha Or�ament�ria do Parecerista
        // tipoPlanilha = 3 : Planilha Or�ament�ria Aprovada Ativa
        // tipoPlanilha = 4 : Cortes Or�ament�rios Aprovados
        // tipoPlanilha = 5 : Remanejamento menor que 20%
        // tipoPlanilha = 6 : Readequa��o

        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'index/montar-planilha-orcamentaria.phtml', array(
                'tipoPlanilha' => $get->tipoPlanilha,
                'tpPlanilha' => (count($planilhaOrcamentaria)>0) ? isset($planilhaOrcamentaria[0]->tpPlanilha) ? $planilhaOrcamentaria[0]->tpPlanilha : '' : '',
                'planilha' => $planilha,
                'link' => $link
            )
        );
    }

} // fecha class