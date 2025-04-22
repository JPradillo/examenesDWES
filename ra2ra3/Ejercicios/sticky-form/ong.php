<?php
// Al ser autogenerada, siempre hay que presentar el formulario
// Si es sticky form hay que meterle todos los datos con values y con checked si es un checkbox o selected si es un select

require_once $_SERVER['DOCUMENT_ROOT'] . '/recuperacion/includes/funciones.php';

$proyectos = [
    "ap" => "Agua potable",
    "ep" => "Escuela de primaria",
    "ps" => "Placas solares",
    "cm" => "Centro médico"
];

inicio_html("Actividad 6 - ONGs", ["/recuperacion/estilos/general.css", "/recuperacion/estilos/formulario.css", "/recuperacion/estilos/tablas.css"]);

// Antes del formulario, comprobamos si hay una petición para recoger los datos
if($_SERVER['REQUEST_METHOD'] == "POST") {
    // Sanear los datos
    // Utilizamos la misma clave que hemos puesto en el atributo name en el campo del formulario
    $filtro_sanear = [
        'email' => FILTER_SANITIZE_EMAIL,
        'registro' => FILTER_DEFAULT,
        'cantidad' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        ],
        'proyecto' => FILTER_SANITIZE_SPECIAL_CHARS,
        'propuesta' => FILTER_SANITIZE_SPECIAL_CHARS
    ];

    $datos_saneados = filter_input_array(INPUT_POST, $filtro_sanear);

    $filtros_validacion = [
        'email' => FILTER_VALIDATE_EMAIL,
        'registro' => FILTER_VALIDATE_BOOL, 
        'cantidad' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'option' => [
                'min_range' => 10,
                'default' => 20
            ],
        ],
        'proyecto' => FILTER_DEFAULT,
        'propuesta' => FILTER_DEFAULT
    ];

    $datos_validados = filter_var_array($datos_saneados, $filtros_validacion);

    $mensajes = [];

    if(!$datos_validados['email']) $mensajes['email'] = "El email no es correcto";
    if(!$datos_validados['cantidad']) $mensajes['cantidad'] = "La cantidad no es correcta";

    if(isset($datos_validados['proyecto']) && $datos_validados['proyecto'] !== "" && !array_key_exists($datos_validados['proyecto'], $proyectos)) {
        // El proyecto no es válido
        $mensajes['proyecto'] = "El proyecto no es válido";
    }

    if($datos_validados['proyecto'] == "" && $datos_validados['propuesta'] == "") {
        // Los datos son erróneos
        $mensajes['propuesta'] = "Hay que elegir un proyecto si no haces una propuesta";
    }

    // Visualizamos los datos del formulario
    // Si el array de mensajes está vacío se visualizan los datos
    if(count($mensajes) == 0) {
        // No hay errores de validación. Se visualizan los datos
        $registro = $datos_validados['registro'] ? "Sí" : "No";
        $proyecto = (isset($datos_validados['proyecto']) && array_key_exists($datos_validados['proyecto'], $proyectos)) ? $proyectos[$datos_validados['proyecto']] : "Se incluye una propuesta";

        echo <<<DATOS
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Registro</th>
                    <th>Cantidad</th>
                    <th>Proyecto</th>
                    <th>Propuesta</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{$datos_validados['email']}</td>
                    <td>$registro</td>
                    <td>{$datos_validados['cantidad']}</td>
                    <td>{$proyecto}</td>
                    <td>{$datos_validados['propuesta']}</td>
                </tr>
            </tbody>
        </table>
        DATOS;
    } else {
        // Hay errores de validación. Visualización de los mensajes de error
        echo "<p>";
        foreach($mensajes as $mensaje) {
            echo "mensaje: $mensaje";
        }
        echo "</p>";
    }
} 
?>
<header>ONG</header>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
    <fieldset>
        <legend>Datos de la suscripción</legend>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" size="25" required value="<?=isset($datos_validados['email']) ? $datos_validados['email'] : ""?>">

        <!-- A los booleanos hay que meterle un valor que puede ser True, 1, ... -->
        <label for="registro">Autoriza registro</label>
        <input type="checkbox" name="registro" id="registro" <?=(isset($datos_validados['registro']) && $datos_validados['registro']) ? "checked" : ""?>>

        <label for="cantidad">Cantidad</label>
        <input type="text" name="cantidad" id="cantidad" required size="5" value="<?=isset($datos_validados['cantidad']) ? $datos_validados['cantidad'] : ""?>">

        <label for="proyecto">Proyecto</label>
        <select name="proyecto" id="proyecto" size="1">
            <option value="">Selecciona una opción</option>
<?php
            foreach($proyectos as $clave => $valor) {
                $selected = (isset($datos_validados['proyecto']) && $datos_validados['proyecto'] == $clave) ? "selected" : "";
                echo "<option value='$clave' $selected>$valor</option>";
            }
?>
        </select>

        <label for="propuesta">Propuesta</label>
        <textarea name="propuesta" id="propuesta" cols="30" rows="3"><?= isset($datos_validados['propuesta']) ? $datos_validados['propuesta'] : "Escribe tu propuesta"?></textarea>
    </fieldset>

    <input type="submit" value="Enviar" name="operacion" id="operacion">
</form>
<?php
fin_html();