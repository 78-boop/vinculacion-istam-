<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoAdministrativo extends Model
{
    protected $table = 'certificados_administrativos';

    protected $fillable = [
        'inscripcion_id',
        'numero_certificado',
        'fecha_generacion',
        'generado_por',
        'ruta_pdf',
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }
}