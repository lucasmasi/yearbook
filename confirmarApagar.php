<?php
session_start();

// Verificar se tem permissão para executar
require_once("./utils/verificarPermissao.php");

include_once("./modelos/cabecalho_interno.html");
?>
<main>
    <div id="conteudo_apagar">
        <strong>Deseja realmente apagar o seu perfil?</strong>
        <div>
            <a href="apagar.php">Sim</a>
            <a href="principal.php">Não</a>
        </div>
    </div>

</main>
<?php
include_once("./modelos/rodape.html");
?>

