<?php
namespace App\Product\Application\DTO;

class ProductDTO
{
    public string $uuid;
    public string $name;

    public function __construct(string $uuid, string $name)
    {
        $this->uuid = $uuid;
        $this->name = $name;
    }
}
