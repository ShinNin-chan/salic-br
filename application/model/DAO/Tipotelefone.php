<?php
/**
 * Modelo Tipotelefone
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Tipotelefone extends Zend_Db_Table
{
	protected $_name = 'AGENTES.dbo.Verificacao'; // nome da tabela



	/**
	 * M�todo para buscar todos os tipos de telefones
	 * @access public
	 * @param void
	 * @return object $db->fetchAll($sql)
	 */
	public static function buscar()
	{
		$sql = "SELECT idVerificacao AS id, Descricao AS descricao ";
		$sql.= "FROM AGENTES.dbo.Verificacao ";
		$sql.= "WHERE idTipo = 3 ";
		$sql.= "ORDER BY Descricao;";

		try
		{
			$db  = Zend_Registry::get('db');
			$db->setFetchMode(Zend_DB::FETCH_OBJ);
		}
		catch (Zend_Exception_Db $e)
		{
			$this->view->message = "Erro ao buscar Tipos de Telefones: " . $e->getMessage();
		}

		return $db->fetchAll($sql);
	} // fecha buscar()

} // fecha class