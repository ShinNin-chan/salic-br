<?php if($this->menuLateral != 'false'){ ?>
    <div id="confirma"></div>
    <script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
    <div id="menu">
        <div id="container">
            <script type="text/javascript">
                function layout_fluido()
                {
                    var janela = $(window).width();

                    var fluidNavGlobal = janela - 245;
                    var fluidConteudo = janela - 253;
                    var fluidTitulo = janela - 252;
                    var fluidRodape = janela - 19;

                    $("#navglobal").css("width",fluidNavGlobal);
                    $("#conteudo").css("width",fluidConteudo);
                    $("#titulo").css("width",fluidTitulo);
                    $("#rodapeConteudo").css("width",fluidConteudo);
                    $("#rodape").css("width",fluidRodape);

                    $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
                }
            </script>

            <?php

                $get = Zend_Registry::get("get");
                //define id do PreProjeto que sera passado as outras implementacoes
                $codPronac = "?idPronac=";
                if(isset($this->idPronac)){
                    $codPronac .= $this->idPronac;
                }elseif(isset($get->idPronac)){
                    $codPronac .= $get->idPronac;
                }

                $codPreProjeto = "&idPreProjeto=";
                if(isset($this->idPreProjeto)){
                    $codPreProjeto .= $this->idPreProjeto;
                }elseif(isset($get->idPreProjeto)){
                    $codPreProjeto .= $get->idPreProjeto;
                }
            ?>

            <div id="menuContexto">
                <div class="top"></div>
                <div id="qm0" class="qmmc">
                        <?php var_dump( $this->grupoativo, $this->parecerista) ?>
                    <!-- Se for Parecerista, Coordenador de PRONAC ou Gestor Salic -->
                    <?php if(($this->grupoativo == "1111") or ($this->grupoativo == "144") or ($this->grupoativo == "122") or
                            ($this->grupoativo == "120") or ($this->grupoativo == "97")){ ?>
                        <?php if(($this->grupoativo != "1111")){ ?>
                            <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'buscaragente'),'',true);?>" title="Ir para Localizar Agente">Pesquisar</a>
                        <?php } ?>

                        <?php if(($this->grupoativo != "93")){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'incluiragente'),'',true);?>" title="Ir para Incluir Agente" >Incluir</a>
                        <?php } ?>
                    <?php } ?>

                    <?php if(($this->grupoativo == "137") or ($this->grupoativo == "93")){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'painelcredenciamento'),'',true);?>" title="Ir para Incluir Agente" >Pesquisar Parecerista</a>

                        <?php if(($this->grupoativo == "137")){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'incluiragente'),'',true);?>" title="Ir para Incluir Agente" >Incluir Parecerista</a>
                        <?php } ?>
                    <?php } ?>

                    <div class="sumir">
                        <!-- Se for Proponente ou Gestor Salic -->
                        <?php if(($this->grupoativo == "144") or ($this->grupoativo == "97")){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'vincularresponsavel', 'action' => 'index'),'',true);?>" title="Ir para Vincular Respons&aacute;vel">Vincular Respons&aacute;vel </a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'vincularresponsavel', 'action' => 'desvincularresponsavel'),'',true);?>" title="Ir para Desvincular Respons&aacute;vel" >Desvincular Respons&aacute;vel</a>
                        <?php } ?>
                    </div>

                    <?php if(isset($this->id)){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'agentes', 'id' => $this->id),'',true);?>" title="Ir para Dados do Agente">Dados Principais</a>

                        <?php if(($this->grupoativo != "93")){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'enderecos', 'id' => $this->id),'',true);?>" title="Ir para Endere&ccedil;os">Endere&ccedil;os</a>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'telefones', 'id' => $this->id),'',true);?>" title="Ir para Telefones">Telefones</a>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'emails', 'id' => $this->id),'',true);?>" title="Ir para Emails">Emails</a>
                        <?php } ?>

                        <!-- Se for Parecerista, Coordenador de PRONAC ou Gestor Salic -->
                        <?php if(($this->grupoativo == "94") || ($this->grupoativo == "137") || ($this->grupoativo == "97") || ($this->grupoativo == "93")){ ?>
                            <?php if($this->parecerista == "sim"){ ?>
                                <?php if(($this->grupoativo != "93")){ ?>
                                <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'escolaridade', 'id' => $this->id),'',true);?>" title="Ir para Forma&ccedil;&atilde;o">Escolaridade/Cursos</a>
                                <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'formacao', 'id' => $this->id),'',true);?>" title="Ir para Forma&ccedil;&atilde;o">Informa&ccedil;&otilde;es Profissionais</a>
                                <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'ferias', 'id' => $this->id),'',true);?>" title="Ir para F&eacute;rias">F&eacute;rias</a>
                                <?php } ?>

                                <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'atestados', 'id' => $this->id),'',true);?>" title="Ir para Atestados">Atestados</a>
                            <?php } ?>
                        <?php } ?>

                        <!-- Se for Parecerista, Coordenador de PRONAC ou Gestor Salic -->
                        <?php if( ($this->grupoativo == "93") || ($this->grupoativo == "97")){ ?>
                            <?php if($this->parecerista == "sim"){ ?>
                            <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'ferias', 'id' => $this->id),'',true);?>" title="Ir para F&eacute;rias">Gerenciar F&eacute;rias</a>
                            <?php } ?>
                        <?php } ?>

                        <!-- Se for CNPJ -->
                        <?php if(($this->dados[0]->tipopessoa == 1) or ($this->Instituicao == 'sim')){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'dirigentes', 'id' => $this->id),'',true);?>" title="Ir para Dirigentes">Dirigentes <span style="float: right;"> (<?php echo $this->qtdDirigentes;?>) </span></a>
                        <?php } ?>

                        <!-- Se for Coordenador de PRONAC ou Gestor Salic -->
                        <?php if(($this->grupoativo == "137") && ($this->parecerista == 'sim')){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'credenciamento', 'id' => $this->id),'',true);?>" title="Ir para Credenciamento">Credenciar / Descredenciar</a>
                        <?php } ?>

                        <?php if(($this->grupoativo != "94") && ($this->grupoativo != "118")&& ($this->grupoativo != "93" && !$this->ehProponente)){ ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'alterarvisao', 'id' => $this->id),'',true);?>" title="Ir para Vis&Otilde;es">Vis&otilde;es</a>
                        <?php } ?>


                        <?php if(($this->dados[0]->tipopessoa == 1)){ ?>
                            <!-- Se for CNPJ -->
                            <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'natureza', 'id' => $this->id),'',true);?>" title="Ir para Natureza">Natureza</a>
                        <?php } else { ?>
                            <!-- Se for CPF -->
                            <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'info-adicionais', 'id' => $this->id),'',true);?>" title="Ir para Informa&ccedil;&Otilde;es Adicionais">Informa&ccedil;&otilde;es Adicionais</a>
                        <?php } ?>

                        <?php if(($this->grupoativo == "120") || ($this->grupoativo == "97")){ ?>
                            <a class="no_seta" href="<?php echo $this->url(array('module'=>'agente', 'controller' => 'agentes', 'action' => 'area-cultural', 'id' => $this->id),'',true);?>" title="Ir para &Aacute;rea Cultural">&Aacute;rea Cultural</a>
                        <?php } ?>

                    <?php } ?>


                </div>
                <div class="bottom"></div>
                <div id="space_menu"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.sanfona > a').click(function(){
                $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                    $(valor).hide('fast');
                });
                $(this).next().toggle('fast');
            });
        });
    </script>

<!-- ========== FIM MENU ========== -->
<?php } ?>
