<?php

namespace App\Tests\Entity\Client;

use App\Entity\Client\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetMinimumStockInKgWithMainUnit0(): void
    {
        $product = new Product();
        $product->setStock(10.0);
        $product->setMainUnit('0');
        
        // When main_unit is 0, stock is in kg
        $this->assertEquals(10.0, $product->getMinimumStockInKg());
    }

    public function testGetMinimumStockInKgWithMainUnit1(): void
    {
        $product = new Product();
        $product->setStock(55.0); // 55 units
        $product->setMainUnit('1');
        $product->setWeightUnit1(0.01054); // 0.01054 kg per unit
        
        // 55 units * 0.01054 kg/unit = 0.5797 kg
        $this->assertEquals(0.5797, $product->getMinimumStockInKg());
    }

    public function testGetMinimumStockInKgWithMainUnit2(): void
    {
        $product = new Product();
        $product->setStock(100.0); // 100 units
        $product->setMainUnit('2');
        $product->setWeightUnit2(0.5); // 0.5 kg per unit
        
        // 100 units * 0.5 kg/unit = 50 kg
        $this->assertEquals(50.0, $product->getMinimumStockInKg());
    }

    public function testGetMinimumStockInKgWithNullStock(): void
    {
        $product = new Product();
        $product->setMainUnit('1');
        $product->setWeightUnit1(0.01054);
        
        // Stock is null, should return null
        $this->assertNull($product->getMinimumStockInKg());
    }

    public function testGetConversionFactorWithMainUnit0(): void
    {
        $product = new Product();
        $product->setMainUnit('0');
        
        // When main_unit is 0, conversion factor is 1.0 (kg to kg)
        $this->assertEquals(1.0, $product->getConversionFactor());
    }

    public function testGetConversionFactorWithMainUnit1(): void
    {
        $product = new Product();
        $product->setMainUnit('1');
        $product->setWeightUnit1(0.01054);
        
        // Conversion factor should be the weight_unit1
        $this->assertEquals(0.01054, $product->getConversionFactor());
    }

    public function testGetConversionFactorWithMainUnit2(): void
    {
        $product = new Product();
        $product->setMainUnit('2');
        $product->setWeightUnit2(0.5);
        
        // Conversion factor should be the weight_unit2
        $this->assertEquals(0.5, $product->getConversionFactor());
    }
}
