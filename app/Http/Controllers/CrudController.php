<?php

namespace App\Http\Controllers;

use App\Models\Crud;
use Illuminate\Http\Request;

class CrudController extends Controller
{

    public function fetchLists()
    {
        $users = Crud::all();
        return response()->json($users);
    }

    public function searchLists(Request $request)
    {
        $search = $request->input('search');
        $users = Crud::where('firstName', 'LIKE', '%' . $search . '%')
            ->orWhere('lastName', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('gender', 'LIKE', '%' . $search . '%')
            ->get();
        return response()->json($users);
    }

    public function createList(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:cruds',
            'gender' => 'required',
            'password' => 'required|min:6'
        ]);

        $user = new Crud();
        $user->firstName = $validatedData['first_name'];
        $user->lastName = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->gender = $validatedData['gender'] == 'male' ? 0:1;
        $user->password = bcrypt($validatedData['password']);
        $user->save();

        return response()->json(['success' => true]);
    }

    public function getList($id)
    {
        $user = Crud::findOrFail($id);
        return response()->json($user);
    }

    public function updateList(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'gender' => 'required',
        ]);

        $user = Crud::findOrFail($id);
        $user->firstName = $validatedData['first_name'];
        $user->lastName = $validatedData['last_name'];
        $user->email = $validatedData['email'];
        $user->gender = $validatedData['gender'] == 'male' ? 0 : 1;
        $user->save();

        return response()->json(['success' => true]);
    }
}
