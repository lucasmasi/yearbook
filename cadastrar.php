<?php
session_start();

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Classe de manipulação de imagem
require_once("./classes/ManipulaImagem.class.php");

// Configurações padrões
include_once("./utils/configuracoes.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

// Imagens
$permissoes = array("gif", "jpeg", "jpg", "png", "image/gif", "image/jpeg", "image/jpg", "image/png");  //strings de tipos e extensoes validas
$partesNomeArquivo = explode(".", basename($_FILES["arquivo"]["name"]));
$extensao = end($partesNomeArquivo);

$imagemObj = new ManipulaImagem();

$origem = basename($_SERVER['HTTP_REFERER']);

// Validações

if ($origem != 'cadastro.php') {
    include_once("./modelos/cabecalho.html");
    echo "<main><div class=\"mensagemErro\"><label>Acesso negado.</label></div></main>";
    include_once("./modelos/rodape.html");
    $banco->fecharConexao();
    die();
}

if (count($_POST) != 8) {
    include_once("./modelos/cabecalho.html");
    echo "<main><div class=\"mensagemErro\"><label>Preencha o formulário novamente. Há campos faltando.</label></div></main>";
    include_once("./modelos/rodape.html");
    $banco->fecharConexao();
    die();
}

if ((in_array($extensao, $permissoes)) && (in_array($_FILES["arquivo"]["type"], $permissoes))) {
    if ($_FILES["arquivo"]["error"] > 0) {
        echo "<h1>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</h1>";
        include_once("./modelos/cabecalho.html");
        echo "<main><div class=\"mensagemErro\"><label>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</label></div></main>";
        include_once("./modelos/rodape.html");
        $banco->fecharConexao();
        die();
    } else { // Arquivo certo
        $senha = htmlspecialchars($_POST['senha']);

        $senhanovamente = htmlspecialchars($_POST['senhanovamente']);

        $login = htmlspecialchars($_POST['login']);

        // Validações de campos

        if (strcmp($senha, $senhanovamente) != 0) {
            include_once("./modelos/cabecalho.html");
            echo "<main><div class=\"mensagemErro\"><label>Preencha o formulário novamente. Há campos faltando.</label><br/><a href='cadastro.php'>Voltar</a></div></main>";
            include_once("./modelos/rodape.html");
            $banco->fecharConexao();
            die();
        }

        // Verificar se o login já está em uso

        $resultados = $banco->obterParticipantePeloLogin($login);

        if (count($resultados) > 0) {
            include_once("./modelos/cabecalho.html");
            echo "<main><div class=\"mensagemErro\"><label>Login já existe. Tente outro login!</label></div></main>";
            include_once("./modelos/rodape.html");
            $banco->fecharConexao();
            die();
        }

        // Tudo está correto salvar usuário no banco e as respectivas imagens

        $nomecompleto = htmlspecialchars($_POST['nomecompleto']);

        $email = htmlspecialchars($_POST['email']);

        $idCidade = htmlspecialchars($_POST['idCidade']);

        $descricao = htmlspecialchars($_POST['descricao']);

        // Copiar a imagem

        $caminhoUpload = DIRIMAGENS . $login;

        if (!file_exists($caminhoUpload)) {
            mkdir($caminhoUpload, 0700);  //permissoes de escrita, leitura e execucao
        }

        $pathCompleto = $caminhoUpload . "/" . basename($_FILES["arquivo"]["name"]);

        if (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $pathCompleto)) {

            $novoPath = $caminhoUpload . "/" . $partesNomeArquivo[0] . "thumbnail." . $partesNomeArquivo[1];

            copy($pathCompleto, $novoPath);

            // Alterar tamanho da imagem para 240 x 320

            $imagemObj->load($pathCompleto);

            $imagemObj->resize(240, 320);

            $imagemObj->save($pathCompleto);

            // Criar a imagem thumbnail

            $imagemObj->load($novoPath);

            $imagemObj->resize(40, 40);

            $imagemObj->save($novoPath);
        } else {
            include_once("./modelos/cabecalho.html");
            echo "<main><div class=\"mensagemErro\"><label>Problemas ao armazenar o arquivo. Tente novamente!</label></div></main>";
            include_once("./modelos/rodape.html");
            $banco->fecharConexao();
            die();
        }

        if ($banco->inserirParticipante($login, sha1($senha), $nomecompleto, $_FILES["arquivo"]["name"], $idCidade, $email, $descricao)) {
            $_SESSION['mensagemSucesso'] = "Cadastro efetuado com sucesso.";
            $banco->fecharConexao();
            header("Location: ./index.php");  //redirecionando para a página principal
        } else {
            include_once("./modelos/cabecalho.html");
            echo "<main><div class=\"mensagemErro\"><label>Erro no cadastro. Tente novamente!</label></div></main>";
            include_once("./modelos/rodape.html");
            $banco->fecharConexao();
            die();
        }
    }
} else {
    include_once("./modelos/cabecalho.html");
    echo "<main><div class=\"mensagemErro\"><label>Arquivo inválido. Tente novamente!</label></div></main>";
    include_once("./modelos/rodape.html");
    $banco->fecharConexao();
    die();
}

$banco->fecharConexao();
?>

