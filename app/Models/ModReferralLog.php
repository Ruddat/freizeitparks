<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ModReferralLog extends Model
{
    protected $fillable = [
        'user_id',
        'referer_url',
        'source',
        'keyword',
        'landing_page',
        'ip_address',
        'visited_at',
        'visit_count',
        'created_at',
        'updated_at',
        'is_bot',
        'country',
        'city',
        'asn',
        'isp',
        'device_type',
        'os',
        'browser',
        'browser_language',
        'keyword_missing',

    ];

    public $timestamps = false; // Deaktiviert created_at und updated_at

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
