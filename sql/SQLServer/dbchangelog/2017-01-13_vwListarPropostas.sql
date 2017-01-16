-- ===========================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criação: 14/01/2017
-- Descrição: Listar propostas culturais
-- ===========================================================================================

IF OBJECT_ID ('vwListarPropostas', 'V') IS NOT NULL
DROP VIEW vwListarPropostas ;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW dbo.vwListarPropostas
AS
SELECT a.idPreProjeto AS idProjeto,a.NomeProjeto AS NomeProposta,a.stPlanoAnual,d.CNPJCPF,a.idAgente,c.idTecnico AS idUsuario,
	   SAC.dbo.fnNomeTecnicoMinc(c.idTecnico) AS Tecnico,
	   CASE
	     WHEN a.AreaAbrangencia = 0 THEN 251
	     WHEN a.AreaAbrangencia = 1 THEN 160
		END AS idSecretaria,
	   CONVERT(CHAR(20),c.DtAvaliacao,120) AS DtAdmissibilidade,
	   DATEDIFF(d,c.DtAvaliacao,GETDATE()) as dias,c.idAvaliacaoProposta,b.idMovimentacao,a.stTipoDemanda
FROM SAC.dbo.PreProjeto                AS a
INNER JOIN SAC.dbo.tbMovimentacao      AS b ON (a.idPreProjeto = b.idProjeto) 
INNER JOIN SAC.dbo.tbAvaliacaoProposta AS c ON (a.idPreProjeto = c.idProjeto)
INNER JOIN AGENTES.dbo.Agentes         AS d ON (a.idAgente = d.idAgente)
WHERE a.stEstado = 1
	  AND a.stTipoDemanda = 'NA'
	  AND b.Movimentacao = 127	
	  AND b.stEstado = 0
	  AND c.ConformidadeOK = 1 
	  AND c.stEstado = 0
	  AND NOT EXISTS(SELECT *	FROM SAC.dbo.Projetos AS u WHERE a.idPreProjeto = idProjeto)
GO 

GRANT  SELECT ON dbo.vwListarPropostas  TO usuarios_internet
GO
