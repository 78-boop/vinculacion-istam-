<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoEstudiante extends Model
{
    protected $table = 'certificados_estudiante';

    protected $fillable = [
        'inscripcion_id',
        'tipo_certificado_id',
        'ruta_archivo',
        'nombre_archivo_original',
        'estado',
        'observaciones_docente',
        'aprobado_por',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function tipoCertificado()
    {
        return $this->belongsTo(TipoCertificado::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // Solo documentos de tipos requeridos activos (los desactivados por el administrador no cuentan)
    public function scopeVigentes($query)
    {
        return $query->whereHas('tipoCertificado', fn ($t) => $t->where('activo', true));
    }
}
