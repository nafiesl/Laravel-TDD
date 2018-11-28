<?php

namespace Tests\Feature\Auth;

use App\User; // Tambahkan use model App\User
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    // Trait refresh database agar migration
    use RefreshDatabase;

    /** @test */
    public function registered_user_can_login()
    {
        // Kita memiliki 1 user terdaftar
        $user = factory(User::class)->create([
            'email'    => 'username@example.net',
            'password' => bcrypt('secret'),
        ]);

        // Kunjungi halaman /login
        $this->visit('/login');

        // Submit form login dengan email dan password yang tepat
        $this->submitForm('Login', [
            'email'    => 'username@example.net',
            'password' => 'secret',
        ]);

        // Lihat halaman ter-redirect ke URL home (login sukses).
        $this->seePageIs('/home');

        // Kita melihat halaman tulisan "Dashboard" pada halaman itu.
        $this->seeText('Dashboard');
    }

    /** @test */
    public function logged_in_user_can_logout()
    {
        // Kita memiliki 1 user terdaftar
        $user = factory(User::class)->create([
            'email'    => 'username@example.net',
            'password' => bcrypt('secret'),
        ]);

        // Login sebagai user tersebut
        $this->actingAs($user);

        // Kunjungi halaman '/home'
        $this->visit('/home');

        // Buat post request ke url '/logout'
        $this->post('/logout');

        // Kunjungi (lagi) halaman '/home'
        $this->visit('/home');

        // User ter-redirect ke halaman '/login'
        $this->seePageIs('/login');
    }
}
