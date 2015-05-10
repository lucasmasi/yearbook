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

// Validações

if ($origem != 'edicaoPerfil.php') {
    include_once("./modelos/cabecalho_interno.html");
    echo "<main><div class=\"mensagemErro\"><label>Por favor, edite o formulário antes.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}

if (count($_POST) != 8) {
    include_once("./modelos/cabecalho_interno.html");
    echo "<main><div class=\"mensagemErro\"><label>Preencha o formulário novamente. Há campos faltando.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}

if (!isset($_SESSION['participante'])) {
    include_once("./modelos/cabecalho_interno.html");
    echo "<main><div class=\"mensagemErro\"><label>Erro ao recuperar usuário logado. Por favor, faça o login novamente.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}

$permissoes = array("gif", "jpeg", "jpg", "png", "image/gif", "image/jpeg", "image/jpg", "image/png");  //strings de tipos e extensoes validas
// Obter os dados enviados pelo formulário

$login = htmlspecialchars($_POST['login']);

$senha = htmlspecialchars($_POST['senha']);

$senhanovamente = htmlspecialchars($_POST['senhanovamente']);

$nomecompleto = htmlspecialchars($_POST['nomecompleto']);

$email = htmlspecialchars($_POST['email']);

$idCidade = htmlspecialchars($_POST['idCidade']);

$descricao = htmlspecialchars($_POST['descricao']);

// Carregar o participante atual

$participante = $_SESSION['participante'];

$imagemObj = new ManipulaImagem();

// Verificar se as senhas foram alteradas
if (empty($senha) && empty($senhanovamente)) {
    // Senha não foi modificada
    // Verificar se o arquivo foi enviado
    if (empty($_FILES['arquivo']['name'])) {
        // Arquivo também não foi modificado
        // Verificar se alterou o login
        if (strcmp($participante['login'], $login) == 0) {
            // Login não foi alterado
            // Atualizar os outros campos na base de dados
            // Verificar se os outros campos foram modificados
            if (!(strcmp($participante['email'], $email) == 0 &&
                    strcmp($participante['nomeCompleto'], $nomecompleto) == 0 &&
                    strcmp($participante['descricao'], $descricao) == 0 &&
                    $participante['idCidade'] == $idCidade)) { // Caso algo tenha mudado
//                $SQLAtualizarParticipante = 'UPDATE participantes SET email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//                $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//                $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array($email, $nomecompleto, $descricao, $idCidade, $login));

                if ($banco->atualizarParticipanteENDC($email, $nomecompleto, $descricao, $idCidade, $login)) { // Atualizado com sucesso
                    $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
                } else {
                    include_once("./modelos/cabecalho_interno.html");
                    echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                    include_once("./modelos/rodape.html");
                    die();
                }
            } else { // Nada foi alterado
                header("Location: principal.php");
                die();
            }
        } else { // Alterou o login
            // Verificar se o login já está em uso
//            $SQLNovoLogin = 'SELECT * FROM participantes WHERE login = ?';
//
//            $operacao = $conexao->prepare($SQLNovoLogin);
//
//            $pesquisar = $operacao->execute(array($login));
//
//            $resultados = $operacao->fetchAll();
            
            $resultados = $banco->obterParticipantePeloLogin($login);

            if (count($resultados) > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Login já está em uso. Tente outro login.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Login pode ser atualizado
            // Renomear a pasta com os arquivos de imagem do login

            $caminhoAntigo = DIRIMAGENS . $participante['login'];

            $caminhoNovo = DIRIMAGENS . $login;

            if (rename($caminhoAntigo, $caminhoNovo)) {
                // Caso sucesso ao renomear a pasta, atualizar os dados

//                $SQLAtualizarParticipante = 'UPDATE participantes SET login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//                $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//                $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array($login, $email, $nomecompleto, $descricao, $idCidade, $participante['login']));

                if ($banco->atualizarParticipanteLENDC($login, $email, $nomecompleto, $descricao, $idCidade, $participante['login'])) { // Atualizado com sucesso
                    $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
                } else {
                    include_once("./modelos/cabecalho_interno.html");
                    echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                    include_once("./modelos/rodape.html");
                    die();
                }
            } else { // Erro ao renomear a pasta
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        }
    } else {
        // Foi enviado novo arquivo
        // Verificar se alterou o login
        if (strcmp($participante['login'], $login) == 0) {
            // Login não foi alterado
            // Verificar se o arquivo enviado está correto

            $partesArquivoImagemNova = explode(".", basename($_FILES["arquivo"]["name"]));

            $extensao = end($partesArquivoImagemNova);

            if (!((in_array($extensao, $permissoes)) && (in_array($_FILES["arquivo"]["type"], $permissoes)))) {
                // Não é um arquivo permitido
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Arquivo enviado não é permitido. Tente outro arquivo.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Verificar se houve erro no envio do arquivo
            if ($_FILES["arquivo"]["error"] > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo correto
            // Remover os arquivos de imagens antigos do diretório
            $caminhoImagens = DIRIMAGENS . $login;

            $imagemMaior = $caminhoImagens . "/" . $participante['arquivoFoto'];

            $partesArquivoImagem = explode(".", $participante['arquivoFoto']);

            $imagemMenor = $caminhoImagens . "/" . $partesArquivoImagem[0] . "thumbnail." . $partesArquivoImagem[1];

            if (!(unlink($imagemMaior) && unlink($imagemMenor))) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivos já apagados
            $imagemMaiorNova = $caminhoImagens . "/" . basename($_FILES["arquivo"]["name"]);

            if (!move_uploaded_file($_FILES["arquivo"]["tmp_name"], $imagemMaiorNova)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Problemas ao armazenar o arquivo. Tente novamente!</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo movido com sucesso
            $imagemMenorNova = $caminhoImagens . "/" . $partesArquivoImagemNova[0] . "thumbnail." . $partesArquivoImagemNova[1];

            // Copiar o arquivo menor
            copy($imagemMaiorNova, $imagemMenorNova);

            // Ajustar os tamanhos dos arquivos
            // Alterar tamanho da imagem para 240 x 320

            $imagemObj->load($imagemMaiorNova);

            $imagemObj->resize(240, 320);

            $imagemObj->save($imagemMaiorNova);

            // Criar a imagem thumbnail

            $imagemObj->load($imagemMenorNova);

            $imagemObj->resize(40, 40);

            $imagemObj->save($imagemMenorNova);

            // Imagens com tamanhos certos. Atualizar os dados no banco

//            $SQLAtualizarParticipante = 'UPDATE participantes SET arquivoFoto = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//            $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//            $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(basename($_FILES["arquivo"]["name"]), $email, $nomecompleto, $descricao, $idCidade, $login));

            if ($banco->atualizarParticipanteFENDC(basename($_FILES["arquivo"]["name"]), $email, $nomecompleto, $descricao, $idCidade, $login)) { // Atualizado com sucesso
                $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
            } else {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        } else { // Alterou o login
            // Verificar se o login já está em uso
//            $SQLNovoLogin = 'SELECT * FROM participantes WHERE login = ?';
//
//            $operacao = $conexao->prepare($SQLNovoLogin);
//
//            $pesquisar = $operacao->execute(array($login));
//
//            $resultados = $operacao->fetchAll();
            
            $resultados = $banco->obterParticipantePeloLogin($login);

            if (count($resultados) > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Login já está em uso. Tente outro login.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Login pode ser atualizado
            // Verificar se o arquivo enviado está correto

            $partesArquivoImagemNova = explode(".", basename($_FILES["arquivo"]["name"]));

            $extensao = end($partesArquivoImagemNova);

            if (!((in_array($extensao, $permissoes)) && (in_array($_FILES["arquivo"]["type"], $permissoes)))) {
                // Não é um arquivo permitido
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Arquivo enviado não é permitido. Tente outro arquivo.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Verificar se houve erro no envio do arquivo
            if ($_FILES["arquivo"]["error"] > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo correto
            // Remover os arquivos de imagens antigos do diretório
            $caminhoImagens = DIRIMAGENS . $participante['login'];

            $imagemMaior = $caminhoImagens . "/" . $participante['arquivoFoto'];

            $partesArquivoImagem = explode(".", $participante['arquivoFoto']);

            $imagemMenor = $caminhoImagens . "/" . $partesArquivoImagem[0] . "thumbnail." . $partesArquivoImagem[1];

            if (!(unlink($imagemMaior) && unlink($imagemMenor))) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivos já apagados
            // Remover o diretório

            if (!rmdir($caminhoImagens)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            $caminhoImagens = DIRIMAGENS . $login;

            // Criar o diretório

            if (!file_exists($caminhoImagens)) {
                mkdir($caminhoImagens, 0700);  //permissoes de escrita, leitura e execucao
            }

            $imagemMaiorNova = $caminhoImagens . "/" . basename($_FILES["arquivo"]["name"]);

            if (!move_uploaded_file($_FILES["arquivo"]["tmp_name"], $imagemMaiorNova)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Problemas ao armazenar o arquivo. Tente novamente!</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo movido com sucesso

            $imagemMenorNova = $caminhoImagens . "/" . $partesArquivoImagemNova[0] . "thumbnail." . $partesArquivoImagemNova[1];

            // Copiar o arquivo menor
            copy($imagemMaiorNova, $imagemMenorNova);

            // Ajustar os tamanhos dos arquivos            
            // Alterar tamanho da imagem para 240 x 320

            $imagemObj->load($imagemMaiorNova);

            $imagemObj->resize(240, 320);

            $imagemObj->save($imagemMaiorNova);

            // Criar a imagem thumbnail

            $imagemObj->load($imagemMenorNova);

            $imagemObj->resize(40, 40);

            $imagemObj->save($imagemMenorNova);

            // Imagens com tamanhos certos. Atualizar os dados no banco

//            $SQLAtualizarParticipante = 'UPDATE participantes SET arquivoFoto = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//            $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//            $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(basename($_FILES["arquivo"]["name"]), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login']));

            if ($banco->atualizarParticipanteFLENDC(basename($_FILES["arquivo"]["name"]), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login'])) { // Atualizado com sucesso
                $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
            } else {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        }
    }
} else { // Caso nova senha tenha sido enviada
    // Verificar se as senhas estão corretas
    $senha = htmlspecialchars($_POST['senha']);

    $senhanovamente = htmlspecialchars($_POST['senhanovamente']);

    if (strcmp($senha, $senhanovamente) != 0) {
        include_once("./modelos/cabecalho_interno.html");
        echo "<main><div class=\"mensagemErro\"><label>Senhas não conferem. Tente novamente.</label></div></main>";
        include_once("./modelos/rodape.html");
        die();
    }

    // Senhas corretas
    // Verificar se o arquivo foi enviado
    if (empty($_FILES['arquivo']['name'])) {
        // Arquivo também não foi modificado
        // Verificar se alterou o login
        if (strcmp($participante['login'], $login) == 0) {
            // Login não foi alterado
            // Atualizar os outros campos na base de dados
            // Verificar se os outros campos foram modificados
            if (!(strcmp($participante['email'], $email) == 0 &&
                    strcmp($participante['nomeCompleto'], $nomecompleto) == 0 &&
                    strcmp($participante['descricao'], $descricao) == 0 &&
                    $participante['idCidade'] == $idCidade)) { // Caso algo diferente da senha tenha mudado
//                $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//                $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//                $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(sha1($senha), $email, $nomecompleto, $descricao, $idCidade, $login));

                if ($banco->atualizarParticipanteSENDC(sha1($senha), $email, $nomecompleto, $descricao, $idCidade, $login)) { // Atualizado com sucesso
                    $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
                } else {
                    include_once("./modelos/cabecalho_interno.html");
                    echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                    include_once("./modelos/rodape.html");
                    die();
                }
            } else { // Somente a senha foi alterada
//                $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ? WHERE login = ?';
//
//                $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//                $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(sha1($senha)));

                if ($banco->atualizarParticipanteS(sha1($senha), $login)) { // Atualizado com sucesso
                    $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
                } else {
                    include_once("./modelos/cabecalho_interno.html");
                    echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                    include_once("./modelos/rodape.html");
                    die();
                }
            }
        } else { // Alterou o login
            // Verificar se o login já está em uso
//            $SQLNovoLogin = 'SELECT * FROM participantes WHERE login = ?';
//
//            $operacao = $conexao->prepare($SQLNovoLogin);
//
//            $pesquisar = $operacao->execute(array($login));
//
//            $resultados = $operacao->fetchAll();
            
            $resultados = $banco->obterParticipantePeloLogin($login);

            if (count($resultados) > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Login já está em uso. Tente outro login.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Login pode ser atualizado
            // Renomear a pasta com os arquivos de imagem do login

            $caminhoAntigo = DIRIMAGENS . $participante['login'];

            $caminhoNovo = DIRIMAGENS . $login;

            if (rename($caminhoAntigo, $caminhoNovo)) {
                // Caso sucesso ao renomear a pasta, atualizar os dados

//                $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//                $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//                $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(sha1($senha), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login']));

                if ($banco->atualizarParticipanteSLENDC(sha1($senha), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login'])) { // Atualizado com sucesso
                    $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
                } else {
                    include_once("./modelos/cabecalho_interno.html");
                    echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                    include_once("./modelos/rodape.html");
                    die();
                }
            } else { // Erro ao renomear a pasta
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        }
    } else {
        // Foi enviado novo arquivo
        // Verificar se alterou o login
        if (strcmp($participante['login'], $login) == 0) {
            // Login não foi alterado
            // Verificar se o arquivo enviado está correto

            $partesArquivoImagemNova = explode(".", basename($_FILES["arquivo"]["name"]));

            $extensao = end($partesArquivoImagemNova);

            if (!((in_array($extensao, $permissoes)) && (in_array($_FILES["arquivo"]["type"], $permissoes)))) {
                // Não é um arquivo permitido
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Arquivo enviado não é permitido. Tente outro arquivo.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Verificar se houve erro no envio do arquivo
            if ($_FILES["arquivo"]["error"] > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo correto
            // Remover os arquivos de imagens antigos do diretório
            $caminhoImagens = DIRIMAGENS . $login;

            $imagemMaior = $caminhoImagens . "/" . $participante['arquivoFoto'];

            $partesArquivoImagem = explode(".", $participante['arquivoFoto']);

            $imagemMenor = $caminhoImagens . "/" . $partesArquivoImagem[0] . "thumbnail." . $partesArquivoImagem[1];

            if (!(unlink($imagemMaior) && unlink($imagemMenor))) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivos já apagados
            $imagemMaiorNova = $caminhoImagens . "/" . basename($_FILES["arquivo"]["name"]);

            if (!move_uploaded_file($_FILES["arquivo"]["tmp_name"], $imagemMaiorNova)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Problemas ao armazenar o arquivo. Tente novamente!</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo movido com sucesso

            $imagemMenorNova = $caminhoImagens . "/" . $partesArquivoImagemNova[0] . "thumbnail." . $partesArquivoImagemNova[1];

            // Copiar o arquivo menor
            copy($imagemMaiorNova, $imagemMenorNova);

            // Ajustar os tamanhos dos arquivos
            // Alterar tamanho da imagem para 240 x 320

            $imagemObj->load($imagemMaiorNova);

            $imagemObj->resize(240, 320);

            $imagemObj->save($imagemMaiorNova);

            // Criar a imagem thumbnail

            $imagemObj->load($imagemMenorNova);

            $imagemObj->resize(40, 40);

            $imagemObj->save($imagemMenorNova);

            // Imagens com tamanhos certos. Atualizar os dados no banco

//            $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, arquivoFoto = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//            $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//            $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(sha1($senha), basename($_FILES["arquivo"]["name"]), $email, $nomecompleto, $descricao, $idCidade, $login));

            if ($banco->atualizarParticipanteSFENDC(sha1($senha), basename($_FILES["arquivo"]["name"]), $email, $nomecompleto, $descricao, $idCidade, $login)) { // Atualizado com sucesso
                $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
            } else {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        } else { // Alterou o login
            // Verificar se o login já está em uso
//            $SQLNovoLogin = 'SELECT * FROM participantes WHERE login = ?';
//
//            $operacao = $conexao->prepare($SQLNovoLogin);
//
//            $pesquisar = $operacao->execute(array($login));
//
//            $resultados = $operacao->fetchAll();
            
            $resultados = $banco->obterParticipantePeloLogin($login);

            if (count($resultados) > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Login já está em uso. Tente outro login.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Login pode ser atualizado
            // Verificar se o arquivo enviado está correto

            $partesArquivoImagemNova = explode(".", basename($_FILES["arquivo"]["name"]));

            $extensao = end($partesArquivoImagemNova);

            if (!((in_array($extensao, $permissoes)) && (in_array($_FILES["arquivo"]["type"], $permissoes)))) {
                // Não é um arquivo permitido
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Arquivo enviado não é permitido. Tente outro arquivo.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Verificar se houve erro no envio do arquivo
            if ($_FILES["arquivo"]["error"] > 0) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro no envio, código: " . $_FILES["arquivo"]["error"] . "</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo correto
            // Remover os arquivos de imagens antigos do diretório
            $caminhoImagens = DIRIMAGENS . $participante['login'];

            $imagemMaior = $caminhoImagens . "/" . $participante['arquivoFoto'];

            $partesArquivoImagem = explode(".", $participante['arquivoFoto']);

            $imagemMenor = $caminhoImagens . "/" . $partesArquivoImagem[0] . "thumbnail." . $partesArquivoImagem[1];

            if (!(unlink($imagemMaior) && unlink($imagemMenor))) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivos já apagados
            // Remover o diretório

            if (!rmdir($caminhoImagens)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar arquivos. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            $caminhoImagens = DIRIMAGENS . $login;

            // Criar o diretório

            if (!file_exists($caminhoImagens)) {
                mkdir($caminhoImagens, 0700);  //permissoes de escrita, leitura e execucao
            }

            $imagemMaiorNova = $caminhoImagens . "/" . basename($_FILES["arquivo"]["name"]);

            if (!move_uploaded_file($_FILES["arquivo"]["tmp_name"], $imagemMaiorNova)) {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Problemas ao armazenar o arquivo. Tente novamente!</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }

            // Arquivo movido com sucesso

            $imagemMenorNova = $caminhoImagens . "/" . $partesArquivoImagemNova[0] . "thumbnail." . $partesArquivoImagemNova[1];

            // Copiar o arquivo menor
            copy($imagemMaiorNova, $imagemMenorNova);

            // Ajustar os tamanhos dos arquivos
            // Alterar tamanho da imagem para 240 x 320

            $imagemObj->load($imagemMaiorNova);

            $imagemObj->resize(240, 320);

            $imagemObj->save($imagemMaiorNova);

            // Criar a imagem thumbnail

            $imagemObj->load($imagemMenorNova);

            $imagemObj->resize(40, 40);

            $imagemObj->save($imagemMenorNova);

            // Imagens com tamanhos certos. Atualizar os dados no banco

//            $SQLAtualizarParticipante = 'UPDATE participantes SET senha = ?, arquivoFoto = ?, login = ?, email = ?, nomeCompleto = ?, descricao = ?, cidade = ? WHERE login = ?';
//
//            $operacaoAtualizarParticipantes = $conexao->prepare($SQLAtualizarParticipante);
//
//            $atualizacaoParticipantes = $operacaoAtualizarParticipantes->execute(array(sha1($senha), basename($_FILES["arquivo"]["name"]), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login']));

            if ($banco->atualizarParticipanteSFLENDC(sha1($senha), basename($_FILES["arquivo"]["name"]), $login, $email, $nomecompleto, $descricao, $idCidade, $participante['login'])) { // Atualizado com sucesso
                $_SESSION['mensagemSucesso'] = "Perfil atualizado com sucesso";
            } else {
                include_once("./modelos/cabecalho_interno.html");
                echo "<main><div class=\"mensagemErro\"><label>Erro ao atualizar o cadastro. Tente novamente.</label></div></main>";
                include_once("./modelos/rodape.html");
                die();
            }
        }
    }
}
// Após atualiza o perfil recarregar o participante na session
unset($_SESSION['participante']);

//$SQLLogin = 'SELECT * FROM participantes LEFT JOIN cidades ON participantes.cidade = cidades.idCidade LEFT JOIN estados ON cidades.idEstado = estados.idEstado WHERE login = ?';
//
//$operacao = $conexao->prepare($SQLLogin);
//
//$pesquisar = $operacao->execute(array($login));

$participanteNovo = $banco->obterParticipanteCompletoPeloLogin($login);

$_SESSION['participante'] = $participanteNovo[0];

$banco->fecharConexao();

header("Location: principal.php");
//echo count($_POST) . "<br/>";
//echo "senha = " . $_POST['senha'] . "<br/>";
//echo count($_FILES) . "<br/>";
//
//echo $_FILES['arquivo']['name'];
//if (empty($_FILES['arquivo']['name'])) {
//    echo "vazio";
//} else {
//    echo "não vazio";
//}
?>
