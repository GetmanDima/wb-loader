<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'date', 'lastChangeDate', 'supplierArticle',
        'techSize', 'barcode', 'totalPrice', 'discountPercent',
        'isSupply', 'isRealization', 'promoCodeDiscount',
        'warehouseName', 'countryName', 'oblastOkrugName', 'regionName',
        'incomeID', 'saleID', 'odid', 'spp',
        'forPay', 'finishedPrice', 'priceWithDisc',
        'nmId', 'subject', 'category',
        'brand', 'IsStorno', 'gNumber',
        'sticker', 'srid'
    ];
}
