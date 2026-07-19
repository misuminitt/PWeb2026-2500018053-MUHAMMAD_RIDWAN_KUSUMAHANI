<?php
require_once __DIR__ . '/config.php';
function layoutStart(string $title, string $active = ''): void {
  $flash = takeFlash();
  $nav = ['dashboard'=>'index.php','barang'=>'products.php','transaksi'=>'movements.php','laporan'=>'report.php'];
  echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | GudangKu</title><link rel="stylesheet" href="assets/style.css"></head><body>';
  echo '<aside class="sidebar"><div class="brand"><span>GK</span><div><b>GudangKu</b><small>Inventory System</small></div></div><nav>';
  foreach ($nav as $key=>$url) { $labels=['dashboard'=>'Dashboard','barang'=>'Data Barang','transaksi'=>'Stok Masuk/Keluar','laporan'=>'Laporan']; echo '<a class="'.($active===$key?'active':'').'" href="'.$url.'">'.$labels[$key].'</a>'; }
  echo '</nav><div class="identity">Muhammad Ridwan Kusumahani<br><small>2500018053</small></div></aside><main class="main"><header><div><h1>'.e($title).'</h1><p>Sistem Pengelolaan Stok Barang Gudang Berbasis Web</p></div><div class="date">'.date('d M Y').'</div></header>';
  if ($flash) echo '<div class="alert '.e($flash['type']).'">'.e($flash['message']).'</div>';
}
function layoutEnd(): void { echo '</main><script src="assets/app.js"></script></body></html>'; }
function statCard(string $label, string $value, string $hint): string { return '<article class="stat"><small>'.$label.'</small><strong>'.$value.'</strong><span>'.$hint.'</span></article>'; }
