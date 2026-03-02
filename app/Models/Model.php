<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * صيغة التاريخ البسيطة عند إرسال النماذج إلى JSON (مثل Inertia).
     * تظهر التواريخ بشكل 2026-03-02 بدلاً من ISO 8601.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }
}
