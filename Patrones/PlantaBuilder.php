<?php
namespace Patrones;

class Planta {
    public string $nombre;
    public string $descripcion;
    public string $clima;
    public ?string $imagen;

    public function __construct(string $nombre, string $descripcion, string $clima, ?string $imagen = null) {
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->clima = $clima;
        $this->imagen = $imagen;
    }
}

class PlantaBuilder {
    private string $nombre = "";
    private string $descripcion = "";
    private string $clima = "";
    private ?string $imagen = null;

    public function setNombre(string $nombre): self {
        $this->nombre = $nombre;
        return $this;
    }

    public function setDescripcion(string $descripcion): self {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function setClima(string $clima): self {
        $this->clima = $clima;
        return $this;
    }

    public function setImagen(?string $imagen): self {
        $this->imagen = $imagen;
        return $this;
    }

    public function build(): Planta {
        return new Planta($this->nombre, $this->descripcion, $this->clima, $this->imagen);
    }
}
?>
