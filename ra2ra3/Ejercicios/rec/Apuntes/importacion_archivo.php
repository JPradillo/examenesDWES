<?php
// Este documento contiene la manera de mostrar los datos de un archivo con PHP
// En el caso de ser CSV
require_once $_SERVER['DOCUMENT_ROOT'] . "/recuperacion/includes/funciones.php";

inicio_html("Importar archivo", ["/recuperacion/estilos/general.css", "/recuperacion/estilos/tablas.css", "/recuperacion/estilos/formulario.css"]);

echo "<header>Importación de datos</header>";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    // Vemos si existe el archivo y si se ha enviado
    if(isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        // Abrimos el archivo en modo lectura
        $archivo = fopen($_FILES['archivo']['tmp_name'], "r");

        echo "<table><caption>Importación de " . $_FILES['archivo']['name'] . "</caption><thead>";

        if($archivo) {
            // Leemos la primera fila si existe la cabecera
            $cabecera = fgetcsv($archivo);
            
            // Creamos la tabla
            echo "<tr>";
            foreach($cabecera as $columna) {
                echo "<th>$columna</th>";
            }
            echo "</tr>" . PHP_EOL;
            echo "</thead><tbody>";
            
            // Presentamos los datos
            $registros = 0;
            while($fila = fgetcsv($archivo)) {
                ++$registros;
                echo "<tbody>";
                echo "<tr>";
                foreach($fila as $campo) {
                    echo "<td>$campo</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            fclose($archivo);
        }
        echo "<p><a href='{$_SERVER['PHP_SELF']}'>Importar otro archivo</a></p>";
    } else {
        echo "<h3>ERROR. El archivo no se ha subido</h3>";
    } 
} else {
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" size="<?=1024 * 2?>">
    <fieldset>
        <legend>Introduzca el archivo con los datos a importar</legend>
        <label for="fila_cabecera">Fila de la cabecera</label>
        <input type="checkbox" name="fila_cabecera" id="fila_cabecera" checked>

        <label for="archivo">Archivo csv</label>
        <input type="file" name="archivo" id="archivo" accept="text/csv">
    </fieldset>
    <input type="submit" value="Inportar" name="operacion" id="op1">
</form>
<?php
}
fin_html();
?>
