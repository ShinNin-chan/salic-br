<?php

/**
 * DAO Arquivo
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class ArquivoDAO extends Zend_Db_Table
{
    /* dados da tabela */

    protected $_schema = "";
    protected $_name = "bdcorporativo.scCorp.tbArquivo";
    protected $_primary = "idArquivo";

    /**
     * M�todo para cadastrar informa��es dos arquivos
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("bdcorporativo.scCorp.tbArquivo", $dados);

        return $cadastrar ? true : false;
    }

    /**
     * M�todo para alterar informa��es dos arquivos
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
     * @return bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idArquivo = $id";
        $alterar = $db->update("bdcorporativo.scCorp.tbArquivo", $dados, $where);

        return $alterar ? true : false;
    }

    /**
     * 
     */
    public static function deletar($idArquivo)
    {
        Zend_Registry::get('db')->exec("delete from bdcorporativo.scCorp.tbArquivoImagem WHERE idArquivo = {$idArquivo}");
        Zend_Registry::get('db')->exec("delete from bdcorporativo.scCorp.tbArquivo WHERE idArquivo = {$idArquivo}");
    }

    /**
     * M�todo para verificar se o arquivo existe (pelo hash)
     * @access public
     * @static
     * @param string $dsHash
     * @return object || bool
     */
    public static function verificarHash($dsHash)
    {
        $sql = "SELECT * FROM bdcorporativo.scCorp.tbArquivo WHERE dsHash = '$dsHash'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para buscar o id do �ltimo arquivo cadastrado
     * @access public
     * @static
     * @param void
     * @return object || integer
     */
    public static function buscarIdArquivo()
    {
        $sql = "SELECT MAX(idArquivo) AS id FROM bdcorporativo.scCorp.tbArquivo";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

}

/**
 * DAO ArquivoImagem
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class ArquivoImagemDAO extends Zend_Db_Table
{
    /* dados da tabela */

    protected $_schema = "";
    protected $_name = "bdcorporativo.scCorp.tbArquivoImagem";
    protected $_primary = "idArquivo";

    /**
     * M�todo para cadastrar os bin�rios dos arquivos
     * @access public
     * @static
     * @param array $dados
     * @return object || bool
     */
    public static function cadastrar($dados)
    {
        /* @var $db Zend_Db_Adapter_Sqlsrv */
        $db= Zend_Db_Table::getDefaultAdapter();
        $dados['biArquivo'] = new Zend_Db_Expr("CONVERT(varbinary(MAX),{$dados['biArquivo']})");
        return $db->insert('bdcorporativo.scCorp.tbArquivoImagem', $dados);
    }

    /**
     * M�todo para alterar os bin�rios dos arquivos
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
     * @return object || bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "UPDATE bdcorporativo.scCorp.tbArquivoImagem SET 
            biArquivo = {$dados['biArquivo']} WHERE idArquivo = $id";

        return $db->fetchAll($sql);
    }
}
