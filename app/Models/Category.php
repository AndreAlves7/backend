<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vcard',
        'type',
        'name'
    ];
    public $timestamps = false;

    // 1 category belongs to 1 card
    public function vcardAssociated()
    {
        return $this->belongsTo(Vcard::class, 'vcard', 'phone_number');
    }

    // 1 category has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
