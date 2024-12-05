<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'send_date',
        'subject',
        'email_name',
        'knak_email_id',
        'html',
        'from_name',
        'from_email',
        'reply_email',
        'knak_version',
        'scheduled',
        'filter_type',
        'filter_value',
    ];

    protected $appends = ['stats'];

    public function recipients()
    {
        return $this->belongsToMany(Recipient::class)->withPivot('delivered_at', 'opened_at', 'clicked_at', 'failed_at', 'failure_reason', 'html');
    }

    // add stats attributes
    public function getStatsAttribute()
    {
        if ($this->recipients()->count() == 0) {
            return [
                'total_recipients' => 0,
                'delivered_percentage' => 0,
                'opened_percentage' => 0,
                'clicked_percentage' => 0,
            ];
        }
        $count = $this->recipients->count();

        $stats = [
            'total_recipients' => $this->recipients->count(),
            'delivered_percentage' => round($this->recipients->filter(function ($recipient) {
                return $recipient->pivot->delivered_at;
            })->count() / $this->recipients->count() * 100),
            'opened_percentage' => round($this->recipients->filter(function ($recipient) {
                return $recipient->pivot->opened_at;
            })->count() / $this->recipients->count() * 100),
            'clicked_percentage' => round($this->recipients->filter(function ($recipient) {
                return $recipient->pivot->clicked_at;
            })->count() / $this->recipients->count() * 100),
        ];

        return $stats;
    }

}
