<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password; 
use Illuminate\Validation\Rule; 

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    } 
    public function rules(): array 
    { 
        return [ 
            'name' => ['required', 'string', 'max:255'], 
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users', 'email') // Menggunakan class Rule agar lebih clean 
            ], 

            'password' => ['required', 'string', 
                Password::min(8)->letters()->numbers() 
            ], 

            'role' => ['required', 
                Rule::in(['admin', 'petugas', 'peminjam']) // input hanya boleh dari opsi ini 
            ], 
            'no_hp' => ['nullable', 'digits_between:11,13'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]; 
    } 
}
