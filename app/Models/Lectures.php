<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lectures extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];
    public function courses()
    {
        return $this->hasMany(Course::class, 'dosen_id');
    }
}
