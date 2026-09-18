<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable = ['nombre', 'imagen', 'activo', 'horas_requeridas'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function estudiantes()
    {
        return $this->hasMany(User::class, 'carrera_id');
    }

    public function actividadesVinculacion()
    {
        return $this->hasMany(ActividadVinculacion::class);
    }
}