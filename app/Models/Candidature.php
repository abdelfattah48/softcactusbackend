<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $table = 'candidatures';

    public const STATUSES = ['new', 'reviewing', 'shortlisted', 'hired', 'rejected'];

    protected $fillable = [
        'poste', 'nom', 'prenom', 'email', 'tel', 'message',
        'cv_path', 'cv_original_name', 'status',
    ];

    /** CV download URL (relative /storage path), or null when no file. */
    public function cvUrl(): ?string
    {
        return $this->cv_path ? '/storage/' . $this->cv_path : null;
    }
}
