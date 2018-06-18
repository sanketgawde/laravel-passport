<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Lcobucci\JWT\Parser;

class UserController extends Controller
{
public $successStatus = 200;
/**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
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
            return response()->json(['error'=>$validator->errors()], 401);
        }
$input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['name'] =  $user->name;
return response()->json(['success'=>$success], $this-> successStatus);
    }
/**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }

    public function logout(Request $request)
    {
        // dd($request->bearerToken());
        $value = $request->bearerToken();
        $id= (new Parser())->parse($value)->getHeader('jti');
        $token= $request->user()->tokens->find($id);
        // dd($token);
        $token->revoke();
        return response()->json(['success'=>"User successfully logout"], $this-> successStatus);
        // code...
    }
}
/*
Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjRmYjg5N2I2M2Q0ZmI5MDE2NTk4ZGE2MmExMGIwY2NmNTE1ZmEyNjI5MGJmYTIyM2FhZmYwODI5OWMwNGI3NWJhNGZiOTE3NTViMTVjMzY0In0.eyJhdWQiOiIxIiwianRpIjoiNGZiODk3YjYzZDRmYjkwMTY1OThkYTYyYTEwYjBjY2Y1MTVmYTI2MjkwYmZhMjIzYWFmZjA4Mjk5YzA0Yjc1YmE0ZmI5MTc1NWIxNWMzNjQiLCJpYXQiOjE1Mjg0Mzg3NzksIm5iZiI6MTUyODQzODc3OSwiZXhwIjoxNTU5OTc0Nzc5LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.mmo9pyB0HS3JpuNwwk-n2l815rxpnBSZWdfLlrKsUb0DrtjNsohBYStNzna493QpLKvB6WXcWypz4uulaCHhNkBY1cp8Hkf6jxgX48_7W6pyKYdP2uKJPYCgHUuuA_EVEzjDQXrtQFDZ3Iu3YmYiha6IlBB2jTyCKJGNrQVI4_46QDjo5VomFDOmuhe66hQiSH3ShOvxYs81OIErA2kkUQMKmzask7Fsjx7gCfL8OhO6uZ0bvWZC9Qgchp21a_DDeQXpNUNGO-JMh6IrhhyPTJlsXUEdeg1_ql50S43WGLeIgsxzpKaIxPntl96p7i01ly2U-DJJgUrT1M8lQqi3S7ClRiVrotQupobopjKNtxOyhQHiH1KL3WoOilVp3LF_L_mmE8Rf8MY3IWdqkVrZ8CvWPpiqOEYODjUqyFYiXLIbS36fEjE0gQFYptKz4T9Z-Oh-jISgtVO9A4O93VElsQn_lBntK2wjk14quSTE0NFngSWI08sn5Zb4VLn3xSIoiyJlWATKtJ-bPtxiRJhxIWLbd33jhQ_jCeAuBslqRRtwqDX7iRVpyYki6LIBltYAVll7Z6wAzpeeYolkVwdWLcNDeVS0BaEePwwdKqElzFIGSYfYYP2myJIr3X0nNxPvzbumft4zOyuIp1KA115SToI6CU3qgPVBmJ56XuuWnU4
*/
