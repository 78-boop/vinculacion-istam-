<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadVinculacion extends Model
{
    protected $table = 'actividades_vinculacion';

    protected $fillable = ['carrera_id', 'creado_por', 'titulo', 'descripcion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function postulaciones()
    {
        return $this->hasMany(PostulacionActividad::class);
    }
}