<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageTasksTest extends TestCase
{
    /** @test */
    public function user_can_create_a_task()
    {
        // User buka halaman Daftar Task
        $this->visit('/tasks');

        // Submit Form dengan form isian `name` dan `description`
        $this->submitForm('Create Task', [
            'name' => 'My First Task',
            'description' => 'This is my first task on my new job.',
        ]);

        // Lihat Record tersimpan ke database
        $this->seeInDatabase('tasks', [
            'name' => 'My First Task',
            'description' => 'This is my first task on my new job.',
            'is_done' => 0,
        ]);

        // Redirect ke halaman Daftar Task
        $this->seePageIs('/tasks');

        // Tampil hasil task yang telah diinput
        $this->see('My First Task');
        $this->see('This is my first task on my new job.');
    }

    /** @test */
    public function user_can_browser_tasks_index_page()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function user_can_edit_an_existing_task()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function user_can_delete_an_existing_task()
    {
        $this->assertTrue(true);
    }
}
