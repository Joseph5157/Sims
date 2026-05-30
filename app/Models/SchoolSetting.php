<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_address',
        'school_phone',
        'school_email',
        'principal_name',
        'school_motto',
        'affiliation_number',
        'established_year',
        'report_card_color',
        'report_card_footer_text',
    ];

    public static function current(): static
    {
        return static::firstOrCreate(
            [],
            ['school_name' => 'My School', 'report_card_color' => '#1e40af']
        );
    }
}
