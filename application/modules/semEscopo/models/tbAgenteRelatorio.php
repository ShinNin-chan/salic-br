<?php
/**
 * DAO tbAgenteRelatorio
 * @since 16/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbAgenteRelatorio extends GenericModel
{
	protected $_banco  = "SAC";
	protected $_schema = "dbo";
	protected $_name   = "tbAgenteRelatorio";


	/**
	 * M�todo para cadastrar
	 * @access public
	 * @param array $dados
	 * @return integer (retorna o �ltimo id cadastrado)
	 */
	public function cadastrarDados($dados)
	{
		return $this->insert($dados);
	} // fecha m�todo cadastrarDados()



	/**
	 * M�todo para alterar
	 * @access public
	 * @param array $dados
	 * @param integer $where
	 * @return integer (quantidade de registros alterados)
	 */
	public function alterarDados($dados, $where)
	{
		$where = "idAgenteRelatorio = " . $where;
		return $this->update($dados, $where);
	} // fecha m�todo alterarDados()



} // fecha class