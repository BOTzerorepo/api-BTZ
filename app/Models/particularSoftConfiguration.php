<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ParticularSoftConfiguration extends Model
{
    protected $table = 'particular_soft_configurations';

    protected $fillable = [
        'name',
        'logo',
        'imgLogin',
        'to_mail_trafico_Team',
        'cc_mail_trafico_Team',
    ];

    protected $appends = ['logo_url', 'img_login_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
    }

    public function getImgLoginUrlAttribute(): ?string
    {
        return $this->imgLogin ? asset('storage/'.$this->imgLogin) : null;
    }
}