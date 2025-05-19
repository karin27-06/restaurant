<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeType extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
    ]; 

    public function Employees(): HasMany {
        return $this->hasMany(Employee::class);
    }
}
