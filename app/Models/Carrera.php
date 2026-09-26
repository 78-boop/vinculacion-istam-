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

    // Actividades (menú Actividades) en las que participa al menos un estudiante de esta carrera
    public function actividades()
    {
        $carreraId = $this->id;

        return Actividad::where(function ($query) use ($carreraId) {
            $query->whereHas('inscripciones.estudiante', fn ($q) => $q->where('carrera_id', $carreraId))
                ->orWhereHas('inscripcion.estudiante', fn ($q) => $q->where('carrera_id', $carreraId));
        });
    }
}