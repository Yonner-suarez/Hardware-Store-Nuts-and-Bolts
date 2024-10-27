
<?php
class Tools
{
    public $id;
    public $name;
    public $price;
    public $stock;
    public $image;
    public $marca;
    public $puntuacion;
    public $code;

    public function __construct($id, $name, $price, $stock, $image, $marca, $puntuacion, $code)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->marca = $marca;
        $this->puntuacion = $puntuacion;
        $this->code = $code;
    }
}

