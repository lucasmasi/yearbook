<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

include_once("./modelos/cabecalho_interno.html");

if (!isset($_SESSION['participante'])) {
    echo "<main><label class=\"mensagemErro\">Nenhum perfil será mostrado.</label></main>";
    include_once("./modelos/rodape.html");
    die();
}
?>
<main>
    <div id="dados_cadastro">
        <h2>Edição do seu perfil</h2>
        <form method="post" action="./editar.php" role="form" enctype="multipart/form-data">
            <dl>
                <dt>Login</dt>
                <dd><input type="text" name="login" class="form-input" value="<?php echo $_SESSION['participante']['login']; ?>" required autofocus/></dd>
                <dt>Senha</dt>
                <dd><input type="password" name="senha" class="form-input"/></dd>
                <dt>Senha novamente</dt>
                <dd><input type="password" name="senhanovamente" class="form-input"/></dd>
                <dt>Nome completo</dt>
                <dd><input type="text" name="nomecompleto" class="form-input" value="<?php echo $_SESSION['participante']['nomeCompleto']; ?>" required/></dd>
                <dt>E-mail</dt>
                <dd><input type="email" name="email" class="form-input" value="<?php echo $_SESSION['participante']['email']; ?>" required/></dd>
                <dt>Estado</dt>
                <dd>
                    <select name="idEstado" id="idEstado" onchange="buscar_cidades();">
                        <?php
                        $resultados = $resultadoEstado = $banco->obterEstados();
                        if (count($resultados) > 0) {
                            foreach ($resultados as $estado) {
                                if (strcmp($estado['idEstado'], $_SESSION['participante']['idEstado']) == 0) {
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
                            $resultadoCidades = $banco->obterCidades($_SESSION['participante']['idEstado']);
                            if (count($resultadoCidades) > 0) {
                                foreach ($resultadoCidades as $cidade) {
                                    if (strcmp($cidade['idCidade'], $_SESSION['participante']['idCidade']) == 0) {
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
                    <textarea name="descricao" class="form-input" rows="3" required><?php echo $_SESSION['participante']['descricao']; ?></textarea>
                </dd>
                <dt>Foto</dt>
                <dd>
                    <input type="file" name="arquivo" id="arquivo" placeholder="Escolha um arquivo"/>
                </dd>
            </dl>
            <div class="btn_salvar">
                <button class="btn btn-lg btn-success btn-block" type="submit">Salvar</button>
            </div>

        </form>
    </div>
</main>
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
<?php
$banco->fecharConexao();
include_once("./modelos/rodape.html");
?>