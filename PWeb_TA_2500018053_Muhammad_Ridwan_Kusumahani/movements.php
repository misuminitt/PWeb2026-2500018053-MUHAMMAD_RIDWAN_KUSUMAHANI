<?php
require 'functions.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $pid=(int)($_POST['product_id']??0);$type=$_POST['type']??'';$qty=(int)($_POST['qty']??0);$date=$_POST['movement_date']??date('Y-m-d');$note=trim($_POST['note']??'');
 $s=$pdo->prepare('SELECT * FROM products WHERE id=?');$s->execute([$pid]);$p=$s->fetch();
 if(!$p||!in_array($type,['IN','OUT'],true)||$qty<=0){flash('error','Data transaksi tidak valid.');}
 elseif($type==='OUT' && $qty>$p['stock']){flash('error','Stok tidak mencukupi. Stok tersedia: '.$p['stock']);}
 else{$pdo->beginTransaction();try{$pdo->prepare('INSERT INTO stock_movements(product_id,type,qty,note,movement_date) VALUES(?,?,?,?,?)')->execute([$pid,$type,$qty,$note,$date]);$delta=$type==='IN'?$qty:-$qty;$pdo->prepare('UPDATE products SET stock=stock+? WHERE id=?')->execute([$delta,$pid]);$pdo->commit();flash('success','Transaksi stok berhasil disimpan.');}catch(Throwable $e){$pdo->rollBack();flash('error','Transaksi gagal disimpan.');}}
 header('Location: movements.php');exit;
}
$products=$pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
$moves=$pdo->query('SELECT m.*,p.code,p.name,p.unit FROM stock_movements m JOIN products p ON p.id=m.product_id ORDER BY m.id DESC LIMIT 30')->fetchAll();
layoutStart('Stok Masuk & Keluar','transaksi');
echo '<section class="grid form-list"><div class="card"><div class="card-head"><h2>Catat Transaksi</h2></div><form method="post" class="form"><label>Barang<select name="product_id" required><option value="">Pilih barang</option>';foreach($products as $p)echo '<option value="'.$p['id'].'">'.e($p['code'].' - '.$p['name'].' (stok '.$p['stock'].')').'</option>';echo '</select></label><div class="form-row"><label>Jenis<select name="type"><option value="IN">Stok Masuk</option><option value="OUT">Stok Keluar</option></select></label><label>Jumlah<input type="number" name="qty" min="1" required></label></div><label>Tanggal<input type="date" name="movement_date" value="'.date('Y-m-d').'" required></label><label>Keterangan<textarea name="note" rows="3" placeholder="Contoh: Pembelian dari pemasok"></textarea></label><button class="btn primary">Simpan Transaksi</button></form></div><div class="card"><div class="card-head"><h2>Riwayat Transaksi</h2></div><div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Barang</th><th>Jenis</th><th>Jumlah</th><th>Keterangan</th></tr></thead><tbody>';
foreach($moves as $m)echo '<tr><td>'.e($m['movement_date']).'</td><td><b>'.e($m['name']).'</b><small class="block">'.e($m['code']).'</small></td><td><span class="badge '.($m['type']==='IN'?'success':'warning').'">'.($m['type']==='IN'?'Masuk':'Keluar').'</span></td><td>'.($m['type']==='IN'?'+':'-').$m['qty'].' '.e($m['unit']).'</td><td>'.e($m['note']?:'-').'</td></tr>';
if(!$moves)echo '<tr><td colspan="5" class="empty">Belum ada transaksi.</td></tr>';echo '</tbody></table></div></div></section>';layoutEnd();
