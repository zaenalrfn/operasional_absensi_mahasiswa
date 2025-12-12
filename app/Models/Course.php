<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'jurusan',
        'dosen_id',
        'sks',
        'kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester'
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lectures::class, 'dosen_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendence::class);
    }
    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }
}
