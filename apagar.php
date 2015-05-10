<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Classe de manipulação de imagem
require_once("./classes/ManipulaImagem.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

// Verificar se a página anterior foi edicaoPerfil.php
$origem = basename($_SERVER['HTTP_REFERER']);

if ($origem != 'confirmarApagar.php') { // Informar erro
    include_once("./modelos/cabecalho_interno.html");
    echo "<main><div class=\"mensagemErro\"><label>Por favor, confirme a remoção anteriormente.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
} else { // Apagar
    // Carregar o participante atual
    $participante = $_SESSION['participante'];

    // Remover os arquivos de imagens antigos do diretório
    $caminhoImagens = DIRIMAGENS . $participante['login'];

    $imagemMaior = $caminhoImagens . "/" . $participante['arquivoFoto'];

    $partesArquivoImagem = explode(".", $participante['arquivoFoto']);

    $imagemMenor = $caminhoImagens . "/" . $partesArquivoImagem[0] . "thumbnail." . $partesArquivoImagem[1];

    if (!(unlink($imagemMaior) && unlink($imagemMenor))) {
        include_once("./modelos/cabecalho_interno.html");
        echo "<main><div class=\"mensagemErro\"><label>Erro ao remover arquivos. Tente novamente.</label></div></main>";
        include_once("./modelos/rodape.html");
        die();
    }

    // Arquivos já apagados. Remover o diretório
    if (!rmdir($caminhoImagens)) {
        include_once("./modelos/cabecalho_interno.html");
        echo "<main><div class=\"mensagemErro\"><label>Erro ao remover diretório. Tente novamente.</label></div></main>";
        include_once("./modelos/rodape.html");
        die();
    }

    // Remover o participante do banco de dados

    if ($banco->removerParticipante($participante['login'])) {
        // Removido com sucesso
        $_SESSION['apagarsession'] = true;
        $_SESSION['mensagemSucesso'] = "Perfil removido com sucesso.";
        $banco->fecharConexao();
        header("Location: ./index.php");  //redirecionando para a página principal
    } else {
        include_once("./modelos/cabecalho_interno.html");
        echo "<main><div class=\"mensagemErro\"><label>Erro ao remover o cadastro. Tente novamente.</label></div></main>";
        include_once("./modelos/rodape.html");
        $banco->fecharConexao();
        die();
    }
}
?>

