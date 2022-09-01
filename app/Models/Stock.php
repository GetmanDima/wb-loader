<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Stock extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'lastChangeDate', 'supplierArticle', 'techSize',
        'barcode', 'quantity', 'isSupply', 'isRealization',
        'quantityFull', 'quantityNotInOrders', 'warehouseName',
        'inWayToClient', 'inWayFromClient', 'nmId', 'subject',
        'category', 'daysOnSite', 'brand', 'SCCode',
        'warehouse', 'Price', 'Discount'
    ];

//    protected function getDateFormat(): string
//    {
//        return 'Y-m-d H:i:s.u';
//    }

//    public function getLastChangeDate(string $value): \Carbon\Carbon|bool
//    {
//        return Carbon::createFromFormat('Y-m-d H:i:s.v', $value);
//    }
//
//    public function setLastChangeDate(Carbon $value): void
//    {
//        $this->attributes['lastChangeDate'] = $value->format('Y-m-d H:i:s.v');
//    }
}
