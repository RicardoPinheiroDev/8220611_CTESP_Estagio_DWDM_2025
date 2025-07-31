<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'status',
        'department_id',
    ];

    protected $casts = [
        'type' => \App\Enums\UserType::class,
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });

        static::deleting(function ($model) {
            if ($model->email) {
                \App\Models\User::where('email', $model->email)->delete();
            }
        });
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}