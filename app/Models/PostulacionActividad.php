<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulacionActividad extends Model
{
    protected $table = 'postulaciones_actividad';

    protected $fillable = [
        'actividad_vinculacion_id',
        'inscripcion_id',
        'estado',
        'observaciones_docente',
        'aprobado_por',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
    ];

    public function actividad()
    {
        return $this->belongsTo(ActividadVinculacion::class, 'actividad_vinculacion_id');
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}