<?php
require 'functions.php';
$totalItems = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalStock = (int)$pdo->query('SELECT COALESCE(SUM(stock),0) FROM products')->fetchColumn();
$lowStock = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE stock <= min_stock')->fetchColumn();
$todayMoves = (int)$pdo->query("SELECT COUNT(*) FROM stock_movements WHERE movement_date = date('now','localtime')")->fetchColumn();
$low = $pdo->query('SELECT * FROM products WHERE stock <= min_stock ORDER BY stock ASC LIMIT 6')->fetchAll();
$recent = $pdo->query('SELECT m.*, p.code, p.name FROM stock_movements m JOIN products p ON p.id=m.product_id ORDER BY m.id DESC LIMIT 7')->fetchAll();
layoutStart('Dashboard','dashboard');
echo '<section class="stats">'.statCard('Jenis Barang',(string)$totalItems,'Produk terdaftar').statCard('Total Stok',(string)$totalStock,'Seluruh unit tersimpan').statCard('Stok Menipis',(string)$lowStock,'Perlu segera ditambah').statCard('Transaksi Hari Ini',(string)$todayMoves,'Aktivitas stok').'</section>';
echo '<section class="grid two"><div class="card"><div class="card-head"><h2>Stok Menipis</h2><a href="products.php">Lihat semua</a></div><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Barang</th><th>Stok</th><th>Status</th></tr></thead><tbody>';
if (!$low) echo '<tr><td colspan="4" class="empty">Semua stok aman.</td></tr>';
foreach($low as $p) echo '<tr><td>'.e($p['code']).'</td><td>'.e($p['name']).'</td><td>'.$p['stock'].' '.e($p['unit']).'</td><td><span class="badge danger">Menipis</span></td></tr>';
echo '</tbody></table></div></div><div class="card"><div class="card-head"><h2>Aktivitas Terbaru</h2><a href="movements.php">Tambah transaksi</a></div><div class="activity">';
if (!$recent) echo '<p class="empty">Belum ada transaksi.</p>';
foreach($recent as $m) echo '<div class="activity-item"><span class="dot '.strtolower($m['type']).'"></span><div><b>'.e($m['name']).'</b><small>'.e($m['code']).' · '.e($m['movement_date']).'</small></div><strong class="'.strtolower($m['type']).'">'.($m['type']==='IN'?'+':'-').$m['qty'].'</strong></div>';
echo '</div></div></section>'; layoutEnd();
