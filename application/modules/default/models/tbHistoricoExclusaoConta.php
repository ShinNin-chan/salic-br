<?php
/**
 * DAO tbHistoricoExclusaoConta
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbHistoricoExclusaoConta extends MinC_Db_Table_Abstract
{
	protected $_banco  = "SAC";
	protected $_schema = "SAC";
	protected $_name   = "tbHistoricoExclusaoConta";

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

}
