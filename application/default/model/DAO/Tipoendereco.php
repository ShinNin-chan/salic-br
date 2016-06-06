<?php
/**
 * Modelo Tipoendereco
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Tipoendereco extends Zend_Db_Table
{
	protected $_name = 'AGENTES.dbo.Verificacao'; // nome da tabela



	/**
	 * M�todo para buscar todos os tipos de endere�os
	 * @access public
	 * @param void
	 * @return object $db->fetchAll($sql)
	 */
	public static function buscar()
	{
		$sql = "SELECT idVerificacao AS id, descricao ";
		$sql.= "FROM AGENTES.dbo.Verificacao ";
		$sql.= "WHERE idTipo = 2 ";
		$sql.= "ORDER BY descricao;";

		try
		{
			$db  = Zend_Registry::get('db');
			$db->setFetchMode(Zend_DB::FETCH_OBJ);
		}
		catch (Zend_Exception_Db $e)
		{
			$this->view->message = "Erro ao buscar Tipos de Endere�os: " . $e->getMessage();
		}

		return $db->fetchAll($sql);
	} // fecha buscar()
} // fecha class