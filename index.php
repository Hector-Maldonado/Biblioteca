<?php
// Se incluyen las clases
require_once 'clases/Libro.php';
require_once 'clases/Biblioteca.php';
// Iniciamos sesión para almacenar los datos
session_start();


// Creamos la biblioteca si no existe en la sesión
if (!isset($_SESSION['biblioteca']) || !is_string($_SESSION['biblioteca'])) {
    $_SESSION['biblioteca'] = serialize(new Biblioteca());
}

$biblioteca = unserialize($_SESSION['biblioteca']);

// Procesamos las solicitudes del usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Agregar libro
    if (isset($_POST['agregar'])) {
        if (isset($_POST['titulo'], $_POST['autor'], $_POST['categoria'])) {
            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $categoria = $_POST['categoria'];
            $biblioteca->agregarLibro($titulo, $autor, $categoria);
        }
       
        $_SESSION['biblioteca'] = serialize($biblioteca);
        header("Location: index.php");
        exit();
    }

    // Editar libro
    if (isset($_POST['editar'])) {
        if (isset($_POST['id'], $_POST['titulo'], $_POST['autor'], $_POST['categoria'])) {
            $id = $_POST['id'];
            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $categoria = $_POST['categoria'];
            $biblioteca->editarLibro($id, $titulo, $autor, $categoria);
        }
     
        $_SESSION['biblioteca'] = serialize($biblioteca);
        header("Location: index.php");
        exit();
    }

    // Eliminar libro
    if (isset($_POST['eliminar'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $biblioteca->eliminarLibro($id);
        }
       
        $_SESSION['biblioteca'] = serialize($biblioteca);
        header("Location: index.php");
        exit();
    }

    // Prestar libro
    if (isset($_POST['prestar'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $biblioteca->prestarLibro($id);
        }
      
        $_SESSION['biblioteca'] = serialize($biblioteca);
        header("Location: index.php");
        exit();
    }

    // Devolver libro
    if (isset($_POST['devolver'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $biblioteca->devolverLibro($id);
        }
        
        $_SESSION['biblioteca'] = serialize($biblioteca);
        header("Location: index.php");
        exit();
    }
}


// Buscar libros si se proporciona un término de búsqueda
$terminoBusqueda = isset($_POST['buscar']) ? $_POST['termino_busqueda'] : '';
$librosEncontrados = $terminoBusqueda ? $biblioteca->buscarLibro($terminoBusqueda) : $biblioteca->obtenerLibros();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Biblioteca</title>
    
        
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Biblioteca</h2>

        <!-- Formulario para agregar un libro -->
        <form action="index.php" method="POST" class="mb-4">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" class="form-control" required>
            </div>
            <br>
            <button type="submit" name="agregar" class="btn btn-primary">Agregar Libro</button>
        </form>

        <!-- Búsqueda de libros -->
        <form action="index.php" method="POST" class="mb-4">
            <div class="form-group">
                <label for="termino_busqueda">Buscar libros:</label>
                <input type="text" id="termino_busqueda" name="termino_busqueda" class="form-control" value="<?= $terminoBusqueda ?>">
            </div>
            <br>    
            <button type="submit" name="buscar" class="btn btn-info">Buscar</button>
        </form>

        <?php if (empty($librosEncontrados) && $terminoBusqueda): ?>
            <div class="alert alert-warning" role="alert">
                No se encontraron libros con el término de búsqueda "<strong><?= $terminoBusqueda ?></strong>".
            </div>
        <?php endif; ?>


        <h3>Lista de Libros</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Disponibilidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($librosEncontrados as $libro): ?>
                    <tr>
                        <td><?= $libro->getTitulo() ?></td>
                        <td><?= $libro->getAutor() ?></td>
                        <td><?= $libro->getCategoria() ?></td>
                        <td><?= $libro->getDisponible() ? 'Disponible' : 'Prestado' ?></td>
                        <td>
                          
                            <form action="index.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                                
                             
                                <button type="submit" name="prestar" class="btn btn-success btn-sm" 
                                    <?= !$libro->getDisponible() ? 'disabled' : '' ?>>
                                    Prestar
                                </button>
                                
                               
                                <button type="submit" name="devolver" class="btn btn-warning btn-sm" 
                                    <?= $libro->getDisponible() ? 'disabled' : '' ?>>
                                    Devolver
                                </button>

                                
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>

                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal<?= $libro->getId() ?>">Editar</button>
                            
                            <div class="modal fade" id="editModal<?= $libro->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Editar Libro</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="index.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $libro->getId() ?>">
                                                <div class="form-group">
                                                    <label for="titulo">Título:</label>
                                                    <input type="text" id="titulo" name="titulo" class="form-control" value="<?= $libro->getTitulo() ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="autor">Autor:</label>
                                                    <input type="text" id="autor" name="autor" class="form-control" value="<?= $libro->getAutor() ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="categoria">Categoría:</label>
                                                    <input type="text" id="categoria" name="categoria" class="form-control" value="<?= $libro->getCategoria() ?>" required>
                                                </div>
                                                <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>