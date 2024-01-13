<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

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

    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validar la contraseña actual
            if (!Hash::check($request->input('old_password'), $user->password)) {
                throw new \Exception('Contraseña no autorizada');
            }
            if (Hash::check($request->input('password'), $user->password)) {
                throw new \Exception('La nueva contraseña no puede ser igual a la actual.');
            }

            // Validar la nueva contraseña y su confirmación
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            // Actualizar la contraseña
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            return response()->json(['message' => 'Contraseña actualizada correctamente.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar el error de validación y devolver un mensaje de error con el status 'error'
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Manejar otras excepciones y devolver un mensaje de error genérico con el status 'error'
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                throw new \Exception('Usuario no encontrado.');
            }
            // Eliminar la foto de perfil
            //$user->deleteProfilePhoto();
            // Revocar y eliminar todos los tokens de acceso
            $user->tokens->each->delete();
            // Eliminar al usuario
            $user->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }
}
