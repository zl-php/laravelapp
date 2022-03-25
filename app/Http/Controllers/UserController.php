<?php
/**
 * FILE: UserController.php.
 */
namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;

class UserController extends Controller
{
    public function test()
    {
        $user = User::find(1);


        $token = auth()->login($user);

        dd($token);

        return $this->success();

    }

    public function test1()
    {
        return $this->success();

    }
}