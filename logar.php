<?php
session_start();

// Classe de conexão com o banco
require_once("./classes/BancoDeDados.class.php");

// Iniciar o objeto do banco de dados
$banco = new BancoDeDados();

$origem = basename($_SERVER['HTTP_REFERER']);

if ($origem != 'index.php') {
    $_SESSION['mensagemErro'] = "Erro! Tente fazer o login.";
    header("Location: index.php");
    die();
}

if (count($_POST) > 3 && count($_POST) < 2) {
    $_SESSION['mensagemErro'] = "Erro! Tente fazer o login novamente. " . count($_POST);
    header("Location: index.php");
    die();
}

$login = htmlspecialchars($_POST['login']);

$senha = htmlspecialchars($_POST['senha']);

$participante = $banco->obterParticipantePeloLoginSenha($login, sha1($senha));

if (count($participante) == 1) { // Logado
    if (isset($_POST['lembrar'])) { // Lembrar login foi marcado        
        setcookie("login", $login, time()+60*60*24*90);
    } else {
        // Apagar o cookie caso ele exista e o usuário tenha desmarcado a opção de lembrar o login
        if (isset($_COOKIE['login'])) {
            setcookie("login", '', time() - 42000);
        }
    }
    $_SESSION['logado'] = true;
    $_SESSION['participante'] = $participante[0];
    // Encaminhar para página principal
    header("Location: principal.php");
} else {
    $_SESSION['mensagemErro'] = "Login e/ou senha inválidos! Tente novamente.";
    header("Location: index.php");
    die();
}
?>

