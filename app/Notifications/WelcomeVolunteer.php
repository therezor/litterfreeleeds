<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * The one email a new volunteer gets: verify your address, here is what happens
 * next, and here is how to use a purple bag safely.
 *
 * It replaces Laravel's stock VerifyEmail for pickers (see
 * User::sendEmailVerificationNotification) so registration produces a single
 * email rather than a bare verification link followed by a welcome.
 */
class WelcomeVolunteer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        // Dispatched from inside RegisterVolunteer's transaction boundary; without
        // this the queued job can run before the user row is committed.
        $this->afterCommit();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var User $notifiable */
        $holder = $notifiable->assignedBagHolder;

        return (new MailMessage)
            ->subject('Welcome to Litter Free Leeds')
            ->markdown('mail.volunteers.welcome', [
                'name' => $notifiable->name,
                'verificationUrl' => $this->verificationUrl($notifiable),
                'hasBagHolder' => $holder instanceof User,
                'conditionsUrl' => route('purple-bag-conditions'),
                'picksUrl' => route('upcoming-picks.index'),
            ]);
    }

    /**
     * Points at our own public route, not Filament's. Filament's verification
     * routes sit behind its panel Authenticate middleware, which would 403 a
     * picker — who by design has no panel access — and leave them permanently
     * unable to verify.
     */
    protected function verificationUrl(User $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes((int) config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1((string) $notifiable->getEmailForVerification()),
            ],
        );
    }
}
