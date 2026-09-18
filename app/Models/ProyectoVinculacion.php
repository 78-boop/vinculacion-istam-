<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoVinculacion extends Model
{
    protected $table = 'proyecto_vinculacions';
    
    protected $fillable = ['nombre', 'descripcion', 'docente_id', 'periodo_academico_id', 'estado'];
    
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }
    
    public function periodoAcademico()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }
    
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'proyecto_vinculacion_id');
    }
}