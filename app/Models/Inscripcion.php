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

    public function certificadosVigentes()
    {
        return $this->hasMany(CertificadoEstudiante::class)->vigentes();
    }

    public function certificadoAdministrativo()
    {
        return $this->hasOne(CertificadoAdministrativo::class);
    }

    public function postulacionesActividad()
    {
        return $this->hasMany(PostulacionActividad::class);
    }

    // Actividades (creadas por el administrador) en las que participa esta inscripción
    public function actividadesAsignadas()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_inscripcion');
    }

    // Inscripciones que le corresponden a un docente: las de los proyectos que dirige
    // y las de las actividades de las que el administrador lo hizo responsable
    public function scopeDelDocente($query, int $docenteId)
    {
        return $query->where(function ($q) use ($docenteId) {
            $q->whereHas('proyecto', fn ($p) => $p->where('docente_id', $docenteId))
                ->orWhereHas('actividadesAsignadas', fn ($a) => $a->where('actividades.docente_id', $docenteId));
        });
    }

    public function esDelDocente(int $docenteId): bool
    {
        return static::whereKey($this->getKey())->delDocente($docenteId)->exists();
    }

    // ¿Ya subió y aprobó los 8 documentos requeridos?
    public function todosCertificadosAprobados()
    {
        $totalActivos = TipoCertificado::where('activo', true)->count();
        $totalAprobados = $this->certificadosVigentes()
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