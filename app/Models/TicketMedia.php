<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketMedia extends Model
{
    protected $guarded = [];

    public function getUrlAttribute(): string { return Storage::url($this->path); }
}
