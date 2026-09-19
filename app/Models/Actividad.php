<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'inscripcion_id',
        'proyecto_vinculacion_id',
        'docente_id',
        'fecha',
        'fecha_inicio',
        'fecha_finalizacion',
        'lugar',
        'descripcion',
        'horas',
        'estado',
        'comentario_docente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_inicio' => 'date',
        'fecha_finalizacion' => 'date',
        'horas' => 'decimal:2',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(ProyectoVinculacion::class, 'proyecto_vinculacion_id');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function inscripciones()
    {
        return $this->belongsToMany(Inscripcion::class, 'actividad_inscripcion');
    }
}