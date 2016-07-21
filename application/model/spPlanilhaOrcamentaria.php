<?php

/**
 * Description of spPlanilhaOrcamentaria
 * Criado em 01/10/2013 - Jefferson Alessandro
 */
class spPlanilhaOrcamentaria extends GenericModel {
        
    protected $_banco = 'SAC';
    protected $_name  = 'spPlanilhaOrcamentaria';

    public function exec($idPronac, $tipoPlanilha){

        // tipoPlanilha = 0 : Planilha Or�ament�ria da Proposta
        // tipoPlanilha = 1 : Planilha Or�ament�ria do Proponente
        // tipoPlanilha = 2 : Planilha Or�ament�ria do Parecerista
        // tipoPlanilha = 3 : Planilha Or�ament�ria Aprovada Ativa
        // tipoPlanilha = 4 : Cortes Or�ament�rios Aprovados
        // tipoPlanilha = 5 : Remanejamento menor que 20%
        // tipoPlanilha = 6 : Readequa��o

        $sql = sprintf("exec $this->_banco.dbo.$this->_name %d, %d",$idPronac,$tipoPlanilha);

        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
?>
