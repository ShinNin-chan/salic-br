<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paUsuariosDoPerfil
 *
 * @author 01129075125
 */
class paUsuariosDoPerfil extends MinC_Db_Table_Abstract {
        
    protected $_banco = 'SAC';
    protected $_name  = 'paUsuariosDoPerfil';

    public function buscarUsuarios($codPerfil, $codOrgao){
        throw new Exception('Metodo transferido para vwUsuariosGrupos');
        $sql = "exec SAC.dbo.paUsuariosDoPerfil $codPerfil, $codOrgao ";
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
?>
