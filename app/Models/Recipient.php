<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bamboo_id',
        'first_name',
        'last_name',
        'email',
        'department',
        'position',
        'location',
        'supervisor_id',
        'avatar_url',
    ];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class)->withPivot('opened_at');
    }
}
