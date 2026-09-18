<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcions';
    
    protected $fillable = [
        'estudiante_id',
        'proyecto_vinculacion_id',
        'fecha_inscripcion',
        'horas_cumplidas',
        'estado',
        'horas_requeridas',
        'fecha_inicio',
        'fecha_finalizacion_estimada',
        'certificado_aprobado'
    ];
    
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
    
    public function proyecto()
    {
        return $this->belongsTo(ProyectoVinculacion::class, 'proyecto_vinculacion_id');
    }
    
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'inscripcion_id');
    }

    public function registrosHoras()
    {
        return $this->hasMany(RegistroHora::class);
    }

    public function actividadesEstudiante()
    {
        return $this->hasMany(ActividadEstudiante::class);
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }

    public function certificadosEstudiante()
    {
        return $this->hasMany(CertificadoEstudiante::class);
    }

    public function certificadoAdministrativo()
    {
        return $this->hasOne(CertificadoAdministrativo::class);
    }

    public function postulacionesActividad()
    {
        return $this->hasMany(PostulacionActividad::class);
    }

    // ¿Ya subió y aprobó los 8 documentos requeridos?
    public function todosCertificadosAprobados()
    {
        $totalActivos = TipoCertificado::where('activo', true)->count();
        $totalAprobados = $this->certificadosEstudiante()
            ->where('estado', 'aprobado')
            ->count();

        return $totalActivos > 0 && $totalAprobados >= $totalActivos;
    }

    // Método para calcular horas completadas
    public function horasCompletadas()
    {
        return $this->registrosHoras()
            ->where('estado', 'aprobada')
            ->sum('horas_registradas') ?? 0;
    }

    // Método para calcular porcentaje completado
    public function porcentajeCompletado()
    {
        $horas = $this->horasCompletadas();
        return min(100, round(($horas / ($this->horas_requeridas ?? 90)) * 100, 2));
    }

    // Método para verificar si puede certificarse
    public function puedeCertificarse()
    {
        return $this->horasCompletadas() >= ($this->horas_requeridas ?? 90);
    }
}