<?php
declare(strict_types=1);
session_start();
$dbDir = __DIR__ . '/data';
if (!is_dir($dbDir)) { mkdir($dbDir, 0777, true); }
$pdo = new PDO('sqlite:' . $dbDir . '/inventory.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec("CREATE TABLE IF NOT EXISTS products (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 code TEXT NOT NULL UNIQUE,
 name TEXT NOT NULL,
 category TEXT NOT NULL,
 unit TEXT NOT NULL,
 min_stock INTEGER NOT NULL DEFAULT 5,
 stock INTEGER NOT NULL DEFAULT 0,
 created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS stock_movements (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 product_id INTEGER NOT NULL,
 type TEXT NOT NULL CHECK(type IN ('IN','OUT')),
 qty INTEGER NOT NULL CHECK(qty > 0),
 note TEXT,
 movement_date TEXT NOT NULL,
 created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
)");
$count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
if ($count === 0) {
  $stmt = $pdo->prepare('INSERT INTO products(code,name,category,unit,min_stock,stock) VALUES(?,?,?,?,?,?)');
  $seed = [
    ['BRG-001','Kertas A4 80 GSM','ATK','Rim',10,42],
    ['BRG-002','Tinta Printer Hitam','Elektronik','Botol',5,12],
    ['BRG-003','Mouse Wireless','Elektronik','Unit',4,3],
    ['BRG-004','Pulpen Gel Hitam','ATK','Pcs',20,65],
    ['BRG-005','Kardus Packing M','Kemasan','Pcs',15,18]
  ];
  foreach ($seed as $row) { $stmt->execute($row); }
}
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function flash(string $type, string $message): void { $_SESSION['flash'] = compact('type','message'); }
function takeFlash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
