<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareApp extends Model
{
    protected $table = 'software_apps';

    protected $fillable = [
        'name',
        'version',
        'description',
        'developer',
        'link',
        'status',
        'icon',
        'platform'
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function isActive()
    {
        return $this->status === 'active';
    }
}
