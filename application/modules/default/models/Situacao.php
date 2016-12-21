<?php
class Situacao extends MinC_Db_Table_Abstract {

    protected $_schema = 'SAC';
    protected $_name = 'Situacao';
    
    public function listasituacao($codigosituacao=array())
	{
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                        array('sit'=>$this->_name)
                      );
            foreach($codigosituacao as $situacao)
            {
            	$select->orwhere('sit.Codigo = ?', $situacao);
            }
            return $this->fetchAll($select);
	} // fecha m�todo listasituacao()
        
}
?>
