<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              =>  'required|string',
            'email'             =>  'required|string|email',
            'password'          =>  'required|string|alpha_num',
            'phone'             =>  'required|string',
            'role_id'           =>  'required|exists:roles,id',
            'profile_picture'   =>  'required|mimes:jpg,png,pdf|max:10240'
        ]);
    }
}
