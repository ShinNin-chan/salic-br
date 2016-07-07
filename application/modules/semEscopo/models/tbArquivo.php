<?php
/**
 * DAO tbArquivo
 * @author emanuel.sampaio - Politec
 * @since 19/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbArquivo extends GenericModel
{
	protected $_banco  = "BDCORPORATIVO";
	protected $_schema = "scCorp";
	protected $_name   = "tbArquivo";



	/**
	 * M�todo para buscar um arquivo pelo seu id
	 * @access public
	 * @param integer $idArquivo
	 * @return array
	 */
	public function buscarDados($idArquivo)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(
			array("a" => $this->_name),
			array("a.idArquivo"
				,"a.nmArquivo"
				,"a.sgExtensao"
				,"a.nrTamanho"
				,"a.dtEnvio"
				,"a.dsHash"
				,"a.stAtivo"
				,"a.dsTipoPadronizado"
				,"a.idUsuario"),'BDCORPORATIVO.scCorp'
		);
		$select->joinInner(
			array("i" => "tbArquivoImagem")	,"a.idArquivo = i.idArquivo",
			array("i.biArquivo"),'BDCORPORATIVO.scCorp'
		);

		$select->where("a.idArquivo = ?", $idArquivo);

		return $this->fetchRow($select);
	} // fecha m�todo buscarDados()
	

	public static function buscarArquivo($id)
	{
		$sql = "SELECT A.nmArquivo, AI.biArquivo 
				FROM BDCORPORATIVO.scCorp.tbArquivo A 
				INNER JOIN BDCORPORATIVO.scCorp.tbArquivoImagem AI ON AI.idArquivo = A.idArquivo
				WHERE A.idArquivo = ".$id;
							
		$db = Zend_Registry::get('db');
		$db->setFetchMode(Zend_DB::FETCH_OBJ);
		
		$resultado = $db->fetchAll("SET TEXTSIZE 10485760");
		$resultado = $db->fetchAll($sql);
		return $resultado;
			
	}
	
	
	
	
	
	
	/**
	 * M�todo para buscar o ultimo registro
	 * @access public
	 * @return int
	 */
	public function buscarUltimo()
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		
		$select->from(array($this->_name),
					  array('*'),'BDCORPORATIVO.scCorp');
		
		
		$select->order('idArquivo desc');
		$select->limit('1', '0');
			
		return $this->fetchRow($select)->toArray();
	} // fecha m�todo buscarDados()



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
		$where = "idArquivo = " . $where;
		return $this->update($dados, $where);
	} // fecha m�todo alterarDados()



	/**
	 * M�todo para excluir
	 * @access public
	 * @param integer $where
	 * @return integer (quantidade de registros exclu�dos)
	 */
	public function excluirDados($where)
	{
		$where = "idArquivo = " . $where;
		return $this->delete($where);
	} // fecha m�todo excluirDados()



	/**
	 * M�todo para verificar se o arquivo existe (pelo hash)
	 * @access public
	 * @param string $dsHash
	 * @return array || bool
	 */
	public function verificarHash($dsHash)
	{
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from($this, "idArquivo");

		$select->where("dsHash = ?", $dsHash);

		return $this->fetchRow($select);
	} // fecha m�todo verificarHash()



        public function excluir($where)
        {
            return $this->delete($where);
        }
} // fecha class