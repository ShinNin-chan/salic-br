<?php
/**
 * Login e autentica��o
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 20/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 */

class Autenticacao_PerfilController extends MinC_Controller_Action_Abstract
{
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
            $tblSGCacesso = new Autenticacao_Model_Sgcacesso();
            $cpf = $auth->getIdentity()->usu_identificacao;
            //$cpf = '00154249017';
            $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? " => $cpf))->current()->toArray();
            $objAuth = $auth->getStorage()->write((object)$rsSGCacesso);

            $_SESSION["GrupoAtivo"]["codGrupo"] = $GrupoAtivo->codGrupo;
            $_SESSION["GrupoAtivo"]["codOrgao"] = $GrupoAtivo->codOrgao;
            parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principalproponente", "ALERT");
        }

        //Reescreve a sessao com o novo orgao superior
        $tblUsuario = new Autenticacao_Model_Usuario();
        $codOrgaoMaxSuperior = $tblUsuario->recuperarOrgaoMaxSuperior($codOrgao);
        $_SESSION['Zend_Auth']['storage']->usu_org_max_superior = $codOrgaoMaxSuperior;

        // redireciona para a p�gina inicial do sistema
        parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principal", "ALERT");
    }
}
