<?php // app/Models/TransportRoute.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model {
    use HasFactory;
    protected $fillable = ['school_id', 'name', 'description', 'driver_id', 'fee_amount', 'is_active'];
    protected $casts = ['fee_amount' => 'decimal:2', 'is_active' => 'boolean'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function driver(): BelongsTo { return $this->belongsTo(User::class, 'driver_id'); } // Assuming driver is a User
    public function studentAssignments(): HasMany { return $this->hasMany(StudentTransportAssignment::class); }
}