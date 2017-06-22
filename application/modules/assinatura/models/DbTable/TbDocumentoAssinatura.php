<?php

class Assinatura_Model_DbTable_TbDocumentoAssinatura extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'sac';
    protected $_name      = 'tbDocumentoAssinatura';


    public function obterDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo) {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            '*',
            $this->_schema
        );
        $objQuery->where('IdPRONAC = ?', $idPronac);
        $objQuery->where('idTipoDoAtoAdministrativo = ?', $idTipoDoAtoAdministrativo);

        $result = $this->fetchRow($objQuery);
        if($result) {
            return $result->toArray();
        }
    }

    public function obterProjetosEncaminhadosParaAssinatura($codOrgao = null, $ordenacao = array())
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);

        $queryPlanilhaOrcamentaria = $this->select();
        $queryPlanilhaOrcamentaria->setIntegrityCheck(false);
        $queryPlanilhaOrcamentaria->from('tbPlanilhaAprovacao',
            array(
                "vlAprovado" => New Zend_Db_Expr(
                    "tbPlanilhaAprovacao.vlUnitario * tbPlanilhaAprovacao.qtItem * tbPlanilhaAprovacao.nrOcorrencia"
                )
            ), $this->_schema
        );
        $queryPlanilhaOrcamentaria->where("tbPlanilhaAprovacao.IdPRONAC = projetos.IdPRONAC");

        $query->from(
            array("Projetos" => "Projetos"),
            array(
                'pronac' => New Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.UfProjeto',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao',
                'Projetos.Situacao',
                'Projetos.DtSituacao',
                'Projetos.Orgao',
                'tbDocumentoAssinatura.cdSituacao',
                'dias' => 'DATEDIFF(DAY, projetos.DtSituacao, GETDATE())',
                '(' . $queryPlanilhaOrcamentaria->assemble() . ') as vlAprovado'
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Area' => 'Area'),
            "Area.Codigo = Projetos.Area",
            "Area.Descricao as area",
            $this->_schema
        );

        $query->joinInner(
            array('Segmento' => 'Segmento'),
            "Segmento.Codigo = Projetos.Segmento",
            array(
                "Segmento.Descricao as segmento",
                "Segmento.tp_enquadramento"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('tbDocumentoAssinatura' => 'tbDocumentoAssinatura'),
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            array(
                "tbDocumentoAssinatura.idTipoDoAtoAdministrativo"
            ),
            $this->_schema
        );

        $query->joinLeft(
            array('Enquadramento' => 'Enquadramento'),
            "Enquadramento.IdPRONAC = Projetos.IdPRONAC
             AND Enquadramento.AnoProjeto = Projetos.AnoProjeto
             AND Enquadramento.Sequencial = Projetos.Sequencial",
            array("Enquadramento.IdEnquadramento"),
            $this->_schema
        );

        if ($codOrgao) {
            $query->where("Projetos.Orgao = ?", $codOrgao);
        }

        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->where("Projetos.Situacao in (?)", array('B04'));
        $query->order($ordenacao);
//xd($this->_db->fetchAll($query));
        return $this->_db->fetchAll($query);
    }

    public function obterProjetosComAssinaturasAbertas($codOrgao = null, $ordenacao = array())
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);

        $query->from(
            array("Projetos" => "Projetos"),
            array(
                'pronac' => New Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.UfProjeto',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao',
                'Projetos.Situacao',
                'Projetos.DtSituacao',
                'Projetos.Orgao',
                'tbDocumentoAssinatura.cdSituacao'
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Area' => 'Area'),
            "Area.Codigo = Projetos.Area",
            "Area.Descricao as area",
            $this->_schema
        );

        $query->joinInner(
            array('Segmento' => 'Segmento'),
            "Segmento.Codigo = Projetos.Segmento",
            array(
                "Segmento.Descricao as segmento",
                "Segmento.tp_enquadramento"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('tbDocumentoAssinatura' => 'tbDocumentoAssinatura'),
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            array(
                "tbDocumentoAssinatura.idTipoDoAtoAdministrativo"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Verificacao' => 'Verificacao'),
            "Verificacao.idVerificacao = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            'Verificacao.Descricao as tipoDoAtoAdministrativo',
            $this->_schema
        );

        $query->joinLeft(
            array('Enquadramento' => 'Enquadramento'),
            "Enquadramento.IdPRONAC = Projetos.IdPRONAC
             AND Enquadramento.AnoProjeto = Projetos.AnoProjeto
             AND Enquadramento.Sequencial = Projetos.Sequencial",
            array("Enquadramento.IdEnquadramento"),
            $this->_schema
        );

        if ($codOrgao) {
            $query->where("Projetos.Orgao = ?", $codOrgao);
        }

        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->order($ordenacao);

        return $this->_db->fetchAll($query);
    }
}