<?php
session_start();

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

include_once("./modelos/cabecalhoIndex.html");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();
?>
<nav>
    <div class="menuLogin">
        <form method="post" action="./logar.php" role="form">
            <div class="primeira_linha">
                <strong style="display: table-cell;">Faça seu login</strong>

                <input type="text" name="login" class="entrada" placeholder="Login"
                       value ="<?php
                       if (isset($_COOKIE["login"]) && !isset($_SESSION['apagarsession'])) {
                           echo htmlspecialchars($_COOKIE["login"]);
                       }
                       ?>"
                       required autofocus style="display: table-cell"/>

                <input type="password" name="senha" class="entrada" placeholder="Senha" required style="display: table-cell;"/>

                <button type="submit" style="display: table-cell;">Entrar</button>
            </div>
            <div class="segunda_linha">
                <div></div>
                <div>
                    <input type="checkbox" name="lembrar" value="lembrar-login" id="lembrarlogin" <?php
                    if (isset($_COOKIE["login"]) && !isset($_SESSION['apagarsession'])) {
                        echo 'checked';
                    }
                    ?>>
                    <label for="lembrarlogin">Lembrar meu login.
                        <?php
                        if (isset($_SESSION['mensagemErro'])) {
                            echo "<span class=\"mensagemErro\">" . $_SESSION['mensagemErro'] . "</span>";
                            unset($_SESSION['mensagemErro']);
                        } else if (isset($_SESSION['mensagemSucesso'])) {
                            echo "<span class=\"mensagemSucesso\">" . $_SESSION['mensagemSucesso'] . "</span>";
                            unset($_SESSION['mensagemSucesso']);
                        }
                        ?>
                    </label>  
                </div>
            </div>
        </form>
    </div>
</nav>
<main>
    <div class="conteudo">
        <div class="texto">
            <p class="principal">Yearbook dos alunos de especialização em Desenvolvimento de Aplicações Web da Puc Minas 2014.</p>
            <p class="secundario">Ainda não faz parte? <a href="cadastro.php">Clique aqui</a> e cadastre-se.</p>
        </div>
        <div class="imagens">
            <?php
            $participantes = $banco->obterLoginFotoNomeDeParticipantes();

            if (count($participantes) > 0) {
                echo "<p>Participantes que já se cadastraram</p>";

                foreach ($participantes as $participante) {
                    $temp = explode(".", $participante['arquivoFoto']);

                    $caminhoImagem = DIRIMAGENS . $participante['login'] . "/" . $temp[0] . "thumbnail." . $temp[1];

                    echo "<a href=\"perfil.php?usuario=" . $participante['login'] . "\">";
                    echo "<figure class=\"mini\" title=\" " . $participante['nomeCompleto'] . "  \" ><img class=\"figura\" width=\"40\" height=\"40\" src=\"" . $caminhoImagem . "\"/></figure></a>";
                }
            }

            $banco->fecharConexao();
            ?>
        </div>            
    </div>
</main>
<?php
if (isset($_SESSION['apagarsession'])) { // Caso apagar o perfil remover tudo
    $_SESSION = array();  //Limpa o vetor de sessão
    // Se queremos terminar a própria sessão, precisamos matar todos os cookies
    foreach ($_COOKIE as $nome => $valor) {
        setcookie($nome, "", time() - 1000);   //apaga todos os cookies disponíveis para este domínio
    }
    session_destroy();  // Destruímos a sessão em si
}
include_once("./modelos/rodape.html");
?>

