<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroHora extends Model
{
    protected $table = 'registro_horas';

    protected $fillable = [
        'inscripcion_id',
        'fecha',
        'horas_registradas',
        'descripcion',
        'estado',
        'observaciones',
        'aprobado_por'
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas_registradas' => 'decimal:1',
    ];

    // Relaciones
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function actividades()
    {
        return $this->hasMany(ActividadEstudiante::class);
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function observacionesDocente()
    {
        return $this->hasMany(ObservacionDocente::class);
    }
}
