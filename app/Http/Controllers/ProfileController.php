<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function updateUser(Request $request): JsonResponse
    {
        try {
            // Validar solo el nombre
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            ]);
    
            $user = $request->user();
            $user->fill($validatedData);
    
            // No se verifica el cambio de email en este caso
    
            $user->save();
    
            // Devolver un mensaje de éxito con el status 'success'
            return response()->json(['message' => 'Perfil actualizado correctamente', 'user' => $user, 'status' => 'success']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar el error de validación y devolver un mensaje de error con el status 'error'
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Manejar otros tipos de errores y devolver un mensaje de error con el status 'error'
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->user()->updatePassword(
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|confirmed|min:8',
            ])
        );

        return back()->with('password_message', 'Contraseña actualizada correctamente.');
    }

    public function destroy(Request $request)
    {
        $request->user()->delete();

        return redirect('/');
    }
}
