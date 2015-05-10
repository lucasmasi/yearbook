<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

include_once("./modelos/cabecalho_interno.html");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

if (!isset($_GET['usuario'])) {
    echo "<main><div><label class=\"mensagemErro\">Nenhum perfil será mostrado.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}

$login = htmlspecialchars($_GET['usuario']);

// Setar cookie do último perfil visitado
$validade = time() + 3600 * 24 * 365;

if (!isset($_COOKIE['perfilvisitado'])) {
    unset($_COOKIE['perfilvisitado']);
}

setcookie("perfilvisitado", $login, $validade);

$participante = $banco->obterParticipanteCompletoPeloLogin($login);

if (count($participante) == 1) {
    $caminhoImagem = "./" . DIRIMAGENS . "/" . $participante[0]['login'] . "/" . $participante[0]['arquivoFoto'];
} else {
    echo "<label class=\"mensagemErro\">Nenhum perfil será mostrado.</label>";
    include_once("./modelos/rodape.html");
    die();
}
?>
<main>
    <div>
        <h2>Dados do perfil</h2>
        <figure>
            <?php echo"<img src=\"" . $caminhoImagem . "\" alt=\"Imagem de " . $participante[0]['nomeCompleto'] . "\" title=\"" . $participante[0]['nomeCompleto'] . "\" height=\"320\" width=\"240\"/>"; ?>    
        </figure>
        <dl>
            <dt>Nome</dt>
            <dd><?php echo $participante[0]['nomeCompleto']; ?></dd>
            <dt>E-mail</dt>
            <dd><?php echo "<a href=\"mailto:" . $participante[0]['email'] . "\">" . $participante[0]['email'] . "</a>"; ?></dd>
            <dt>Estado</dt>
            <dd><?php echo $participante[0]['nomeEstado']; ?></dd>
            <dt>Cidade</dt>
            <dd><?php echo $participante[0]['nomeCidade']; ?></dd>
            <dt>Descrição</dt>
            <dd><?php echo $participante[0]['descricao']; ?></dd>
        </dl>
    </div>
</main>
<?php
$banco->fecharConexao();
include_once("./modelos/rodape.html");
?>