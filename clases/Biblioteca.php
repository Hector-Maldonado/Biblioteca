<?php
class Biblioteca {
    private $libros = [];

    // Agregar libro
    public function agregarLibro($titulo, $autor, $categoria) {
        $id = count($this->libros) + 1; 
        $libro = new Libro($id, $titulo, $autor, $categoria);
        $this->libros[] = $libro;
    }

    // Obtener todos los libros
    public function obtenerLibros() {
        return $this->libros;
    }

    // Buscar libros por título, autor o categoría
    public function buscarLibro($termino) {
        $resultados = [];
        foreach ($this->libros as $libro) {
            if (stripos($libro->getTitulo(), $termino) !== false || 
                stripos($libro->getAutor(), $termino) !== false || 
                stripos($libro->getCategoria(), $termino) !== false) {
                $resultados[] = $libro;
            }
        }
        return $resultados;
    }

    // Editar libro
    public function editarLibro($id, $titulo, $autor, $categoria) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id) {
                $libro->setTitulo($titulo);
                $libro->setAutor($autor);
                $libro->setCategoria($categoria);
                return true;
            }
        }
        return false;
    }

    // Eliminar libro
    public function eliminarLibro($id) {
        foreach ($this->libros as $key => $libro) {
            if ($libro->getId() == $id) {
                unset($this->libros[$key]);
                return true;
            }
        }
        return false;
    }

    // Prestar libro
    public function prestarLibro($id) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id) {
                return $libro->prestar();
            }
        }
        return false;
    }

    // Devolver libro
    public function devolverLibro($id) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id) {
                $libro->devolver();
                return true;
            }
        }
        return false;
    }
}
?>