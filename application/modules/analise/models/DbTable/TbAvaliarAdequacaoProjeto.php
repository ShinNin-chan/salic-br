<?php

class Analise_Model_DbTable_TbAvaliarAdequacaoProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbAvaliarAdequacaoProjeto';
    protected $_primary = 'idAvaliarAdequacaoProjeto';


    public function buscarUltimaAvaliacao($idPronac)
    {
        $avaliacao = $this->buscar(array('idPronac = ?' => $idPronac), array('idAvaliarAdequacaoProjeto DESC'), 1)->toArray();
        return $avaliacao[0];
    }

    public function inserirAvaliacao($idPronac, $orgaoUsuario, $idTecnico = null)
    {
        if (empty($idTecnico))
            $idTecnico = new Zend_Db_Expr('sac.dbo.fnPegarTecnico(110, ' . $orgaoUsuario . ' ,1)');

        $dados = array(
            'idPronac' => $idPronac,
            'dtEncaminhamento' => $this->getExpressionDate(),
            'idTecnico' => $idTecnico,
            'dtAvaliacao' => null,
            'dsAvaliacao' => null,
            'siEncaminhamento' => 1,
            'stAvaliacao' => 0,
            'stEstado' => 1
        );

        return $this->insert($dados);
    }

    public function atualizarAvaliacaoNegativa($idPronac, $idTecnico, $avaliacao)
    {
        $dados = array(
            'dtAvaliacao' => $this->getExpressionDate(),
            'dsAvaliacao' => $avaliacao,
            'siEncaminhamento' => 16,
            'stAvaliacao' => 2,
            'stEstado' => 0
        );

        $where = array(
            'idPronac = ?' => $idPronac,
            'stEstado = ?' => 1,
            'siEncaminhamento = ?' => 1,
            'idTecnico = ?' => $idTecnico
        );

        return $this->update($dados, $where);
    }


    public function atualizarAvaliacaoPositiva($idPronac, $idTecnico, $avaliacao)
    {
        $dados = array(
            'dtAvaliacao' => $this->getExpressionDate(),
            'dsAvaliacao' => $avaliacao,
            'siEncaminhamento' => 15,
            'stAvaliacao' => 1,
            'stEstado' => 0
        );

        $where = array(
            'idPronac = ?' => $idPronac,
            'stEstado = ?' => 1,
            'siEncaminhamento = ?' => 1,
            'idTecnico = ?' => $idTecnico
        );

        return $this->update($dados, $where);
    }

}