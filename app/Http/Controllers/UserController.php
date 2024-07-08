<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();  // Obtenemos todos los usuarios de la tabla User
        return response()->json($users);
    }

    public function store( Request $request )
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($request->input('password')),
        ]);

    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function updateUser( Request $request, $id )
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:55',
            'email' => 'email|required|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if(!empty($validatedData['password']))
        {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        return response()->json($user);

    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

}
