<?php
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase {
    public function testKalkulasiHargaAtauLogikaSederhana() {
        $harga = 150000;
        $diskon = 10000;
        $hasil = $harga - $diskon;
        
        $this->assertEquals(140000, $hasil);
    }
}