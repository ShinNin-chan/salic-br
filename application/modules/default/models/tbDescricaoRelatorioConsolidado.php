<?php
/**
 * DAO tbDescricaoRelatorioConsolidado
 * @since 16/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbDescricaoRelatorioConsolidado extends GenericModel
{
	protected $_banco  = "SAC";
	protected $_schema = "dbo";
	protected $_name   = "tbDescricaoRelatorioConsolidado";

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
        

} // fecha class
?>