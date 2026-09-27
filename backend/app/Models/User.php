<?php 
 
namespace App\Models; 
 
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Illuminate\Database\Eloquent\Relations\HasMany; 
 
class User extends Authenticatable 
{ 
    use HasApiTokens, HasFactory, Notifiable; 
    protected $table = 'users'; 
    protected $fillable = [ 
        'name', 'email', 'password', 'role', 'is_super_admin', 'no_hp', 'alamat',
        'foto_profile' 
    ]; 
    protected $hidden = [ 
        'password', 'remember_token', 
    ]; 
 
    protected function casts(): array 
    { 
        return [ 
            'email_verified_at' => 'datetime', 
            'password' => 'hashed', // Laravel otomatis meng-hash teks apapun yang masuk ke properti password! 
            'is_super_admin' => 'boolean',
        ]; 
    } 

    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && (bool) $this->is_super_admin;
    }
 
    public function peminjaman(): HasMany { 
        return $this->hasMany(Peminjaman::class); 
    } 

    public function logAktivitas(): HasMany { 
        return $this->hasMany(LogAktivitas::class); 
    } 

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->foto_profile && file_exists(public_path($this->foto_profile))) {
            return asset($this->foto_profile);
        }

        return asset('images/no-image.svg');
    }


}