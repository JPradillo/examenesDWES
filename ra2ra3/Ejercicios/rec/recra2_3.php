<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/includes/funciones.php";

define("TAMAYO_MAXIMO_KB", 200);

inicio_html("Recuperación RA2 RA3", ["/recuperacion/estilos/general.css", "/recuperacion/estilos/formulario.css", "/recuperacion/estilos/tablas.css"]);

echo "<header>Recuperación RA2 - RA3</header>";

$marcas = [
    "fi" => "Fiat",
    "op" => "Opel",
    "me" => "Mercedes"
];

$tipos = [
    "T" => 'Turismo',
    "F" => 'Furgoneta'
];

function muestra_formulario(array $marcas, array $tipos) {
?>
    <form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=200 * 1024?>">
        <fieldset>
            <legend>Datos del vehículo a buscar</legend>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="tipo">Tipo</label>
            <div>
<?php
                foreach($tipos as $tipo => $nombre) {
                    echo "<input type='radio' name='tipo' id='$tipo' value='$tipo'>$nombre<br>";
                }
?>
            </div>

            <label for="marca">Marca</label>
            <select name="marca" id="marca" size="1">
                <option value="">Selecciona una marca</option>
<?php
                foreach($marcas as $marca => $nombre) {
                    echo "<option value='$marca'>$nombre</option>";
                }
?>
            </select>

            <label for="antiguedad">Antigüedad (años)</label>
            <input type="text" name="antiguedad" id="antiguedad" size="2">

            <label for="itv">ITV pasada</label>
            <input type="checkbox" name="itv" id="itv" checked>

            <label for="archivo">Archivo csv</label>
            <input type="file" name="archivo" id="archivo" accept="text/csv">
        </fieldset>
        <input type="submit" value="Enviar" name="operacion" id="operacion">
    </form>
<?php
}

function comprobar_archivo() {
    // Comprobamos la subida del archivo ya que si no existe no me deja enviar

    /*
        $_FILES['archivo']['tmp_name'];  // Ubicación del archivo en el servidor
        $_FILES['archivo']['name'];      // Nombre del archivo
        $_FILES['archivo']['size'];      // Tamaño
        $_FILES['archivo']['type'];      // Tipo mime
        $_FILES['archivo']['error'];     // Número de error
    */

    // Comprobamos la subida del archivo
    if($_FILES['archivo']['error'] == UPLOAD_ERR_FORM_SIZE) {
        return 1;
    }

    // Comprobamos el tipo mime
    $tipo_mime_validos = ["text/csv"];

    $tipo_mime_subido = $_FILES['archivo']['type'];

    $tipo_mime_archivo = mime_content_type($_FILES['archivo']['tmp_name']);

    $puntero_info = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_mime_info = finfo_file($puntero_info, $_FILES['archivo']['tmp_name']);

    if(!in_array($tipo_mime_subido, $tipo_mime_validos) || !in_array($tipo_mime_archivo, $tipo_mime_validos) || !in_array($tipo_mime_info, $tipo_mime_validos)) {
        return 2;
    }

    if($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        return 3;
    }

    return 0;
}

function sanear_y_validar() {
    global $tipos, $marcas;

    // En este punto el tipo mime es el esperado y tiene un tamaño dentro del límite
    // Saneamos los datos
    $filtros_saneamiento = [
        'email' => FILTER_SANITIZE_EMAIL,
        'tipo' => FILTER_SANITIZE_SPECIAL_CHARS,
        'marca' => FILTER_SANITIZE_SPECIAL_CHARS,
        'antiguedad' => FILTER_SANITIZE_NUMBER_INT,
        'itv' => FILTER_DEFAULT
    ];

    $datos_saneados = filter_input_array(INPUT_POST, $filtros_saneamiento);

    // Validamos los datos
    $filtros_validacion = [
        'email' => FILTER_VALIDATE_EMAIL,
        'tipo' => FILTER_DEFAULT,
        'marca' => FILTER_DEFAULT,
        'antiguedad' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
                'max_range' => 5
            ],
        ],
        'itv' => FILTER_VALIDATE_BOOL
    ];

    $datos_validados = filter_var_array($datos_saneados, $filtros_validacion);

    // Validamos la lógica de negocio
    if(!array_key_exists($datos_validados['tipo'], $tipos)) {
        $datos_validados['tipo'] = false;
    }

    if(!array_key_exists($datos_validados['marca'], $marcas)) {
        $datos_validados['marca'] = false;
    }

    // Si falta algun dato, se termina
    $array_filtrado = array_filter($datos_validados);

    if(count($array_filtrado) < 4 || count($array_filtrado) == 4 && $datos_validados['itv']) {
        return false;
    }
    else {
        return $datos_validados;
    }
}

function mostrar_resultados($datos_validados) {
    global $marcas, $tipos;

    // Abrimos el archivo y comprobamos los datos con los del archivo
    $archivo = fopen($_FILES['archivo']['tmp_name'], "r");
    $linea = fgetcsv($archivo);

    echo <<<TABLA
    <table>
        <thead>
            <tr>
                <th>{$linea[0]}</th>
                <th>{$linea[1]}</th>
                <th>{$linea[2]}</th>
                <th>{$linea[3]}</th>
            </tr>
        </thead>
        <tbody>
    TABLA;

    while($linea = fgetcsv($archivo)) {
        if($linea[0] == $tipos[$datos_validados['tipo']] && $linea[1] == $marcas[$datos_validados['marca']] && $linea[2] == $datos_validados['antiguedad'] && $linea[3] == ($datos_validados['itv'] ? "Si" : "No")) {
            echo <<<FILA
                <tr>
                    <td>$linea[0]</td>
                    <td>$linea[1]</td>
                    <td>$linea[2]</td>
                    <td>$linea[3]</td>
                </tr>
            FILA;
        }
    }

    echo "</tbody></table>";
}

if($_SERVER['REQUEST_METHOD'] == "GET") {
    muestra_formulario($marcas, $tipos);
} 

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $error = comprobar_archivo();

    $mensaje_error = match($error) {
        1 => "El archivo ha superado el tamaño máximo",
        2 => "El tipo mime tiene que ser csv",
        3 => "No hay archivo en la subida",
        default => "Error desconocido"
    };

    if($error) {
        echo "<h3>$mensaje_error</h3>";
        fin_html();
        exit($error);
    }

    $datos_validados = sanear_y_validar();

    if(!$datos_validados) {
        echo "<h3>Error. Los datos no están validados</h3>";
        exit(4);
    }

    mostrar_resultados($datos_validados);

    echo "<p><a href='{$_SERVER['PHP_SELF']}'>Volver al formulario de búsqueda</a></p>";
}

fin_html();
?>