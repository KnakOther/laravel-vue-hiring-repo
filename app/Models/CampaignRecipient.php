<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignRecipient extends Model
{
    use HasFactory;
    protected $fillable = [
        'campaign_id',
        'recipient_id',
        'message_id',
        'html',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'opened_at',
        'clicked_at',
        'complaint_at',
        'failed_at',
        'failure_reason',
    ];

    protected $table = 'campaign_recipient';

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}
