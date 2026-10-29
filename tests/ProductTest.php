<?php

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Gunakan database SQLite in-memory khusus untuk testing agar aman
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Buat tabel users
        $this->pdo->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL
        )");

        // Buat tabel products
        $this->pdo->exec("CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            price REAL NOT NULL,
            category TEXT NOT NULL,
            status TEXT NOT NULL
        )");

        // Seed data user dummy
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin@test.com', $hashedPassword, 'admin']);
    }

    // 1. Test Fitur Autentikasi Login
    public function testUserLoginSuccess()
    {
        $email = 'admin@test.com';
        $password = 'password123';

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($user);
        $this->assertTrue(password_verify($password, $user['password']));
    }

    // 2. Test Fitur Tambah Produk Alat Musik (Create)
    public function testCreateProduct()
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category, status) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Yamaha PAC112VM RM', 'Gitar listrik yamaha', 3520000, 'Gitar Listrik', 'habis']);

        $this->assertTrue($result);

        // Verifikasi produk masuk ke database
        $product = $this->pdo->query("SELECT * FROM products WHERE name = 'Yamaha PAC112VM RM'")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(3520000, $product['price']);
        $this->assertEquals('Gitar Listrik', $product['category']);
        $this->assertEquals('habis', $product['status']);
    }

    // 3. Test Fitur Baca & Cari Produk Berdasarkan Kategori Alat Musik (Read & Search)
    public function testSearchProducts()
    {
        // Masukkan data dummy alat musik sesuai database Anda
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES 
            ('Yamaha PAC112VM RM', 'Gitar listrik yamaha', 3520000, 'Gitar Listrik', 'habis'),
            ('Gibson SG Tribute Vintage Cherry', 'Gitar listrik gibson original', 18300000, 'Gitar Listrik', 'tersedia'),
            ('Vox AC15C1 15W', 'Ampli gitar vox original', 11000000, 'Amplifier', 'tersedia')
        ");

        // Simulasi pencarian berdasarkan kategori
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute(['Gitar Listrik']);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $products);
    }

    // 4. Test Fitur Update Produk (Contoh: Ubah Status dan Harga Ampli/Gitar)
    public function testUpdateProduct()
    {
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES ('Yamaha GA-15II', 'Ampli gitar yamaha', 1200000, 'Amplifier', 'habis')");
        
        // Update harga dan status menjadi tersedia
        $stmt = $this->pdo->prepare("UPDATE products SET price = ?, status = ? WHERE name = ?");
        $stmt->execute([1250000, 'tersedia', 'Yamaha GA-15II']);

        $product = $this->pdo->query("SELECT * FROM products WHERE name = 'Yamaha GA-15II'")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1250000, $product['price']);
        $this->assertEquals('tersedia', $product['status']);
    }

    // 5. Test Fitur Hapus Produk (Delete)
    public function testDeleteProduct()
    {
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES ('Yamaha BB-434M Electric Bass', 'Bass yamaha original', 4800000, 'Bass', 'habis')");

        $stmt = $this->pdo->prepare("DELETE FROM products WHERE name = ?");
        $stmt->execute(['Yamaha BB-434M Electric Bass']);

        $count = $this->pdo->query("SELECT COUNT(*) FROM products WHERE name = 'Yamaha BB-434M Electric Bass'")->fetchColumn();
        $this->assertEquals(0, $count);
    }
}