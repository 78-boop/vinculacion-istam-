<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';

    protected $fillable = [
        'inscripcion_id',
        'fecha_generacion',
        'numero_certificado',
        'horas_certificadas',
        'estado',
        'aprobado_por',
        'observaciones_rechazo',
        'fecha_aprobacion',
        'ruta_pdf'
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_aprobacion' => 'date',
        'horas_certificadas' => 'decimal:1',
    ];

    // Relaciones
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // Métodos
    public function esAprobado()
    {
        return $this->estado === 'aprobado';
    }

    public function esRechazado()
    {
        return $this->estado === 'rechazado';
    }

    public function estatoColor()
    {
        return match($this->estado) {
            'aprobado' => 'green',
            'rechazado' => 'red',
            'pendiente' => 'yellow',
            default => 'gray'
        };
    }
}
