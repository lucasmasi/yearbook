<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

// Verificar se os dados do participante estão na sessão
if (!isset($_SESSION['participante'])) {
    include_once("./modelos/cabecalho.html");
    echo "<main><div class=\"mensagemErro\"><label class=\"mensagemErro\">Nenhum perfil será mostrado.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}

include_once("./modelos/cabecalho_interno.html");

$participante = $_SESSION['participante'];

$caminhoImagem = "./" . DIRIMAGENS . "/" . $participante['login'] . "/" . $participante['arquivoFoto'];
?>
<main>
    <?php
    if (isset($_SESSION['mensagemSucesso'])) {
        echo "<main><div class=\"mensagemSucesso\">" . $_SESSION['mensagemSucesso'] . "</div></main>";
        unset($_SESSION['mensagemSucesso']);
    }
    ?>
    <div>
        <h2>Seu perfil</h2>
        <figure>
            <?php echo"<img src=\"" . $caminhoImagem . "\" alt=\"Imagem de " . $participante['nomeCompleto'] . "\" title=\"" . $participante['nomeCompleto'] . "\" height=\"320\" width=\"240\"/>"; ?>    
        </figure>
        <dl>
            <dt>Nome</dt>
            <dd><?php echo $participante['nomeCompleto']; ?></dd>
            <dt>E-mail</dt>
            <dd><?php echo "<a href=\"mailto:" . $participante['email'] . "\">" . $participante['email'] . "</a>"; ?></dd>
            <dt>Estado</dt>
            <dd><?php echo $participante['nomeEstado']; ?></dd>
            <dt>Cidade</dt>
            <dd><?php echo $participante['nomeCidade']; ?></dd>
            <dt>Descrição</dt>
            <dd><?php echo $participante['descricao']; ?></dd>
        </dl>
    </div>

    <?php
    if (isset($_COOKIE['perfilvisitado'])) {
        ?>
        <div id="perfilvisitado">
            <h2>Último perfil visitado</h2>
            <?php
            $loginVisitado = htmlspecialchars($_COOKIE['perfilvisitado']);

            $participante_visitado = $banco->obterFNEDeParticipantePeloLogin($loginVisitado);

            if (count($participante_visitado) == 1) {
                $temp = explode(".", $participante_visitado[0]['arquivoFoto']);

                $caminhoImagem_visitado = DIRIMAGENS . $loginVisitado . "/" . $temp[0] . "thumbnail." . $temp[1];
                ?>
                <figure>
                    <?php echo"<img class=\"figura\" src=\"" . $caminhoImagem_visitado . "\" alt=\"Imagem de " . $participante_visitado[0]['nomeCompleto'] . "\" title=\"" . $participante_visitado[0]['nomeCompleto'] . "\" height=\"40\" width=\"40\"/>"; ?>    
                </figure>
                <dl>
                    <dt>Nome</dt>
                    <dd><?php echo $participante_visitado[0]['nomeCompleto']; ?></dd>
                    <dt>E-mail</dt>
                    <dd><?php echo "<a href=\"mailto:" . $participante_visitado[0]['email'] . "\">" . $participante_visitado[0]['email'] . "</a>"; ?></dd>
                </dl>
                <a class="link_ver_mais" href="perfil.php?usuario=<?php echo $loginVisitado; ?>">Ver mais</a>
            <?php } ?>
        </div>
        <div id="outrosperfis_visitado">
            <h2>Outros participantes</h2>
            <?php
            $participantes = $banco->obterLFNDeParticipantesDiferenteVisitado($participante['login'], $loginVisitado);

            if (count($participantes) > 0) {
                foreach ($participantes as $participante) {
                    $temp = explode(".", $participante['arquivoFoto']);

                    $caminhoImagem = DIRIMAGENS . $participante['login'] . "/" . $temp[0] . "thumbnail." . $temp[1];

                    echo "<a href=\"perfil.php?usuario=" . $participante['login'] . "\">";
                    echo "<figure class=\"mini\" ><img class=\"figura\" width=\"40\" height=\"40\" src=\"" . $caminhoImagem . "\"/></figure><figcaption>" . $participante['nomeCompleto'] . "</figcaption></a>";
                }
            }
            ?>
        </div>
        <?php
    } else {

        $participantes = $banco->obterLFNDeParticipantesDiferente($participante['login']);

        if (count($participantes) > 0) {
            
            echo "<div id=\"outrosperfis\"><h2>Outros participantes</h2>";

            foreach ($participantes as $participante) {
                $temp = explode(".", $participante['arquivoFoto']);

                $caminhoImagem = DIRIMAGENS . $participante['login'] . "/" . $temp[0] . "thumbnail." . $temp[1];

                echo "<a href=\"perfil.php?usuario=" . $participante['login'] . "\">";
                echo "<figure class=\"mini\" ><img class=\"figura\" width=\"40\" height=\"40\" src=\"" . $caminhoImagem . "\"/></figure><figcaption>" . $participante['nomeCompleto'] . "</figcaption></a>";
            }
            
            echo "</div>";
        }
        
    }
    ?>
</main>
<?php
$banco->fecharConexao();

include_once("./modelos/rodape.html");
?>
