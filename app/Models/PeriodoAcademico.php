<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoAcademico extends Model
{
    protected $table = 'periodo_academicos';
    
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];
    
    public function proyectos()
    {
        return $this->hasMany(ProyectoVinculacion::class);
    }
    
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}