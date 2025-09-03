<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'logo',
        'website'
    ];

    public function logoUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->logo && Storage::disk('public')->exists($this->logo)) {
                return Storage::url($this->logo);
            }

            return asset('assets/no-image.png');
        });
    }
}
