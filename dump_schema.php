<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

function dumpTable($table) {
    echo "Table: $table\n";
    $columns = Schema::getColumnListing($table);
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    echo "\n";
}

dumpTable('transactions');
dumpTable('transaction_details');
dumpTable('cashier_items');
dumpTable('warehouse_items');
