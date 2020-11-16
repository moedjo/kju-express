<?php

namespace Kju\Express\Models;

use Model;

/**
 * Model
 */
class Courier extends \Backend\Models\User
{
    public $belongsTo = [];

    public function scopeCourier($query)
    {
        return $query->whereHas('groups', function ($query) {
            $query->where('code', 'dojo_inventory_courier');
        });
    }
}
