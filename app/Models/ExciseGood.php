<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExciseGood extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'wb_id', 'inn', 'finishedPrice',
        'operationTypeId', 'fiscalDt', 'docNumber',
        'fnNumber', 'regNumber', 'excise',
        'date'
    ];
}
