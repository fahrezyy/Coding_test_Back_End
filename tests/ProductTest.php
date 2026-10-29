<?php

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Gunakan database SQLite in-memory khusus untuk testing agar tidak merusak database utama
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Buat tabel users dan products untuk simulasi
        $this->pdo->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL
        )");

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

    // 2. Test Fitur Tambah Produk (Create)
    public function testCreateProduct()
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, description, price, category, status) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute(['Laptop ASUS', 'Laptop gaming', 15000000, 'Elektronik', 'tersedia']);

        $this->assertTrue($result);

        // Verifikasi produk masuk ke database
        $product = $this->pdo->query("SELECT * FROM products WHERE name = 'Laptop ASUS'")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(15000000, $product['price']);
        $this->assertEquals('tersedia', $product['status']);
    }

    // 3. Test Fitur Baca & Cari Produk (Read & Search)
    public function testSearchProducts()
    {
        // Masukkan data dummy
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES 
            ('Keyboard Mechanical', 'RGB switch', 750000, 'Aksesoris', 'tersedia'),
            ('Gaming Mouse', 'Wireless', 350000, 'Aksesoris', 'habis')
        ");

        // Simulasi pencarian berdasarkan kategori
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute(['Aksesoris']);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $products);
    }

    // 4. Test Fitur Update Produk
    public function testUpdateProduct()
    {
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES ('Mouse Lama', 'Biasa', 100000, 'Aksesoris', 'tersedia')");
        
        // Update harga dan status
        $stmt = $this->pdo->prepare("UPDATE products SET price = ?, status = ? WHERE name = ?");
        $stmt->execute([120000, 'habis', 'Mouse Lama']);

        $product = $this->pdo->query("SELECT * FROM products WHERE name = 'Mouse Lama'")->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(120000, $product['price']);
        $this->assertEquals('habis', $product['status']);
    }

    // 5. Test Fitur Hapus Produk (Delete)
    public function testDeleteProduct()
    {
        $this->pdo->exec("INSERT INTO products (name, description, price, category, status) VALUES ('Produk Hapus', 'Test', 50000, 'Lainnya', 'tersedia')");

        $stmt = $this->pdo->prepare("DELETE FROM products WHERE name = ?");
        $stmt->execute(['Produk Hapus']);

        $count = $this->pdo->query("SELECT COUNT(*) FROM products WHERE name = 'Produk Hapus'")->fetchColumn();
        $this->assertEquals(0, $count);
    }
}