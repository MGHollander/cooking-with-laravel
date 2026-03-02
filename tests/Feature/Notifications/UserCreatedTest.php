<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_created_notification_can_be_sent_with_en_locale()
    {
        $this->app->setLocale('en');
        $user = User::factory()->create();
        $token = 'test-token';

        $notification = new UserCreated($token);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('test-token', $mail->actionUrl);
        $this->assertStringContainsString(urlencode($user->email), $mail->actionUrl);
        // English URL contains "reset-password"
        $this->assertStringContainsString('reset-password', $mail->actionUrl);
    }

    public function test_user_created_notification_can_be_sent_with_nl_locale()
    {
        $this->app->setLocale('nl');
        $user = User::factory()->create();
        $token = 'test-token';

        $notification = new UserCreated($token);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('test-token', $mail->actionUrl);
        $this->assertStringContainsString(urlencode($user->email), $mail->actionUrl);
        // Dutch URL contains "reset-wachtwoord"
        $this->assertStringContainsString('reset-wachtwoord', $mail->actionUrl);
    }
}
