<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadEstudiante extends Model
{
    protected $table = 'actividades_estudiante';

    protected $fillable = [
        'registro_hora_id',
        'inscripcion_id',
        'fecha',
        'actividad_realizada',
        'resultado',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Relaciones
    public function registroHora()
    {
        return $this->belongsTo(RegistroHora::class);
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }
}
