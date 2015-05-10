<?php
session_start();

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

include_once("./modelos/cabecalhoIndex.html");
?>
<main id="dados_cadastro">
    <div>
        <h2>Cadastro de novo integrante</h2>
        Entre agora com seus dados e faça parte dessa comunidade
        <form method="post" action="./cadastrar.php" role="form" enctype="multipart/form-data">
            <dl>
                <dt>Login</dt>
                <dd><input type="text" name="login" class="form-input" required autofocus/></dd>
                <dt>Senha</dt>
                <dd><input type="password" name="senha" class="form-input" required/></dd>
                <dt>Senha novamente</dt>
                <dd><input type="password" name="senhanovamente" class="form-input" required/></dd>
                <dt>Nome completo</dt>
                <dd><input type="text" name="nomecompleto" class="form-input" required/></dd>
                <dt>E-mail</dt>
                <dd><input type="email" name="email" class="form-input" required/></dd>
                <dt>Estado</dt>
                <dd>
                    <select name="idEstado" id="idEstado" onchange="buscar_cidades();">
                        <?php
                        $resultadoEstado = $banco->obterEstados();
                        // Iniciar com Minas Gerais
                        if (count($resultadoEstado) > 0) {
                            foreach ($resultadoEstado as $estado) {
                                if (strcmp($estado['nomeEstado'], "Minas Gerais") == 0) {
                                    $idEstado = $estado['idEstado'];
                                    echo "<option value =\"" . $estado['idEstado'] . "\" selected>" . $estado['nomeEstado'] . "</option>";
                                } else {
                                    echo "<option value =\"" . $estado['idEstado'] . "\">" . $estado['nomeEstado'] . "</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </dd>

                <dt>Cidade</dt>
                <dd>
                    <div id="load_cidades">
                        <select name="idCidade">
                            <?php
                            $resultadoCidades = $banco->obterCidades($idEstado);
                            // Iniciar com Belo Horizonte
                            if (count($resultadoCidades) > 0) {
                                foreach ($resultadoCidades as $cidade) {
                                    if (strcmp($cidade['nomeCidade'], "Belo Horizonte") == 0) {
                                        echo "<option value =\"" . $cidade['idCidade'] . "\" selected>" . $cidade['nomeCidade'] . "</option>";
                                    } else {
                                        echo "<option value =\"" . $cidade['idCidade'] . "\">" . $cidade['nomeCidade'] . "</option>";
                                    }
                                }
                            }
                            ?>   
                        </select>
                    </div>

                </dd>

                <dt>Descrição</dt>
                <dd>
                    <textarea name="descricao" class="form-input" rows="3" required></textarea>
                </dd>
                <dt>Foto</dt>
                <dd>
                    <input type="file" name="arquivo" id="arquivo" placeholder="Escolha um arquivo" required/>
                </dd>
            </dl>
            <div class="btn_cadastro">
                <button type="submit">Cadastrar</button>
            </div>
        </form>
    </div>
</main>
<?php
// Fechar a conexão com o banco de dados
$banco->fecharConexao();

include_once("./modelos/rodape.html");
?>
<script type="text/javascript">
    function buscar_cidades() {
        var estado = $('#idEstado').val();  //codigo do estado escolhido
        //se encontrou o estado
        if (estado) {
            var url = 'ajax_buscar_cidades.php?idEstado=' + estado;  //caminho do arquivo php que irá buscar as cidades no BD

            $.get(url, function(dataReturn) {
                $('#load_cidades').html(dataReturn);  //coloco na div o retorno da requisicao
            });
        }
    }
</script>


