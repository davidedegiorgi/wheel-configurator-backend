<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\AccountDeletedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $this->formatName($data['name']),
            'last_name' => $this->formatName($data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Login user and generate token
     */
    public function login(array $data): ?array
    {
        if (!Auth::attempt($data)) {
            return null;
        }

        $user = Auth::user();

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Get current user profile
     */
    public function getProfile(User $user): User
    {
        return $user;
    }

    public function sendPasswordResetLink(array $data): string
    {
        return Password::sendResetLink([
            'email' => $data['email'],
        ]);
    }

    public function resetPassword(array $data): string
    {
        return Password::reset($data, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            $user->tokens()->delete();
        });
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $this->formatName($data['name']),
            'last_name' => $this->formatName($data['last_name']),
        ]);

        return $user->fresh();
    }


    public function deleteAccount(User $user): void
    {
        $email = $user->email;
        $name = trim($user->name . ' ' . ($user->last_name ?? ''));

        DB::transaction(function () use ($user) {
            $configurations = Configuration::where('user_id', $user->id)->get();

            Quote::where('user_id', $user->id)->delete();

            foreach ($configurations as $configuration) {
                $configuration->components()->detach();
            }

            Configuration::where('user_id', $user->id)->delete();
            $user->tokens()->delete();
            $user->delete();
        });

        Notification::route('mail', $email)->notify(new AccountDeletedNotification($name));
    }

    private function formatName(string $value): string
    {
        return (string) Str::of($value)->trim()->squish()->title();
    }
}
