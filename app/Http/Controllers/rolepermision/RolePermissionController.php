<?php

namespace App\Http\Controllers\rolepermision;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    const PER_PAGE = 20;
    const VIEW_DIR = 'oms_setting';
    public function getRoles() {
       $roles = Role::all();
       return view(self::VIEW_DIR. '.userRole')->with(compact('roles'));
    }

    public function addEditRole(Request $request) {
        dd($request->all());
    }
}
