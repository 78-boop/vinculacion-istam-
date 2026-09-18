<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservacionDocente extends Model
{
    protected $table = 'observaciones_docente';

    protected $fillable = [
        'registro_hora_id',
        'docente_id',
        'observacion',
        'aprobacion'
    ];

    // Relaciones
    public function registroHora()
    {
        return $this->belongsTo(RegistroHora::class);
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }
}
