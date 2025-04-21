<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/includes/funciones.php";

inicio_html("Examen de recuperación con archivo", ["/recuperacion/estilos/general.css", "/recuperacion/estilos/tablas.css", "/recuperacion/estilos/formulario.css"]);

define("DIRECTORIO_FINAL", $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/");

$cursos = [
    'of' => ['nombre' => 'Ofimática', 'precio' => 100], 
    'pr' => ['nombre' => 'Programación', 'precio' => 200], 
    'ro' => ['nombre' => 'Reparación de ordenadores', 'precio' => 150]
];

echo "<header>Solicitud demandante de empleo</header>";

function mostrar_formulario( array $cursos ) {
?>
    <form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=1024 * 100?>">
        <fieldset>
            <legend>Datos de la solicitud</legend>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" size="25">

            <label for="cursos">Cursos</label>
            <select name="cursos[]" multiple size="3">
    <?php
            foreach($cursos as $clave => $valor) {
                echo "<option value='$clave'>{$valor['nombre']} - {$valor['precio']}</option>";
            }
    ?>
            </select>

            <label for="clases">Nº clases presenciales</label>
            <textarea name="clases" id="clases" cols="20" rows="3"></textarea>

            <label for="desempleo">Situación de desempleo</label>
            <input type="checkbox" name="desempleo" id="desempleo" checked>

            <label for="archivo">Tarjeta demandante de empleo</label>
            <input type="file" name="archivo" id="archivo" accept="application/pdf">
        </fieldset>
        <input type="submit" value="Enviar solicitud" name="operacion" id="operacion">
    </form>
<?php
}

function sanear_validar( array $cursos ) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    $cursos_obtenidos = filter_input(INPUT_POST, "cursos", FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
    foreach($cursos_obtenidos as $curso) {
        if(!array_key_exists($curso, $cursos)) {
            echo "<h3>Error. Algún curso seleccionado no es válido</h3>";
            mostrar_enlace();
            break;
        }
    }

    $clases = filter_input(INPUT_POST, "clases", FILTER_SANITIZE_NUMBER_INT);
    $clases = filter_var($clases, FILTER_VALIDATE_INT, [
        'min_range' => 5,
        'max_range' => 10
    ]);

    $clases_validas = true;
    if($clases > 10 || $clases < 5) {
        $clases_validas = false;
    }

    if(!$email || !$cursos_obtenidos || !$clases_validas) {
        echo "<h3>Error. No has pasado la validación</h3>";
        mostrar_enlace();
        exit(1);
    }

    $desempleo = filter_input(INPUT_POST, "desempleo", FILTER_DEFAULT);
    $desempleo = filter_var($desempleo, FILTER_VALIDATE_BOOL);

    if($desempleo) {
        if($_FILES['archivo']['error'] == UPLOAD_ERR_OK){
            // Comprobación de los datos enviados como archivo
            $tipos_permitidos = ['application/pdf'];
            $tipo_mime1 = mime_content_type($_FILES['archivo']['tmp_name']);
            $tipo_mime2 = $_FILES['archivo']['type'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipo_mime3 = finfo_file($finfo, $_FILES['archivo']['tmp_name']);

            if($tipo_mime1 === $tipo_mime2 && $tipo_mime2 == $tipo_mime3 && in_array($tipo_mime2, $tipos_permitidos)) {
                // Es válido y probamos a crear el archivo
                $path = DIRECTORIO_FINAL . "ra2ra3/Ejercicios/examen24/tarjetas/";
                if(!is_dir($path) && !file_exists($path)) {
                    if(mkdir($path, 0755, true)) {
                        $from = $_FILES['archivo']['tmp_name'];
                        $to = $path . "{$email}.pdf";
                        if(move_uploaded_file($from, $to)) {
                            echo "<h4>El archivo se subió correctamente</h4>";
                            $nombre_archivo = basename($to);
                            echo <<<DATOS
                                <h3>Datos de la solicitud</h3>
                                <p><strong>Email:</strong> {$email}</p>
                                <p><strong>Nombre del archivo del usuario:</strong> {$_FILES['archivo']['name']}</p>
                                <p><strong>Nombre del archivo subido:</strong> {$nombre_archivo}</p>
                                <p><strong>Tamaño:</strong> {$_FILES['archivo']['size']}</p>
                            DATOS; 
                        } else {
                            echo "<h3>No se ha podido guardar el archivo.</h3>";
                            mostrar_enlace();
                            exit(2);
                        }
                    } else {
                        echo "<h3>No se ha podido crear la carpeta de archivos</h3>";
                        mostrar_enlace();
                        exit(3);
                    }
                } else {
                    echo "<h3>Problemas con el archivo o directorio</h3>";
                    mostrar_enlace();
                    exit(4);
                }
            } else {
                echo "<h3>Error en el archivo enviado</h3>";
                mostrar_enlace();
                exit(5);
            }
        } elseif($_FILES['archivo']['error'] == UPLOAD_ERR_FORM_SIZE) {
            echo "<h3>El archivo enviado supera el tamaño indicado por la directiva MAX_FILE_SIZE</h3>";
            mostrar_enlace();
            exit(6);
        } elseif($_FILES['archivo']['error'] == UPLOAD_ERR_NO_FILE) {
            echo "<h3>Error. No se ha subido ningún fichero</h3>";
            mostrar_enlace();
            exit(7);
        }
    }

    $datos_validados = [
        'email' => $email,
        'cursos_obtenidos' => $cursos_obtenidos,
        'clases' => $clases,
        'desempleo' => $desempleo
    ];

    return $datos_validados;
}

function mostrar_enlace() {
    echo "<p><a href='{$_SERVER['PHP_SELF']}'>Volver a intentarlo</a></p>";
}

function mostrar_resultados() {
    global $cursos;
    $datos_validados = sanear_validar($cursos);
    echo <<<TABLE
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Cursos</th>
                    <th>Clases presenciales</th>
                    <th>Desempleo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{$datos_validados['email']}</td>
                    <td>
    TABLE;

    foreach($datos_validados['cursos_obtenidos'] as $curso) {
        echo "{$cursos[$curso]['nombre']}<br>";
    }
    $desempleado = $datos_validados['desempleo'] ? "Sí" : "No";

    echo <<<TABLE2
                    </td>
                    <td>{$datos_validados['clases']}</td>
                    <td>$desempleado</td>
                </tr>
            </tbody>
        </table><br>
    TABLE2;
    crear_presupuesto($datos_validados['cursos_obtenidos'], $datos_validados['clases'], $datos_validados['desempleo']);

    mostrar_formulario($cursos);
}

function crear_presupuesto($cursos_obtenidos, $clases, $desempleo) {
    global $cursos;
    $total = 0;
    foreach($cursos_obtenidos as $curso) {
        $c = $cursos[$curso];
        echo "<p><strong>{$c['nombre']}</strong>: {$c['precio']}€</p>"; 
        $total += $c['precio'];
    }
    
    $precio_clases = $clases * 10;
    $total += $precio_clases;

    if($desempleo) {
        echo "<p>Al estar desempleado tienes un 10% de descuento</p>";
        $descuento = 0.1;
        $total *= $descuento;
    }

    echo "<h3>El presupuesto final es de {$total}€</h3>";
    PHP_EOL;
}

if($_SERVER['REQUEST_METHOD'] == "GET") {
    mostrar_formulario($cursos);
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    global $cursos;
    mostrar_resultados($cursos);
}

fin_html();
?>