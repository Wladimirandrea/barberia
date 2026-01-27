<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaysOff extends Model
{
    protected $table = 'days_offs'; 
    protected $fillable = ['user_id','monday','tuesday','wednesday','thursday','friday','saturday','sunday']; 
    public $timestamps = true;

    protected $casts = [ 'monday' => 'boolean', 'tuesday' => 'boolean', 'wednesday' => 'boolean', 'thursday' => 'boolean', 'friday' => 'boolean', 'saturday' => 'boolean', 'sunday' => 'boolean', ];
}
