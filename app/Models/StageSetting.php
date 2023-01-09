<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageSetting extends Model
{
    use HasFactory;

    public function Designation(){

        return $this->belongsTo(Designation::class);

    }
}
