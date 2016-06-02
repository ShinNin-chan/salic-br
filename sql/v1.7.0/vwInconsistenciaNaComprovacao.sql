-- =========================================================================================
-- Autor: R�mulo Menh� Barbosa
-- Data de Cria��o: 01/05/2016
-- Descri��o: Inconsist�ncia na comprova��o x informa��es banc�rias
-- =========================================================================================

IF OBJECT_ID ('vwInconsistenciaNaComprovacao', 'V') IS NOT NULL
DROP VIEW vwInconsistenciaNaComprovacao ;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW dbo.vwInconsistenciaNaComprovacao
AS
SELECT QtdeA as idPronac,ChaveA as Pronac,CampoA as NomeProjeto,CampoB as ItemOrcamentario,
       ChaveB as CNPJCPF,CampoC as Fornecedor,QtdeB as idComprovantePagamento,ChaveC as nrDocumentoDePagamento,
	   DataA as dtPagamento,ValorA as vlPagamento,ValorB as vlComprovado,CampoD as dsLancamento,DataB as dtLancamento,
	   ValorC as vlDebitado,ValorD as vlDiferenca
  FROM sac.dbo.Intranet
  WHERE Tipo = 35
        AND ValorD <> 0
GO 

GRANT  SELECT ON dbo.vwInconsistenciaNaComprovacao  TO usuarios_internet
GO
