<div class="col s2">
    <ul class="collection with-header">
        <li class="black-text collection-header"><h6>Consultar</h6></li>
        <li class="collection-item"><a href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'listar-propostas-analise-visual-tecnico')); ?>' title="Ir para Visual por T&eacute;cnico">Visual por T&eacute;cnico</a></li>
        <li class="collection-item"><a href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'historico-analise-visual')); ?>' title="Ir para Hist&oacute;rico da An&aacute;lise Visual">Hist&oacute;rico da An&aacute;lise Visual</a></li>
        <li class="collection-item"><a href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'listar-propostas-analise-final')); ?>' title="Ir para Proposta em An&aacute;lise Final">Proposta em An&aacute;lise Final</a></li>
        <li class="collection-item"><a href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'painel-projetos-distribuidos')); ?>' title="Ir para Projetos Distribu&iacute;dos por &Oacute;rg&atilde;os">Projetos Distribu&iacute;dos por &Oacute;rg&atilde;os</a></li>
        <li class="black-text collection-header"><h5>An&aacute;lise</h5></li>
        <li class="collection-item"><a class="no_seta" href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'gerenciarparecertecnico', 'action' => 'imprimiretiqueta')); ?>' title="Imprimir Etiqueta e Projeto">Imprimir Etiqueta e Projeto</a></li>
        <li class="collection-item"><a class="no_seta" href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'alterarunianalisepropostaconsulta')); ?>' title="Ir para Alterar Unidade da an&aacute;lise da Proposta">Alterar Uni. da an&aacute;lise da Proposta</a></li>
    </ul>
</div>
