<?php
namespace App\Services;

class EmailEvent {
    const OPENED = 'opened';
    const DELIVERED = 'delivered';
    const CLICKED = 'clicked';
    const BOUNCED = 'bounced';
    const FAILED = 'failed';
}
