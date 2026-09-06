<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class CoreSettings extends Model
{
    protected $table = 'core_info';

    protected $fillable = [
        'sys_name', 'release', 'sys_build', 'copyright_year',
        'banner', 'bannerMode', 'bannerLink',
        'emailfirchief', 'emaildepfirchief', 'emailcinstructor',
        'emaileventc', 'emailfacilitye', 'emailwebmaster',
        'academy_preview_mode', 'academy_nav_enabled', 'academy_staff_access_enabled',
        'academy_access_mode', 'academy_maintenance_mode',
    ];

    protected $casts = [
        'academy_preview_mode' => 'boolean',
        'academy_nav_enabled' => 'boolean',
        'academy_staff_access_enabled' => 'boolean',
        'academy_maintenance_mode' => 'boolean',
    ];
}

