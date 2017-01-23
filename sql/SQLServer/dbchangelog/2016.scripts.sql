/** SCRIPTS CRIADOS EM 2016 **/

/* Adição da coluna 'id_login_cidadao' no banco de dados  */
ALTER TABLE ControleDeAcesso.dbo.SGCacesso ADD id_login_cidadao int;

/* INCLUSÃO DA SITUAÇÃO PARA TRABALHAR COM O ENQUADRAMENTO */
INSERT INTO SAC.dbo.Situacao VALUES('B01','Proposta transformada em projeto','C','1')

-- Acrescenta "Recolhimento" na etapa de Custos de Produto
UPDATE SAC.dbo.tbPlanilhaEtapa
SET tpCusto='P'
WHERE idPlanilhaEtapa=5;

-- Remove Not Null na coluna idPolicaoDaLogo
ALTER TABLE sac.dbo.PlanoDistribuicaoProduto ALTER COLUMN idPosicaoDaLogo INT;



-- cria itens para a lista de execução imediata
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta normal', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proteção do patrimônio material', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proteção do patrimônio imaterial', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proteção  de acervos', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Planos anuais', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta museológica', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta de manutenção de corpos estáveis', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta de construção de equipamentos culturais', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta aprovado em editais', 1);
INSERT INTO SAC.dbo.Verificacao (idTipo, Descricao, stEstado) VALUES (23, 'Proposta  com contratos de patrocínios', 1);

-- Remove Not Null na coluna stPlanoAnual
ALTER TABLE sac.dbo.PreProjeto ALTER COLUMN stPlanoAnual BIT;

-- Cria novo documento
INSERT INTO SAC.dbo.DocumentosExigidos (Descricao,Area,Opcao,stEstado,stUpload)
VALUES ('RESULTADO DA SELEÇÃO PÚBLICA','0',2,1,1);

-- Cria novo campo para prorrogacao automática
ALTER TABLE sac.dbo.PreProjeto ADD tpProrrogacao BIT DEFAULT 1 NULL;

-- cria mais duas etapas para planilha orcamentaria
INSERT INTO SAC.dbo.tbPlanilhaEtapa (Descricao, tpCusto, stEstado, tpGrupo) VALUES ('Pós-Produção', 'P', 1, 'A');
INSERT INTO SAC.dbo.tbPlanilhaEtapa (Descricao, tpCusto, stEstado, tpGrupo) VALUES ('Custos Vinculados', 'A', 1, 'D');

-- cria novos itens
INSERT INTO SAC.dbo.tbPlanilhaItens
(idPlanilhaItens, Descricao, idUsuario)
VALUES(8197, 'Custos de Administração', 236);
INSERT INTO SAC.dbo.tbPlanilhaItens
(idPlanilhaItens, Descricao, idUsuario)
VALUES(8198, 'Custos de Divulgação', 236);
INSERT INTO SAC.dbo.tbPlanilhaItens
(idPlanilhaItens, Descricao, idUsuario)
VALUES(8199, 'Controle e Auditoria', 236);

-- ajusta o cárdapio da etapa de custos vinculados
insert into tbItensPlanilhaProduto
(idProduto,idPlanilhaEtapa,idPlanilhaItens,idUsuario)
select 0,8,8197,236

insert into tbItensPlanilhaProduto
(idProduto,idPlanilhaEtapa,idPlanilhaItens,idUsuario)
select 0,8,8198,236

insert into tbItensPlanilhaProduto
(idProduto,idPlanilhaEtapa,idPlanilhaItens,idUsuario)
select 0,8,8199,236

insert into tbItensPlanilhaProduto
(idProduto,idPlanilhaEtapa,idPlanilhaItens,idUsuario)
select 0,8,5249,236

