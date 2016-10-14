<?php
/**
 * Modelo Estado
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class EstadoDAO extends MinC_Db_Table_Abstract
{
	protected $_name = 'uf'; // nome da tabela
	protected $_schema = 'agentes'; // nome da tabela

	/**
	 * M�todo para buscar os estados
	 * @access public
	 * @param void
	 * @return object $db->fetchAll($sql)
	 */
	public function buscar($id = null)
	{
		$sql = "SELECT idUF AS id, Sigla AS descricao ";
		$sql.= "FROM AGENTES.dbo.UF ";

		if (!empty($id))
		{
			$sql.= "WHERE idUF = {$id} ";
		}

		$sql.= "ORDER BY Sigla";

		try
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->setFetchMode(Zend_DB::FETCH_OBJ);
		}
		catch (Zend_Exception_Db $e)
		{
			$this->view->message = "Erro ao buscar Estados: " . $e->getMessage();
		}
		//xd($sql);
		return $db->fetchAll($sql);
	} // fecha buscar()

    public function listar($id = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from($this->_name, ['iduf AS id', 'sigla AS descricao'], $this->_schema);

        if (!empty($id))
        {
            $sql->where('idUF = ?', $id);
        }

        return $db->fetchAll($sql);
    }
}
