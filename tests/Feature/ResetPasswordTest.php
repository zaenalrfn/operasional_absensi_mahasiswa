<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test resetting password successfully.
     */
    public function test_reset_password_successfully()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset successfully. Check your email for the new password.']);

        $user->refresh();

        $this->assertFalse(Hash::check('oldpassword', $user->password));

        Mail::assertSent(ResetPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test resetting password with non-existent email.
     */
    public function test_reset_password_with_invalid_email()
    {
        Mail::fake();

        $response = $this->postJson('/api/reset-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        Mail::assertNothingSent();
    }
}
