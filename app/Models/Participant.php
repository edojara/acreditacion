<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'educational_entity_id',
        'full_name',
        'phone',
        'position',
        'registration_date',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    // Relaciones
    public function educationalEntity(): BelongsTo
    {
        return $this->belongsTo(EducationalEntity::class, 'educational_entity_id');
    }

    // Scopes útiles
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('educational_entity_id', $entityId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Accessors
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) return null;

        // Formatear número chileno
        $phone = preg_replace('/\D/', '', $this->phone);

        if (strlen($phone) === 9) {
            return '+56 9 ' . substr($phone, 0, 4) . ' ' . substr($phone, 4);
        }

        return $this->phone;
    }
}
