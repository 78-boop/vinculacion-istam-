<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCertificado extends Model
{
    protected $table = 'tipos_certificado';

    protected $fillable = ['codigo', 'nombre', 'orden', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function certificadosEstudiante()
    {
        return $this->hasMany(CertificadoEstudiante::class);
    }
}