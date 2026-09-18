<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $fillable = [
        'registro_hora_id',
        'actividad_estudiante_id',
        'tipo_archivo',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño_bytes'
    ];

    // Relaciones
    public function registroHora()
    {
        return $this->belongsTo(RegistroHora::class, 'registro_hora_id');
    }

    public function actividadEstudiante()
    {
        return $this->belongsTo(ActividadEstudiante::class, 'actividad_estudiante_id');
    }
}
