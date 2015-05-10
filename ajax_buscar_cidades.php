<?php
require_once("./classes/BancoDeDados.class.php");

$banco = new BancoDeDados();

// Obter o id do estado passado por parametro
if (isset($_REQUEST['idEstado'])) {
    $idEstado = htmlspecialchars($_REQUEST['idEstado']);
} else { // Nenhum estado foi passado
    $idEstado = -1; // Não irá carregar nenhuma cidade
}

$cidades = $banco->obterCidades($idEstado);
?>
<select name="idCidade">
    <?php
    foreach ($cidades as $cidade) {
        echo "<option value =\"" . $cidade['idCidade'] . "\">" . $cidade['nomeCidade'] . "</option>";
    }
    ?>
</select>
<?php 
$banco->fecharConexao();
?>
