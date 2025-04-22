<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/includes/funciones.php";

inicio_html("Formulario de búsqueda de vehículos", ['/recuperacion/estilos/general.css', '/recuperacion/estilos/formulario.css', '/recuperacion/estilos/tablas.css']);

$tipos = [
    't' => "Turismo",
    'f' => "Furgoneta"
];

$marcas = [
    'fi' => "Fiat",
    'op' => "Opel",
    'me' => "Mercedes"
];

echo "<header>Filtrado de vehículos</header>";

function sanear_validar() {
    global $marcas, $tipos;

    $filtros_saneamiento = [
        'email' => FILTER_SANITIZE_EMAIL,
        'tipo' => FILTER_SANITIZE_SPECIAL_CHARS,
        'marca' => FILTER_SANITIZE_SPECIAL_CHARS,
        'antiguedad' => FILTER_SANITIZE_NUMBER_INT,
        'itv' => FILTER_SANITIZE_SPECIAL_CHARS
    ];

    $datos_saneados = filter_input_array(INPUT_POST, $filtros_saneamiento);

    $filtros_validacion = [
        'email' => FILTER_VALIDATE_EMAIL,
        'tipo' => FILTER_DEFAULT,
        'marca' => FILTER_DEFAULT,
        'antiguedad' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
                'max_range' => 5
            ]
        ],
        'itv' => FILTER_VALIDATE_BOOL
    ];

    $datos_validados = filter_var_array($datos_saneados, $filtros_validacion);

    if(!array_key_exists($datos_validados['tipo'], $tipos)) {
        $datos_validados['tipo'] = false;
    }

    if(!array_key_exists($datos_validados['marca'], $marcas)) {
        $datos_validados['marca'] = false;
    }

    $array_filtrado = array_filter($datos_validados);

    if(count($array_filtrado) < 4 || count($array_filtrado) == 4 && $datos_validados['itv']) {
        return false;
    }
    else {
        return $datos_validados;
    }
}

function mostrar_resultados($datos_validados) {
    global $tipos, $marcas;
    
    $archivo = fopen($_FILES['archivo']['tmp_name'], "r");
    $linea = fgetcsv($archivo);

    echo <<<TABLE
        <table>
            <thead>
                <tr>
                    <td>$linea[0]</td>
                    <td>$linea[1]</td>
                    <td>$linea[2]</td>
                    <td>$linea[3]</td>
                </tr>
            </thead>
            <tbody>
    TABLE;
    
    while($linea = fgetcsv($archivo)) {
        if($linea[0] == $tipos[$datos_validados['tipo']] && $linea[1] == $marcas[$datos_validados['marca']] && $linea[2] == $datos_validados['antiguedad'] && $linea[3] == ($datos_validados['itv'] ? "Si" : "No")) {
            echo <<<TABLA
                <tr>
                    <td>$linea[0]</td>
                    <td>$linea[1]</td>
                    <td>$linea[2]</td>
                    <td>$linea[3]</td>
                </tr>
            TABLA;
        }
    }

    echo "</tbody></table>";
}

function comprobar_archivo() {
    if($_FILES['archivo']['error'] == UPLOAD_ERR_FORM_SIZE) {
        return 1;
    }
    
    $tipo_mime_permitidos = ['text/csv'];

    $tipo_mime1 = $_FILES['archivo']['tmp_name'];
    $tipo_mime2 = mime_content_type($_FILES['archivo']['tmp_name']);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_mime3 = finfo_file($finfo, $_FILES['archivo']['tmp_name']);

    if(!in_array($tipo_mime1, $tipo_mime_permitidos) || !in_array($tipo_mime2, $tipo_mime_permitidos) || !in_array($tipo_mime3, $tipo_mime_permitidos)) {
        return 2;
    }

    if ($_FILES['archivo']['error'] == UPLOAD_ERR_NO_FILE) {
        return 3;
    }

    return 0;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $error = comprobar_archivo();
    
    match($error) {
        1 => "El archivo ha superado el tamaño máximo",
        2 => "El tipo mime tiene que ser CSV",
        3 => "No hay ningún archivo en la petición"
    };

    $datos_validados = sanear_validar();
    if(!$datos_validados) {
        echo "<h3>Error. Los datos no están validados</h3>";
        exit(4);
    }

    mostrar_resultados($datos_validados);
    echo "<p><a href='vehicleSearch.php'>Volver a hacer una petición</a></p>";
}

?>
    <form action"<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=1024 * 200?>">
        <fieldset>
            <legend>Datos del coche</legend>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" size="25" value="<?=$datos_validados['email'] ?? ""?>">

            <label for="tipo">Tipo de vehículo</label>
            <div>
<?php
            foreach ($tipos as $clave => $valor) {
                $checked = (isset($datos_validados['tipo']) && $datos_validados['tipo'] == $clave) ? "checked" : "";
                echo "<input type='checkbox' name='tipo' id='$clave' value='$clave' $checked>$valor";
            }
?>
            </div>

            <label for="marca">Marca</label>
            <select name="marca">
                <option value="">Selecciona una marca</option>
<?php
                foreach ($marcas as $clave => $valor) {
                    $selected = (isset($datos_validados['marca']) && $datos_validados['marca'] == $clave) ? "selected" : "";
                    echo "<option value='$clave' $selected>$valor</option>";
                }
?>
            </select>

            <label for="antiguedad">Antigüedad</label>
            <input type="number" name="antiguedad" id="antiguedad" min="1" max="5" value="<?=$datos_validados['antiguedad'] ?? ""?>">

            <label for="itv">ITV pasada</label>
            <input type="checkbox" name="itv" id="itv" <?=$datos_validados['itv'] ? "checked" : ""?>>

            <label for="archivo">Archivo csv</label>
            <input type="file" name="archivo" id="archivo" accept="text/csv">
        </fieldset>
        <input type="submit" value="Filtrar búsqueda" name="operacion" id="operacion">
    </form>
<?php

fin_html();
?>