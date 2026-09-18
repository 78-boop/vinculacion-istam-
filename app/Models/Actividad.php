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
        'fecha',
        'lugar',
        'descripcion',
        'horas',
        'estado',
        'comentario_docente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas' => 'decimal:2',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}