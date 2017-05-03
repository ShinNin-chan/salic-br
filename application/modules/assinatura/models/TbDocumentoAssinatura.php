<?php

class Assinatura_Model_TbDocumentoAssinatura extends MinC_Db_Model
{
    protected $_idDocumentoAssinatura;
    protected $_IdPRONAC;
    protected $_idTipoDoAtoAdministrativo;
    protected $_conteudo;
    protected $_dt_criacao;
    protected $_idCriadorDocumento;
    protected $_cdSituacao;

    /**
     * @return mixed
     */
    public function getCdSituacao()
    {
        return $this->_cdSituacao;
    }

    /**
     * @param mixed $cdSituacao
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setCdSituacao($cdSituacao)
    {
        $this->_cdSituacao = $cdSituacao;
        return $this;
    }

    /*
     * @var string CD_SITUACAO_ABERTO Disponível para assinatura
     */
    const CD_SITUACAO_ABERTO = 1;

    /*
     * @var string CD_SITUACAO_FINALIZADO Fechado para assinatura
     */
    const CD_SITUACAO_FINALIZADO = 2;

    /**
     * @return mixed
     */
    public function getIdDocumentoAssinatura()
    {
        return $this->_id_documento_assinatura;
    }

    /**
     * @param mixed $id_documento_assinatura
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdDocumentoAssinatura($id_documento_assinatura)
    {
        $this->_id_documento_assinatura = $id_documento_assinatura;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPRONAC()
    {
        return $this->_IdPRONAC;
    }

    /**
     * @param mixed $IdPRONAC
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdPRONAC($IdPRONAC)
    {
        $this->_IdPRONAC = $IdPRONAC;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdTipoDoAtoAdministrativo()
    {
        return $this->_idTipoDoAtoAdministrativo;
    }

    /**
     * @param mixed $idTipoDoAtoAdministrativo
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdTipoDoAtoAdministrativo($idTipoDoAtoAdministrativo)
    {
        $this->_idTipoDoAtoAdministrativo = $idTipoDoAtoAdministrativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConteudo()
    {
        return $this->_conteudo;
    }

    /**
     * @param mixed $conteudo
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setConteudo($conteudo)
    {
        $this->_conteudo = $conteudo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtCriacao()
    {
        return $this->_dt_criacao;
    }

    /**
     * @param mixed $dt_criacao
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setDtCriacao($dt_criacao)
    {
        $this->_dt_criacao = $dt_criacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCriadorDocumento()
    {
        return $this->_idCriadorDocumento;
    }

    /**
     * @param mixed $idCriadorDocumento
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdCriadorDocumento($idCriadorDocumento)
    {
        $this->_idCriadorDocumento = $idCriadorDocumento;
        return $this;
    }


}
