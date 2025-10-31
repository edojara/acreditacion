<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalEntity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'region',
        'country',
        'phone',
        'email',
        'website',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relaciones
    public function contacts(): HasMany
    {
        return $this->hasMany(EntityContact::class, 'educational_entity_id');
    }

    public function primaryContact()
    {
        return $this->hasOne(EntityContact::class, 'educational_entity_id')
                    ->where('is_primary', true);
    }

    // Scopes útiles

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->region, $this->country]);
        return implode(', ', $parts);
    }

    public function getContactCountAttribute()
    {
        return $this->contacts()->count();
    }
}
