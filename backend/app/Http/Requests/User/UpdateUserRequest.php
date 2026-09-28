<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 
use Illuminate\Validation\Rules\Password; 

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array 
    { 
        $user = $this->route('user'); 
        // Memastikan kita mendapatkan ID (mengantisipasi jika parameter route berupa objek Model) 
        $userId = $user instanceof \App\Models\User ? $user->id : $user; 
 
        return [ 
            'name' => ['required', 'string', 'max:255'], 
            'email' => ['required', 'string', 'email', 'max:255', 
                // Mengabaikan ID user yang sedang di-update agar tidak memicu error "Email sudah terdaftar" 
                Rule::unique('users', 'email')->ignore($userId), 
            ], 

            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers() 
            ], 

            'role' => ['required', 
                Rule::in(['admin', 'petugas', 'peminjam']) 
            ], 
            
            'no_hp' => ['nullable', 'digits_between:11,13'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]; 
    } 
}
