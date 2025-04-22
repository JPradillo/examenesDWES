# APUNTES RA3
## Validación de arrays
Para poder validar que un conjunto de datos están dentro de otros, simplemente hay que crear una variable `todo_ok` que se inicialice a True. En el caso de que haya alguno que no esté en el array, cambiar el valor de la variable a False y de esta manera, si al final la variable es False, lanzar un error.

Por ejemplo supongamos que hay un conjunto de 4 cursos de informática, en los cuales están Ofimática, Programación, Sistemas y Base de datos y un `select` que pueda contener varios cursos a la vez. Para hacer la validación sería algo tal que así:

```php
<?php
<?php
$cursos = [
    'of' => ['name' => "Ofimática", "precio" => 100],
    'pr' => ['name' => "Programación", "precio" => 150],
    'si' => ['name' => "Sistemas", "precio" => 200],
    'bd' => ['name' => "Base de datos", "precio" => 250],
];

$cursos_seleccionados = filter_input(INPUT_POST, 'cursos' ,  FILTER_SANITIZE_SPECIAL_CHARS,
FILTER_REQUIRE_ARRAY);

// Comprobamos que el curso está en los cursos válidos
$curso_ok = True;
if($curso_ok) {
    foreach($cursos_seleccionados as $curso) {
        if(!in_array($curso, $cursos)) {
            $curso_ok = False;
            break;
        }
        $curso_ok = True;
    }
}
?>
<form action="" method="POST">
    <fieldset>
        <legend>Selección múltiple</legend>
        <label for="cursos">Cursos</label>
        <select name="cursos[]" multiple size="4">
<?php
        foreach($cursos as $curso => $valor) {
            echo "<option value='$curso'>{$valor['name']} - {$valor['precio']}</option>";
        }
?>
        </select>
    </fieldset>
</form>
```

## Lectura de archivos

### Errores

| Código de Error         | Descripción                                                   |
|-------------------------|---------------------------------------------------------------|
| `UPLOAD_ERR_INI_SIZE`   | El archivo excede lo indicado por `upload_max_filesize`       |
| `UPLOAD_ERR_FORM_SIZE`  | El archivo excede el tamaño indicado en `MAX_FILE_SIZE`       |
| `UPLOAD_ERR_OK`         | El archivo es correcto                                        |
| `UPLOAD_ERR_NO_FILE`    | No se ha subido ningún archivo                                |

### Comparaciones para validación

1. Array con los tipos permitidos
2. Cogemos el tipo mime del archivo
3. Abrimos el fileinfo en modo tipo MIME
4. Obtenemos el tipo del archivo
5. Comparamos los datos obtenidos

```php
define("DIRECTORIO", $_SERVER['DOCUMENT_ROOT'])
$tipos_permitidos = ["application/pdf"];
$tipo_mime1 = mime_content_type($_FILES['archivo']['tmp_name']);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$tipo_mime2 = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
$tipo_mime3 = $_FILES['archivo']['type'];

if($tipo_mime1 == $tipo_mime2 && $tipo_mime2 == $tipo_mime3 && in_array($tipo_mime1, $tipos_permitidos)) {
    // code ...   
}
```

### Guardamos el archivo
En el caso de haber conseguido pasar todos los test del tipo mime, lo que toca ahora es guardar los datos con un nombre específico

```php
if($tipo_mime1 == $tipo_mime2 && $tipo_mime2 == $tipo_mime3 && in_array($tipo_mime1, $tipos_permitidos)) {
    // El tipo es permitido y se han pasado todas las comprobaciones
    // Guardamos el archivo
    $nombre = DIRECTORIO . "/" . $_FILES['archivo']['name'];
    
    if(move_uploaded_file($_FILES['archivo']['tmp_name'])) {
        echo "<h3>El archivo se ha podido subir sin complicaciones</h3>";
    } else {
        echo "<h3>Error al subir el archivo</h3>";
    }
}
```

### Archivos CSV
Para leer un archivo csv existen los siguientes métodos en PHP.

#### fopen
Abre el archivo pasado como parámetro de la forma que se indica en el segundo parámetro. Por ejemplo para abrir un archivo llamado `archivo` simplemente hay que:

```php
$archivo = fopen($_FILES['archivo']['tmp_name'], "r");
```

#### fgetcsv
Recibe como parámetro el archivo recibido y lo que hace es leer una línea completa del archivo

#### fclose
Cierra el archivo que recibe como parámetro

Para comprobar que a traves de un formulario, se muestren los datos que coincidan con los campos seleccionados simplemente hay que comparar la fila que contenga todos los datos con la fila que contenga el archivo csv. Es decir, para hacerlo por ejemplo con el archivo que contiene el archivo `vehículos.csv` simplemente hay que comparar los campos que hemos recibido con cada una de las líneas.