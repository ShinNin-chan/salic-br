<?php

class Autenticacao_Model_FnVerificarPermissao extends MinC_Db_Table_Abstract {

    protected $_banco = 'SAC';
    protected $_name = 'dbo.fnVerificarPermissao';

    public function verificarPermissaoProjeto($idPronac, $idUsuarioLogado) {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao(2,'',$idUsuarioLogado,$idPronac) as Permissao");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }

    /**
     * verificarPermissaoProposta
     *
     * @param mixed $idPreProjeto
     * @param mixed $idUsuarioLogado
     * @access public
     * @return void
     * @todo Verificar local para metodo.
     * SAC.dbo.fnVerificarPermissao --> SP removida
     */
    public function verificarPermissaoProposta($idPreProjeto, $idUsuarioLogado)
    {
        //$select = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao(1,'',$idUsuarioLogado,$idPreProjeto) as Permissao");
        $db = Zend_Db_Table::getDefaultAdapter();

        $acao = 1;
        $cpfCnpjProponente = 0;
        $permissao = 0;

        //SELECT @CPF_Logado = CPF FROM CONTROLEDEACESSO.dbo.SGCacesso WHERE IdUsuario = @idUsuario_Logado
        $sql = $db->select()
            ->from('sgcacesso', 'cpf', $this->getSchema('controledeacesso'))
            ->where('IdUsuario = ?', $idUsuarioLogado)
        ;
        $cpfLogado = $db->fetchRow($sql);

        //VERIFICAR SE O CPF LOGADO ESTA CADASTRADO NO BANCO AGENTES
        $sql = $db->select()
            ->from('agentes', 'idagente', $this->getSchema('agentes'))
            ->where('CNPJCPF = ?', $cpfLogado['cpf'])
        ;
        $idAgenteLogado = $db->fetchRow($sql);

        $idAgenteLogado = empty($idAgenteLogado) ? 0 : $idAgenteLogado;

         //PEGAR ID DO PROPONENTE E O TIPO DE PESSOA
        $sql = $db->select()
            ->from('agentes', ['idagente','tipopessoa'], $this->getSchema('agentes'))
            ->where('CNPJCPF = ?', $cpfLogado['cpf'])
        ;

        $proponente = $db->fetchRow($sql);
        $proponente = empty($proponente) ? 0 :$proponente ;

        switch($acao){
        //-- CHECAR PERMISSAO PARA ADMINISTRATIVO
        case 0 :
            if ($proponente['tipopessoa'] == 0) {
                if ($cpfLogado['cpf'] == $cpfCnpjProponente) {
                    $permissao = 1;
                } else {
                    $permissao = 0;
                }
            } else {
                $sql = $db->select()
                    ->from(['a' => 'vinculacao'], null, $this->getSchema('agentes'))
                    ->join(['b' => 'agentes'], '(a.idagente = b.idagente)', 'b.cnpjcpf' ,$this->getSchema('agentes'))
                    ->join(['c' => 'agentes'], '(a.idvinculoprincipal = c.idagente)', null, $this->getSchema('agentes'))
                    ->join(['d' => 'visao'], '(d.idagente = a.idagente)', null,$this->getSchema('agentes'))
                    ->where('b.cnpjcpf = ?', $cpfLogado['cpf'])
                    ->where('c.cnpjcpf = ?', $cpfCnpjProponente)
                    ->where('d.visao = 198')
                    ;

                $cpfDirigente = $db->fetchRow($sql);

                if (!empty($cpfDirigente)) {
                    if($cpfLogado['cpf'] == $cpfDirigente['cnpjcpf']) {
                        $permissao = 1;
                    } else {
                        $permissao = 0;
                    }
                } else {
                    $permissao = 0;
                }
            }
            break;
        case 1:
            $sql = $db->select()
                ->from(['a' => 'preprojeto'], ['a.idagente', 'a.idusuario'], $this->getSchema('sac'))
                ->join(['b' => 'agentes'], '(a.idagente = b.idagente)', ['b.cnpjcpf', 'b.tipopessoa'], $this->getSchema('agentes'))
                ->where('idpreprojeto = ?', $idPreProjeto)
                ;

            $agente = $db->fetchRow($sql);

            if ($agente['tipopessoa'] == 0) {
                if ($cpfLogado['cpf'] == $agente['cnpjcpf'] ||  $agente['a.idusuario'] == $idUsuarioLogado) {
                    $permissao = 1;
                } else {
                    $permissao = 0;
                }
            } else {
                $sql = $db->select()
                    ->from(['a' => 'vinculacao'], null, $this->getSchema('agentes'))
                    ->join(['b' => 'agentes'], '(a.idagente = b.idagente)', 'b.cnpjcpf', $this->getSchema('agentes'))
                    ->join(['c' => 'agentes'], '(a.idvinculoprincipal = c.idagente)', null, $this->getSchema('agentes'))
                    ->join(['d' => 'visao'], '(d.idagente = a.idagente)', null, $this->getSchema('agentes'))
                    ->where('b.cnpjcpf = ?', $cpfLogado['cpf'] )
                    ->where('c.cnpjcpf = ?', $cpfCnpjProponente)
                    ->where('d.visao = 198')
                    ;
                    $dirigenteCpf = $db->fetchRow($sql);

                    if (!empty($dirigenteCpf)) {
                       //IF @CPF_Logado = @CPF_Dirigente or @idUsuario_Responsavel = @idUsuario_Logado
                        if ($cpfLogado['cpf'] == $dirigenteCpf['b.cnpjcpf'] || $agente['a.idusuario'] == $idUsuarioLogado) {
                            $permissao = 1;
                        } else {
                            $permissao = 0;
                        }
                    } else {
                        $permissao = 0;
                    }

                    if ($permissao == 0) {
                        $sql = $db->select()
                            ->from(['a' => 'preprojeto'], 'a.idAgente', $this->getSchema('sac'))
                            ->join(['b' => 'agentes'], '(a.idAgente = b.idAgente)', null, $this->getSchema('agentes'))
                            ->join(['c' => 'tbvinculoproposta'], '(a.idPreProjeto = c.idPreProjeto)', null, $this->getSchema('agentes'))
                            ->join(['d' => 'tbvinculo'], '(c.idVinculo = d.idVinculo)', null, $this->getSchema('agentes'))
                            ->join(['e' => 'sgcacesso'], '(d.idUsuarioResponsavel = e.idUsuario)', null, $this->getSchema('controledeacesso'))
                            ->where('c.siVinculoProposta = 2')
                            ->where('e.IdUsuario = ?', $idUsuarioLogado)
                            ->where('a.idPreProjeto = ?', $idPreProjeto)
                        ;

                        $idAgente = $db->fetchRow($sql);

                        if (!empty($idAgente)) {
                            $permissao = 1;
                        }
                    }
            }
            break;
        case 2 :

            $sql = $db->select()
                ->from(['a' => 'projetos'], '(a.anoprojeto+a.sequencial) as pronac', $this->getSchema('sac'))
                ->join(['b' => 'agentes'], '(a.cgccpf = b.cnpjcpf)', null , $this->getSchema('agentes'))
                ->join(['c' => 'sgcacesso'], '(a.cgccpf = c.cpf)', null, $this->getSchema('controledeacesso'))
                ->where('c.idusuario = ?', $idUsuarioLogado)
                ->where('a.idpronac = ?', $idPreProjeto)
                ;
            $pronac = $db->fetchRow($sql);

            if (!empty($pronac)){
                $permissao = 1;
            } else {

                $sql = $db->select()
                    ->from(['a' => 'projetos'], '(a.anoprojeto+a.sequencial) as pronac', $this->getSchema('sac'))
                    ->join(['b' => 'agentes'], '(a.cgccpf = b.cnpjcpf)', null , $this->getSchema('agentes'))
                    ->join(['c' => 'tbprocuradorprojeto'], '(a.idpronac = c.idpronac)', null , $this->getSchema('agentes'))
                    ->join(['d' => 'tbprocuracao'], '(c.idprocuracao = d.idprocuracao)', null , $this->getSchema('agentes'))
                    ->join(['f' => 'agentes'], '(d.idagente = f.idagente)', null , $this->getSchema('agentes'))
                    ->join(['e' => 'sgcacesso'], '(f.cnpjcpf = e.cpf)', null , $this->getSchema('controledeacesso'))
                    ->where('c.siestado = 2')
                    ->where('e.idusuario = ?', $idUsuarioLogado)
                    ->where('a.idpronac = ?', $idPreProjeto)
                    ;

                $pronac = $db->fetchRow($sql);
                if (!empty($pronac)){
                    $permissao =1;
                } else {
                    $sql = $db->select()
                        ->from(['a' => 'projetos'], '(a.anoprojeto+a.sequencial) as pronac', $this->getSchema('sac'))
                        ->join(['b' => 'agentes'], '(a.cgccpf = b.cnpjcpf)', null , $this->getSchema('agentes'))
                        ->join(['c' => 'vinculacao'], '(b.idagente = c.idvinculoprincipal)', null , $this->getSchema('agentes'))
                        ->join(['d' => 'agentes'], '(c.idagente = d.idagente)', null , $this->getSchema('agentes'))
                        ->join(['e' => 'sgcacesso'], '(d.cnpjcpf = e.cpf)', null , $this->getSchema('controledeacesso'))
                        ->where('e.idusuario = ?', $idUsuarioLogado)
                        ->where('a.idpronac = ?', $idPreProjeto)
                        ;

                    $pronac = $db->fetchRow($sql);

                    if (!empty($pronac)){
                        $permissao = 1;
                    } else {
                        $permissao = 0;
                    }
                }
            }
            break;
        case 3 :
            $sql = $db->select()
                ->from(['vwUsuariosOrgaosGrupos'], 'usu_codigo', $this->getSchema('tabelas'))
                ->where('sis_codigo = 21')
                ->where('usu_codigo = ?', $idUsuarioLogado)
                ->where('uog_status = 1')
                ->limit(1)
                ;

            $codigo = $db->fetchRow($sql);

            if (!empty($codigo)) {
                $permissao = 1;
            } else {
                $permissao = 0;
            }
            break;
        }
        $perm = new stdClass();
        $perm->Permissao = ($permissao == 0) ? 0 : 1;
        return  $perm;
    }

    public function verificarPermissaoAdministrativo($idUsuarioLogado) {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao(0,'',$idUsuarioLogado,'') as Permissao");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }

}

