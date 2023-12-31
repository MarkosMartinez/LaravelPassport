<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{

    /**

     * Register api

     *

     * @return \Illuminate\Http\Response

     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'Usuario registrado correctamente!');
    }

    /**

     * Login api

     *

     * @return \Illuminate\Http\Response

     */

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('LaravelPassport')->accessToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'Usuario logueado correctamente');
        } else {
            return $this->sendError('Unautorizado.', ['error' => 'Unautorizado']);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->delete(); // o revoke();
        return response()
        ->json([
            'message' => 'Sesion cerrada correctamente!',
        ]);
    }
        public function logoutall(Request $request)
    {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete(); // o revoke();
        });
        return response()
        ->json([
            'message' => 'Sesiones cerradas correctamente!',
        ]);
    }
}