<?php

namespace Kju\Express\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\Backend;
use Backend\Models\UserRole;
use BackendMenu;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Support\Facades\Flash;

class Users extends Controller
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $bodyClass = 'compact-container';

    public $requiredPermissions = [
        'access_users'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Kju.Express', 'users');
    }


    /**
     * Extends the list query to hide superusers if the current user is not a superuser themselves
     */
    public function listExtendQuery($query)
    {
        $query->where('is_superuser', false);
        $query->where('id', '!=', $this->user->id);

        if ($this->user->hasAccess('access_users_add_role_*')) {

            $query->where(function ($query) {

                if ($this->user->hasAccess('access_users_add_role_courier')) {
                    $role = UserRole::where('code', 'courier')->first();
                    $query->orWhere('role_id', $role->id);
                }
                if ($this->user->hasAccess('access_users_add_role_operator')) {
                    $role = UserRole::where('code', 'operator')->first();
                    $query->orWhere('role_id', $role->id);
                }
    
                if ($this->user->hasAccess('access_users_add_role_supervisor')) {
                    $role = UserRole::where('code', 'supervisor')->first();
                    $query->orWhere('role_id', $role->id);
                }
            });
            
        }
    }

    /**
     * Prevents non-superusers from even seeing the is_superuser filter
     */
    public function listFilterExtendScopes($filterWidget)
    {
    }

    /**
     * Strike out deleted records
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if ($record->trashed()) {
            return 'strike';
        }
    }

    /**
     * Extends the form query to prevent non-superusers from accessing superusers at all
     */
    public function formExtendQuery($query)
    {

        $query->where('is_superuser', false);

        $query->where('id', '!=', $this->user->id);

        if ($this->user->hasAccess('access_users_add_role_*')) {

            $query->where(function ($query) {

                if ($this->user->hasAccess('access_users_add_role_courier')) {
                    $role = UserRole::where('code', 'courier')->first();
                    $query->orWhere('role_id', $role->id);
                }
                if ($this->user->hasAccess('access_users_add_role_operator')) {
                    $role = UserRole::where('code', 'operator')->first();
                    $query->orWhere('role_id', $role->id);
                }
    
                if ($this->user->hasAccess('access_users_add_role_supervisor')) {
                    $role = UserRole::where('code', 'supervisor')->first();
                    $query->orWhere('role_id', $role->id);
                }
            });
            
        }
        // Ensure soft-deleted records can still be managed
        $query->withTrashed();
    }

    /**
     * Handle restoring users
     */
    public function update_onRestore($recordId)
    {
        $this->formFindModelObject($recordId)->restore();

        Flash::success(Lang::get('backend::lang.form.restore_success', ['name' => Lang::get('backend::lang.user.name')]));

        return Redirect::refresh();
    }


    /**
     * Unsuspend this user
     */
    public function update_onUnsuspendUser($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        $model->unsuspend();

        Flash::success(Lang::get('backend::lang.account.unsuspend_success'));

        return Redirect::refresh();
    }
}
