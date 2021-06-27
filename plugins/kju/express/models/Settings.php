<?php

namespace Kju\Express\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'kju_express_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
