<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vcard extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'phone_number',
        'name',
        'email',
        'photo_url',
        // 'password',
        'confirmation_code',
    ];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'phone_number';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';


    // 1 card has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'vcard', 'phone_number');
    }

    public function pairTransactions()
    {
        return $this->hasMany(Transaction::class, 'pair_vcard', 'phone_number');
    }

    // 1 card has many categories
    public function categories()
    {
        return $this->hasMany(Category::class, 'vcard', 'phone_number');
    }
}
