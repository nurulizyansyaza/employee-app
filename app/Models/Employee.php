<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const CREATED_AT = 'entry_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id',
        'name',
        'birthdate',
        'sex',
        'address',
        'salary',
        'currency',
        'nik',
        'is_active',
        'entry_date',
        'update_date',
    ];

    protected $casts = [
        'birthdate'   => 'date',
        'sex'         => 'boolean',
        'is_active'   => 'boolean',
        'salary'      => 'decimal:4',
        'entry_date'  => 'datetime',
        'update_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            if (empty($employee->id)) {
                $employee->id = static::generateNextId();
            }
            $employee->entry_date  = $employee->entry_date ?? now();
            $employee->update_date = now();
        });

        static::updating(function (Employee $employee) {
            $employee->update_date = now();
        });
    }

    public static function generateNextId(): string
    {
        $prefix = now()->format('Ym');

        return DB::transaction(function () use ($prefix) {
            $last = static::query()
                ->where('id', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('id');

            $next = $last ? ((int) substr($last, 6)) + 1 : 1;

            return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
        });
    }
}
