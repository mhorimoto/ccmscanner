#! /usr/bin/php
<?php
require_once('CCMScanner.php');
$scanner = new CCMScanner();

// HTTP経由であることを意識せずに scan() を呼べる
$data = $scanner->scan(['ROOM' => 45 ]);

// 表示部分
foreach ($data as $n => $item) {
    $dateStr = date('Y-m-d H:i:s', $item['FTIME']); 
    echo "[$n] {$item['ROOM']}-{$item['REGION']}-{$item['ORDER']}-{$item['PRIORITY']}/{$item['CCMTYPE']}/{$item['VALUE']}/ {$dateStr}\n";
}
?>
