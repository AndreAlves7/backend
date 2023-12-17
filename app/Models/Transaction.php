<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vcard',
        'type',
        'value',
        'payment_type',
        'payment_reference',
        'pair_vcard',
        'category_id',
        'description',
    ];

    //assiciado(pt) -> (en) associated 
    // 1 transaction belongs to 1 card
    public function vcardAssociated()
    {
        return $this->belongsTo(Vcard::class, 'vcard', 'phone_number');
    }

    public function pairVcardAssociated()
    {
        return $this->belongsTo(Vcard::class, 'pair_vcard', 'phone_number');
    }


    // 1 transaction belongs to 1 category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
