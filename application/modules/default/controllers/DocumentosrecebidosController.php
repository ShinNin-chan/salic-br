<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocumentosrecebidosController
 *
 * @author tisomar
 */
class DocumentosrecebidosController extends MinC_Controller_Action_Abstract {

    public function init() {
        // verifica as permiss&otilde;es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 121; // T&eacute;cnico
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }


    public function indexAction()
    {


    }


}