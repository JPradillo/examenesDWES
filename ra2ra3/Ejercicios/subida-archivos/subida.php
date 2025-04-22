<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/includes/funciones.php";

inicio_html("Actividad 7", ["/recuperacion/estilos/general.css", "/recuperacion/estilos/formulario.css", "/recuperacion/estilos/tablas.css"]);

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $filtro_sanear = [
        'dni' => FILTER_SANITIZE_SPECIAL_CHARS,
        'cv' => FILTER_DEFAULT,
        'nombre' => FILTER_SANITIZE_SPECIAL_CHARS,
        'aceptacion' => FILTER_DEFAULT
    ];

    $datos_saneados = filter_input_array(INPUT_POST, $filtro_sanear);

    $filtros_validacion = [
        'dni' => FILTER_DEFAULT,
        'cv' => FILTER_DEFAULT,
        'nombre' => FILTER_DEFAULT,
        'aceptacion' => FILTER_VALIDATE_BOOL
    ];

    $datos_validados = filter_var_array($datos_saneados, $filtros_validacion);

    $mensajes = [];


}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
    <fieldset>
        <legend>Datos personales</legend>
        <label for="dni">DNI</label>
        <input type="text" name="dni" id="dni" required>

        <label for="file"></label>
    </fieldset>
</form>

<?php
fin_html();
?>