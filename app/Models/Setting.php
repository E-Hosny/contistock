<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class Setting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
    ];
}
