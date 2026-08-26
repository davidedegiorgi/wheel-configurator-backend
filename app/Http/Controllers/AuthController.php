<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoginRequest;
use App\Http\Requests\StoreRegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(StoreRegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        
        return response()->json([
            'message' => 'Registrazione completata.',
            'user' => $user,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(StoreLoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'message' => 'Credenziali non valide',
            ], 401);
        }

        return response()->json([
            'message' => 'Login effettuato con successo',
            'user' => $result['user'],
            'token' => $result['token'],
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = $this->authService->sendPasswordResetLink($request->validated());

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Hai già richiesto un link di recupero. Riprova tra qualche minuto.',
            ], 429);
        }

        return response()->json([
            'message' => 'Se l\'email è registrata, riceverai un link per reimpostare la password.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Il link non è valido o è scaduto.',
            ], 422);
        }

        return response()->json([
            'message' => 'Password aggiornata. Ora puoi accedere.',
        ]);
    }


    /**
     * Get current user profile
     */
    public function me()
    {
        return response()->json($this->authService->getProfile(Auth::user()));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return response()->json(
            $this->authService->updateProfile(Auth::user(), $request->validated())
        );
    }

    public function deleteAccount()
    {
        $this->authService->deleteAccount(Auth::user());

        return response()->noContent();
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->authService->logout(Auth::user());

        return response()->json([
            'message' => 'Logout effettuato con successo',
        ]);
    }
}
