<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'lastChangeDate', 'supplierArticle',
        'techSize', 'barcode', 'totalPrice', 'discountPercent',
        'warehouseName', 'oblast', 'incomeID',
        'odid', 'nmId', 'subject', 'category',
        'brand', 'isCancel', 'cancel_dt', 'gNumber',
        'sticker', 'srid'
    ];
}
