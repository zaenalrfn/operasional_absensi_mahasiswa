<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'tanggal',
        'status',
        'method',
        'photo_capture',
        'verified'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'verified' => 'boolean'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
