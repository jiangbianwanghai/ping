<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    public $timestamps = true;
    protected $dateFormat = 'U';
}
