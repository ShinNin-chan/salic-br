<?php

/**
 * Class Proposta_Model_DbTable_DocumentosExigidos
 *
 * @name Proposta_Model_DbTable_DocumentosExigidos
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 *
    Codigo
    Descricao
    Area
    Opcao
    stEstadostUpload
 */
class Proposta_Model_DbTable_DocumentosExigidos extends MinC_Db_Table_Abstract
{
    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'documentosexigidos';

    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'sac';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'Codigo';

    /**
     * Realizando a busca na view: vwdocumentosexigidosapresentacaoproposta
     * Futuramente deletar este metodo junto com a view, pois nao tem nessecidade desta view por ser muito simples.
     *
     * @name buscarDocumentoOpcao
     * @param $idOpcao
     * @return array
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  28/09/2016
     */
    public function buscarDocumentoOpcao($idOpcao)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->getName('vwdocumentosexigidosapresentacaoproposta'),
                ['codigo', 'descricao'],
                $this->_schema)
            ->where('opcao = ?', $idOpcao)
            ->order('descricao');
        $result = $this->fetchAll($select);
        return ($result)? $result->toArray() : array();
    }

    public function buscarDocumentoPendente($idPreProjeto){
        $selectProponente = $this->select()
            ->setIntegrityCheck(false)
//            ->from(['dp' => 'documentosproponente'], ['idprojeto', 'contador', 'codigodocumento', 'opcao'], $this->_schema)
            ->from(['dp' => 'documentosproponente'], ['contador', 'codigodocumento'], $this->_schema)
            ->joinInner(['d' => 'documentosexigidos'], 'dp.codigodocumento = d.codigo', ['opcao'], $this->_schema)
            ->joinInner(['p' => 'preprojeto'], 'dp.idprojeto = p.idpreprojeto', null, $this->_schema)
            ->joinInner(['m' => 'tbmovimentacao'], 'm.idprojeto = p.idpreprojeto', ['idprojeto'], $this->_schema)
            ->where('movimentacao IN (?)', [97, 95])
            ->where('m.stestado = ?', 0)
            ->where('m.idprojeto = ?', (int)$idPreProjeto)
        ;

        $selectProjeto = $this->select()
            ->setIntegrityCheck(false)
//            ->from(['dpr' => 'documentosprojeto'], ['idprojeto', 'contador', 'codigodocumento', 'opcao'], $this->_schema)
            ->from(['dpr' => 'documentosprojeto'], ['contador', 'codigodocumento'], $this->_schema)
            ->joinInner(['d' => 'documentosexigidos'], 'dpr.codigodocumento = d.codigo', ['opcao'], $this->_schema)
            ->joinInner(['p' => 'preprojeto'], 'dpr.idprojeto = p.idpreprojeto', null, $this->_schema)
            ->joinInner(['m' => 'tbmovimentacao'], 'm.idprojeto = p.idpreprojeto', ['idprojeto'], $this->_schema)
            ->where('movimentacao IN (?)', [97, 95])
            ->where('m.stestado = ?', 0)
            ->where('m.idprojeto = ?', (int)$idPreProjeto)
        ;

        $select = $this->select()
            //->distinct()
            ->union(array($selectProponente, $selectProjeto))
//            ->joinLeft(['doc' => 'documentosexigidos'], 'vdoc.codigodocumento = doc.codigo', '*', $this->_schema)

//                        left join SAC.dbo.DocumentosExigidos doc on vdoc.CodigoDocumento = doc.Codigo
        ;

        $resultado = $this->fetchAll($select);
        return $resultado;
    }
}
