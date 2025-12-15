# Login Feature Documentation

## Overview
Sistem autentikasi lengkap untuk Insurance Management System dengan role-based access control.

## Features

### 1. Login System ✓
- Login form dengan email dan password
- Remember me functionality
- Session-based authentication
- Automatic redirect ke home setelah login
- Redirect ke login jika akses page tanpa login

### 2. User Roles ✓
- **Admin**: Full access untuk create/edit contract
- **Approver**: Access untuk approve/reject contract

### 3. Protected Routes ✓
- Semua routes di `web.php` protected dengan `auth` middleware
- Semua API routes protected dengan `web` dan `auth` middleware
- User harus login untuk akses aplikasi

### 4. UI Components ✓
- Login page dengan design modern
- User info di navbar (nama, email, role badge)
- Logout button di dropdown menu
- Role badge (Admin: blue, Approver: green)

## Default Users

### Admin Account
```
Email: admin@example.com
Password: password
Role: admin
```

### Approver Account
```
Email: approver@example.com
Password: password
Role: approver
```

## Usage

### Login
1. Akses aplikasi (akan redirect ke `/login`)
2. Masukkan email dan password
3. Optional: centang "Remember Me"
4. Klik "Login"
5. Akan redirect ke home page

### Logout
1. Klik dropdown user di navbar (kanan atas)
2. Klik "Logout"
3. Akan redirect ke login page

## Technical Details

### Authentication Flow
```
User → Login Page → LoginController@login → Validate Credentials → 
Create Session → Redirect to Home
```

### Route Protection
```php
// Web routes protected
Route::middleware(['auth'])->group(function () {
    // All application routes here
});

// API routes protected
Route::middleware(['web', 'auth'])->group(function () {
    // All API routes here
});
```

### Session Configuration
- Driver: file (default Laravel)
- Lifetime: 120 minutes
- Remember me: 2 weeks

## Files Created/Modified

### New Files
- `app/Http/Controllers/Auth/LoginController.php` - Authentication controller
- `resources/views/auth/login.blade.php` - Login form view
- `database/seeders/DefaultUsersSeeder.php` - Default users seeder

### Modified Files
- `routes/web.php` - Added login routes & auth middleware
- `routes/api.php` - Added auth middleware to API routes
- `resources/views/components/navbar.blade.php` - Added user info & logout
- `app/Models/User.php` - Added role field to fillable

## Security Features

### 1. Password Hashing
- Passwords stored dengan bcrypt hashing
- Laravel's Hash facade untuk verify password

### 2. CSRF Protection
- Semua form protected dengan CSRF token
- Token automatically generated dan validated

### 3. Session Security
- Session regeneration setelah login
- Session invalidation setelah logout
- Token regeneration untuk prevent session fixation

### 4. Validation
- Email dan password required
- Email format validation
- Error messages untuk invalid credentials

## Testing

### Test Login Flow
1. Logout jika sudah login
2. Akses `/` atau route lain
3. Verify redirect ke `/login`
4. Login dengan credentials yang valid
5. Verify redirect ke home
6. Verify nama dan role di navbar

### Test Role-Based Access
1. Login sebagai Admin
2. Verify bisa create/edit contract (jika pending/rejected)
3. Logout dan login sebagai Approver
4. Verify bisa approve/reject contract
5. Verify tidak bisa create/edit contract

### Test Logout
1. Klik dropdown user di navbar
2. Klik logout
3. Verify redirect ke login page
4. Try akses route lain
5. Verify redirect kembali ke login

## Troubleshooting

### Issue: "419 Page Expired" error
**Solution**: Clear browser cache atau refresh halaman login

### Issue: Tidak bisa login dengan credentials yang benar
**Solution**: 
1. Verify user exists di database
2. Check password hash di database
3. Run seeder lagi: `php artisan db:seed --class=DefaultUsersSeeder`

### Issue: Redirect loop
**Solution**:
1. Check session configuration di `.env`
2. Verify session directory writable
3. Clear session: `php artisan cache:clear`

## Environment Configuration

### Required `.env` Settings
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

## API Authentication

For API requests, include session cookies or use Sanctum tokens:

```javascript
// AJAX requests with CSRF token
$.ajax({
    url: '/api/endpoint',
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { ... }
});
```

## Password Reset (Future Enhancement)

Fitur ini belum diimplementasikan. Untuk reset password saat ini:
```php
// Via tinker atau seeder
$user = User::where('email', 'email@example.com')->first();
$user->password = Hash::make('new-password');
$user->save();
```

## Change Password (Future Enhancement)

Untuk change password saat ini, bisa dilakukan via database:
```sql
UPDATE users 
SET password = '$2y$12$...' -- hash dari password baru
WHERE email = 'email@example.com';
```

## Next Steps

1. **Password Reset**: Implement forgot password functionality
2. **Profile Page**: Allow users to update their profile
3. **Change Password**: Allow users to change their password
4. **User Management**: Admin page to manage users
5. **Activity Log**: Track user login/logout activities
6. **Two-Factor Auth**: Add 2FA for enhanced security

## Support

Untuk bantuan lebih lanjut, check:
- Laravel Authentication Documentation
- Project README.md
- APPROVAL_FEATURE.md (untuk role-based contract approval)
