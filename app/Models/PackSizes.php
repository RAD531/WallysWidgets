<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackSizes extends Model
{
    use HasFactory;

    //array of allowed properties for mass-assignment
    protected $fillable = ['packSize'];
}
