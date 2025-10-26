<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'educational_entity_id',
        'name',
        'position',
        'email',
        'phone',
        'mobile',
        'type',
        'is_primary',
        'notes',
        'status',
        'metadata',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'metadata' => 'array',
    ];

    // Relaciones
    public function educationalEntity(): BelongsTo
    {
        return $this->belongsTo(EducationalEntity::class, 'educational_entity_id');
    }

    // Scopes útiles
    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFullContactInfoAttribute()
    {
        $info = [$this->name];

        if ($this->position) {
            $info[] = $this->position;
        }

        return implode(' - ', $info);
    }

    public function getPreferredPhoneAttribute()
    {
        return $this->mobile ?: $this->phone;
    }

    // Mutators
    public function setIsPrimaryAttribute($value)
    {
        // Si se marca como primario, quitar el primario anterior
        if ($value && $this->educational_entity_id) {
            static::where('educational_entity_id', $this->educational_entity_id)
                 ->where('id', '!=', $this->id ?? 0)
                 ->update(['is_primary' => false]);
        }

        $this->attributes['is_primary'] = $value;
    }
}
