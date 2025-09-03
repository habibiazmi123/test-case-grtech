<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_id',
        'email',
        'phone'
    ];

    public function fullName(): Attribute
    {
        return Attribute::get(
            fn() => trim("{$this->first_name} {$this->last_name}")
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