-- amarrando os valores de pos-producao etapa 7
-- @todo ATENCAO, esse valores não irão para produção, o Rômulo fará o insert correto
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (1, 7, 4001, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (1, 7, 4002, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (1, 7, 4003, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (1, 7, 4005, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5000, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5001, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5002, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5003, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5004, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (19, 7, 5005, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6000, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6001, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6002, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6003, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6004, 236);
INSERT INTO SAC.dbo.tbItensPlanilhaProduto (idProduto, idPlanilhaEtapa, idPlanilhaItens, idUsuario) VALUES (51, 7, 6005, 236);

use sac;
DROP PROCEDURE paTransformarPropostaEmProjetoNovoIN;

CREATE PROCEDURE [dbo].[paTransformarPropostaEmProjetoNovoIN]
                @idProposta int,
                @CNPJCPF    varchar(14),
                @idOrgao    int,
                @idUsuario  int,
                @Processo   varchar(17)
AS

-- ==========================================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criação: 28/11/2012
-- Descrição: Transformar as propostas do mecanismo de incentivo fiscal federal em projetos culturais.
-- Data de Alteração: 23/01/2014
-- Motivo: Inclusão CLÁUSULA TOP 1 NO SELECT para incluir registro na tabela Interessado.
-- Data de Alteração: 20/04/2016
-- Motivo: Inclusão do parâmetro @Processo para integração com o sistema SEI.
--         Exclusão da chamada da procedure paGeraProcessoSalic. Autor: Yuri Marques
-- ==========================================================================================================

SET NOCOUNT ON
DECLARE @Erro         int
DECLARE @Rows         int
DECLARE @Sequencial   int
DECLARE @AnoProjeto   char(2)
DECLARE @NrProjeto    varchar(5)
DECLARE @Ano          int
DECLARE @idPronac     int
--DECLARE @Processo     varchar(17)
DECLARE    @NomeProposta VARCHAR(200)
DECLARE    @Destinatario VARCHAR(100)
DECLARE @Email          VARCHAR(50)
DECLARE    @Mensagem      VARCHAR(MAX)
DECLARE @tableHTML    NVARCHAR(MAX)
DECLARE    @Retorno      INT
DECLARE @Flag         int
DECLARE @idTextoEmail int
DECLARE @idVinculada  int

BEGIN TRAN
SET @Flag = 0

-- ==========================================================================================================
-- TABELA TEMPORÁRIA
-- ==========================================================================================================

CREATE TABLE #Verificacao
       (
        idContador int IDENTITY(1,1),
        Descricao  varchar (250)
       )

-- ==========================================================================================================
-- INSERIR INFORMAÇÕES NA TABELA INTERESSADO
-- ==========================================================================================================
IF NOT EXISTS(SELECT * FROM Interessado WHERE CgcCpf = @CNPJCPF)
   BEGIN
     INSERT INTO Interessado
                (CgcCpf,TipoPessoa,Nome,Responsavel,Endereco,Cidade,UF,CEP,Natureza,Esfera,Administracao,Utilidade)
          SELECT TOP 1 p.CNPJCPF,
                 case
                   when len(p.CNPJCPF)=11
                     then  '1'
                     else  '2'
                 end as TipoPessoa,
                 Nome,dbo.fnNomeResponsavel(p.Usuario),p.Logradouro + ' - ' + p.Bairro,u.Municipio,u.UF,p.CEP,
                 case
                   when Direito = 1
                     then '1'
                   when Direito = 2 or Direito = 35
                     then '2'
                  end as Direito,
                 case
                   when Esfera = 3
                     then '1'
                   when Esfera = 4
                     then '2'
                   when Esfera = 5
                     then '3'
                  end as Esfera,
                 case
                   when Administracao = 11
                     then '1'
                   when Administracao = 12
                     then '2'
                  end as Administracao,
                 case
                   when Direito = 2
                     then '1'
          when Direito = 35
                     then '2'
                  end as Utilidade
                FROM vCadastrarProponente p
                 INNER JOIN Agentes.dbo.Agentes a on (p.idAgente = a.idAgente)
                 INNER JOIN Agentes.dbo.EnderecoNacional e on (p.idAgente = e.idAgente and e.Status = 1)
                 INNER JOIN Agentes.dbo.vUFMunicipio u on (e.UF = u.idUF and e.Cidade = u.idMunicipio )
                 LEFT JOIN  vwNatureza n on (p.idAgente =n.idAgente)
  WHERE p.CNPJCPF=@CNPJCPF and Correspondencia = 1
   END
ELSE
   BEGIN
     UPDATE Interessado
        SET Endereco = e.Logradouro + ' - ' + e.Bairro,
            Cidade   = u.M
 unicipio,
            UF       = u.UF,
            CEP      = e.CEP,
            Natureza  = case
                        when n.Direito = 1
                          then '1'
                        when n.Direito = 2 or n.Direito = 35
                          then '2'
                       end,
            Esfera   = case
                        when n.Esfera = 3
                          then '1'
                        when n.Esfera = 4
                          then '2'
                        when n.Esfera = 5
                         then '3'
                       end,
            Administracao = case
                             when n.Administracao = 11
                              then '1'
                             when n.Administracao = 12
                              then '2'
                            end,
            Utilidade = case
                         when n.Direito = 2
                           then '1'
                         when n.Direito = 35
                           then '2'
                        end
            FROM Interessado i
                 INNER JOIN Agentes.dbo.Agentes a on (i.CgcCpf = a.CNPJCPF)
                 INNER JOIN Agentes.dbo.EnderecoNacional e on (a.idAgente = e.idAgente and e.Status = 1)
                 INNER JOIN Agentes.dbo.vUFMunicipio u on (e.UF = u.idUF and e.Cidade = u.idMunicipio )
                 LEFT JOIN  vwNatureza n on (a.idAgente =n.idAgente)
                 WHERE i.CGCCPF=@CNPJCPF and e.Status = 1
   END

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('1.Erro ao tentar incluir ou alterar %d registros na tabela Interessado.')
     SET @Flag = 1
   END
IF @Rows < 1
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('2.Não há registro para incluir / alterar registro na tabela Interessado.')
     SET @Flag = 1
   END
IF @Rows <> 1
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('3.Não é possível incluir / alterar mais de um registro na tabela Interessado.')
     SET @Flag = 1
   END

-- ==========================================================================================================
-- GERAR PRONAC
-- ==========================================================================================================
SET @Ano = year(getdate())

UPDATE SequencialProjetos
   SET @Sequencial = Sequencial = Sequencial + 1
   WHERE Ano = YEAR(GETDATE())

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('4.Erro ao tentar atualizar registros na tabela SequencialProjetos.')
     SET @Flag = 1
   END

IF @Rows = 0
   BEGIN
     INSERT INTO SequencialProjetos (Ano,Sequencial)
                 VALUES (@Ano ,1)
     SET @Sequencial = 1
   END

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('5.Não é possível incluir ou alterar mais de um registro na tabela SequencialProjetos.')
     SET @Flag = 1
   END

SET @AnoProjeto = substring(convert(varchar(10),getdate(),111),3,2)

IF @Sequencial > 9999
   BEGIN
     SET @NrProjeto = SUBSTRING(RTRIM(LTRIM(STR(@Sequencial + 100000))), 2, 5)
   END
ELSE
   BEGIN
     SET @NrProjeto = SUBSTRING(RTRIM(LTRIM(STR(@Sequencial + 100000))), 3, 4)
   END
-- ==========================================================================================================
-- GERAR O Nº DE PROCESSO NO BANCO SAD
-- ==========================================================================================================
--EXEC dbo.paGeraProcessoSalic @idUsuario,@idOrgao,@Processo output
-- ==========================================================================================================
-- GRAVAR DADOS NA TABELA PROJETOS
-- ==========================================================================================================
INSERT INTO Projetos
              (AnoProjeto,Sequencial,UFProjeto,Area,Segmento,Mecanismo,NomeProjeto,CgcCpf,Situacao,DtProtocolo,DtAnalise,
               OrgaoOrigem,Orgao,DtSituacao,Provide
 nciaTomada,ResumoProjeto,DtInicioExecucao,DtFimExecucao,SolicitadoReal,
               idProjeto,Processo,Logon)
    SELECT TOP 1 @AnoProjeto,@NrProjeto,u.Sigla,dbo.fnSelecionarArea(idPreProjeto),dbo.fnSelecionarSegmento(idPreProjeto),
               Mecanismo,NomeProjeto,a.CNPJCPF,'B11',getdate(),getdate(),@idOrgao,@idOrgao,getdate(),
               'Proposta transformada em projeto cultural',ResumoDoProjeto,DtInicioDeExecucao,DtFinalDeExecucao,
               dbo.fnSolicitadoNaProposta(idPreProjeto),idPreProjeto,@Processo,@idUsuario
               FROM PreProjeto p
               INNER JOIN Agentes.dbo.Agentes a on (p.idAgente = a.idAgente)
               INNER JOIN Agentes.dbo.EnderecoNacional e on (a.idAgente = e.idAgente and e.Status = 1)
               INNER JOIN Agentes.dbo.UF u on (e.UF = u.idUF)
               WHERE idPreProjeto  = @idProposta and NOT EXISTS(SELECT TOP 1 * FROM Projetos x WHERE p.idPreProjeto = x.idProjeto)

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('6.Erro ao tentar incluir %d registros na tabela Projetos.')
     SET @Flag = 1
   END

IF @Rows <> 1
   BEGIN
     INSERT INTO #Verificacao
        VALUES ('7.Não é possível incluir mais de %d registros na tabela Projeto.')
     SET @Flag = 1
   END

-- ==========================================================================================================
-- PEGAR O IDPRONAC
-- ==========================================================================================================
-- SELECT @idPronac = idPronac FROM Projetos WHERE AnoProjeto = @AnoProjeto and Sequencial = @NrProjeto

-- ==========================================================================================================
-- DISTRIBUIR OS PROJETOS PARA A VINCULADA DE ACORDO COM O SEGMENTO DO PRODUTO PRINCICPAL
-- ==========================================================================================================
-- SELECT @idVinculada = idOrgao FROM PlanoDistribuicaoProduto t
--                              INNER JOIN vSegmento s on (t.Segmento = s.Codigo)
--                              WHERE t.stPrincipal = 1 and idProjeto = @idProposta

-- INSERT INTO tbDistribuirParecer
--             (idPronac,idProduto,TipoAnalise,idOrgao,DtEnvio, stPrincipal)
--      SELECT @idPronac,idProduto,3,@idVinculada,getdate(), stPrincipal FROM PlanoDistribuicaoProduto WHERE idProjeto = @idProposta

UPDATE SAC.dbo.Projetos set Situacao = 'B01' WHERE IdPRONAC = 1

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('8.Erro ao tentar incluir %d registros na tabela tbDistribuirParecer.')
     SET @Flag = 1
   END

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('10.Erro ao tentar incluir %d registros na tabela tbDistribuirParecer.')
     SET @Flag = 1
   END
-- ==========================================================================================================
-- CARREGAR A PLANILHA DO PARECERISTA
-- ==========================================================================================================
INSERT INTO tbPlanilhaProjeto
            (idPlanilhaProposta,idPronac,idProduto,idEtapa,idPlanilhaItem,Descricao,idUnidade,Quantidade,Ocorrencia,ValorUnitario,QtdeDias,
             TipoDespesa,TipoPessoa,Contrapartida,FonteRecurso,UFDespesa,    MunicipioDespesa,idUsuario)
           SELECT idPlanilhaProposta,@idPronac,idProduto,idEtapa,idPlanilhaItem,Descricao,Unidade,
                Quantidade,Ocorrencia,ValorUnitario,QtdeDias,TipoDespesa,TipoPessoa,Contrapartida,FonteRecurso,UFDespesa,
                MunicipioDespesa,0
                FROM tbPlanilhaProposta
                WHERE idProjeto = @idProposta

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('11.Erro ao tentar incluir %d registros na tabela tbPlanilhaProjeto.')
     SET @Flag = 1
   END

-- ==========================================================================================================
--
 CARREGAR A TABELA DE ANÁLISE DE CONTEÚDO
-- ==========================================================================================================
INSERT INTO tbAnaliseDeConteudo
           (idPronac,idProduto)
       SELECT @idPronac,idProduto FROM tbPlanilhaProposta
                                  WHERE idProjeto = @idProposta AND idProduto <> 0
                                  GROUP BY idProduto

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('12.Erro ao tentar incluir %d registros na tabela tbPlanilhaProjeto.')
     SET @Flag = 1
   END

-- ==========================================================================================================
-- INSERIR INFORMAÇÕES NA TABELA CONTABANCARIA
-- ==========================================================================================================
INSERT INTO ContaBancaria
           (AnoProjeto,Sequencial,Mecanismo,Banco,Agencia,Logon)
       SELECT @AnoProjeto,@NrProjeto,Mecanismo,'001',AgenciaBancaria,@idUsuario FROM PreProjeto WHERE idPreProjeto = @idProposta

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('13.Erro ao tentar incluir %d registros na tabela ContaBancaria.')
     SET @Flag = 1
   END

IF @Rows <> 1
   BEGIN
     INSERT INTO #Verificacao
            VALUES ('14.Não é possível incluir mais de %d registros na ContaBancaria.')
     SET @Flag = 1
   END

--================================================================================================================
-- CARREGAR INFORMAÇÕES PARA ENVIAR EMAIL
--================================================================================================================
IF NOT EXISTS (SELECT TOP 1 * FROM tbHistoricoEmail WHERE idPronac = @idPronac and idTextoEmail = 12 and
                                               (CONVERT(char(10),(DtEmail),111) = CONVERT(char(10),getdate(),111)))
   BEGIN
     --================================================================================================================
     -- GRAVAR EMAIL ENVIADO NA TABELA TBHISTORICOEMAIL
     --================================================================================================================
     SET @idTextoEmail = 12

     INSERT INTO tbHistoricoEmail
                (idPronac,idTextoemail,DtEmail,stEstado,idUsuario)
         VALUES (@idPronac,@idTextoEmail,getdate(),1,@idUsuario)

     SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

     IF @Erro <> 0
        BEGIN
          INSERT INTO #Verificacao
            VALUES ('15.Erro ao inserir registros %d na tabela tbHistoricoEmail.')
          SET @Flag = 1
        END

     IF @Rows <> 1
        BEGIN
          INSERT INTO #Verificacao
            VALUES ('16.Não é permitido inserir %d registros ao mesmo tempo na tabela tbHistoricoEmail.')
          SET @Flag = 1
       END

     SELECT @Mensagem = dsTexto FROM tbTextoEmail WHERE idTextoEmail = @idTextoEmail

     SELECT @NomeProposta=NomeProjeto,@Destinatario=Nome
            FROM Projetos p
            INNER JOIN Interessado i on (p.CgcCpf = i.CgcCpf)
            WHERE idPronac = @idPronac
     --================================================================================================================
     -- ENVIAR EMAIL AO PROPONENTE
     --================================================================================================================
     DECLARE MyCursor CURSOR FOR
     SELECT Descricao FROM agentes.dbo.Internet i
            INNER JOIN PreProjeto p on (i.idAgente = p.idAgente)
            WHERE p.idPreProjeto = @idProposta  and i.idAgente=p.idAgente and Status = 1

     SET @Mensagem = '<b>Projeto: ' + CONVERT(VARCHAR(10),@AnoProjeto+@NrProjeto) + ' - ' + @NomeProposta + '<br> Proponente: ' +
          @Destinatario + '<br> </b>' + @Mensagem

     OPEN MyCursor

     W
 HILE @@FETCH_STATUS = @@FETCH_STATUS
       BEGIN
         FETCH NEXT FROM MyCursor INTO @Email
         IF (@@FETCH_STATUS = -2)          CONTINUE
         IF (@@FETCH_STATUS = -1)          BREAK

         EXEC msdb.dbo.sp_send_dbmail    @profile_name = 'PerfilGrupoPRONAC',
                                        @recipients = @Email,
                                        @body = @Mensagem,
                                        @body_format = 'HTML',
                                           @subject = 'Projeto Cultural',
                                        @exclude_query_output =1;

       END
     CLOSE MyCursor
     DEALLOCATE MyCursor
   END
--================================================================================================================
--
--================================================================================================================
IF @Flag = 1
   BEGIN
    SELECT 1 as Flag,Descricao as Mensagem FROM #Verificacao
    ROLLBACK TRAN
   END
ELSE
  BEGIN
    COMMIT TRAN
    SELECT 0 as Flag,@AnoProjeto+@NrProjeto as Mensagem
  END

/* INCLUSÃO DA SITUAÇÃO PARA TRABALHAR COM O ENQUADRAMENTO */
INSERT INTO SAC.dbo.Situacao VALUES('B02','Projeto enquadrado','C','1')

/* INCLUSÃO DA SITUAÇÃO PARA TRABALHAR COM O ENQUADRAMENTO */
INSERT INTO SAC.dbo.Situacao VALUES('B03','Projeto enquadrado com recusso','C','1');

/* ADIÇÃO DA COLUNA TP_ENQUADRAMENTO - onde os valores são : 1 para artigo 26 e 2 para artigo 18 */
ALTER TABLE sac.dbo.Segmento ADD tp_enquadramento CHAR(1) NULL;