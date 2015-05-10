<?php
if (isset($_SESSION['logado'])) {
    if (!$_SESSION['logado']) { // Não esta logado
        include_once("./modelos/cabecalho.html");
        echo "<main><div class=\"mensagemErro\"><label>Você não tem permissão para ver este conteúdo. Por favor, faça login.</label></div></main>";
        include_once("./modelos/rodape.html");
        die();
    }
} else { // Não esta logado
    include_once("./modelos/cabecalho.html");
    echo "<main><div class=\"mensagemErro\"><label>Você não tem permissão para ver este conteúdo. Por favor, faça login.</label></div></main>";
    include_once("./modelos/rodape.html");
    die();
}
?>

