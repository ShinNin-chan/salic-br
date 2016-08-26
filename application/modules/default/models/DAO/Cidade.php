<?php
/**
 * Modelo Cidade
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @link http://www.cultura.gov.br
 */

class Cidade extends Zend_Db_Table
{
    protected $_name = 'municipios';
    protected $_schema = 'agentes';

    /**
     * M�todo para buscar as cidades de um determinado estado
     * @access public
     * @param integer $idUF
     * @return object $db->fetchAll($sql)
     */
    public static function buscar($idUF)
    {
        $sql = "SELECT idMunicipioIBGE AS id, Descricao AS descricao ";
        $sql.= "FROM AGENTES.dbo.Municipios ";
        $sql.= "WHERE idUFIBGE = " . $idUF . " ";
        $sql.= "ORDER BY Descricao;";

        try
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        }
        catch (Zend_Exception_Db $e)
        {
            $this->view->message = "Erro ao buscar Cidades: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
