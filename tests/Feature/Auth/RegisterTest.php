<?php

namespace Tests\Feature\Auth;

use App\User; // Tambahkan use model App\User
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    // Trait refresh database agar migration dijalankan
    use RefreshDatabase;

    private function getRegisterFields($overrides = [])
    {
        return array_merge([
            'name'                  => 'John Thor',
            'email'                 => 'username@example.net',
            'password'              => 'secret',
            'password_confirmation' => 'secret',
        ], $overrides);
    }

    /** @test */
    public function user_can_register()
    {
        // Kunjungi halaman '/register'
        $this->visit('/register');

        // Submit form register dengan nama, email dan password 2 kali
        $this->submitForm('Register', $this->getRegisterFields());

        // Lihat halaman ter-redirect ke url '/home' (register sukses).
        $this->seePageIs('/home');

        // Kita melihat halaman tulisan "Dashboard" pada halaman itu.
        $this->seeText('Dashboard');

        // Lihat di database, tabel users, data user yang register sudah masuk
        $this->seeInDatabase('users', [
            'name'  => 'John Thor',
            'email' => 'username@example.net',
        ]);

        // Cek hash password yang tersimpan cocok dengan password yang diinput
        $this->assertTrue(app('hash')->check('secret', User::first()->password));
    }

    /**
     * @test
     * @dataProvider registrationDataProvider
     */
    public function validate_user_register_form($errorField, $formFields)
    {
        $this->post('/register', $this->getRegisterFields($formFields));

        $this->assertSessionHasErrors([$errorField]);
    }

    public function registrationDataProvider()
    {
        // Return array dari [kondisi] and [hasil yang diharapkan].

        return [
            // error pada field name, jika name kosong
            ['name', ['name' => '']],
            // error pada field name, jika name panjang 260 karakter
            ['name', ['name' => str_repeat('John Thor ', 26)]],
            // error pada field email, jika email kosong
            ['email', ['email' => '']],
            // error pada field email, jika mengisi email yang tidak valid
            ['email', ['email' => 'username.example.net']],
            // error pada field email, jika mengisi email yang terlalu panjang
            ['email', ['email' => str_repeat('username@example.net', 13)]],
            // error pada field password, jika password kosong
            ['password', ['password' => '']],
            // error pada field password, jika password kurang dari 6 karakter
            ['password', ['password' => 'ecret']],
            // error pada field password, jika konfirmasi password tidak sama
            ['password', ['password' => 'secret', 'password_confirmation' => 'escret']],
        ];
    }

    /** @test */
    public function user_email_must_be_unique_on_users_table()
    {
        // Buat satu user baru
        $user = factory(User::class)->create(['email' => 'emailsama@example.net']);

        // Submit form untuk register dengan field
        // 'email' yang sudah ada di tabel users.
        $this->post('/register', $this->getRegisterFields([
            'email' => 'emailsama@example.net',
        ]));

        // Cek pada session apakah ada error untuk field 'email'.
        $this->assertSessionHasErrors(['email']);
    }
}
