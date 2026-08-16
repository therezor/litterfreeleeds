<?php

namespace App\Notifications;

use App\Filament\Resources\Onboardings\OnboardingResource;
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells a Purple Bag Holder that a volunteer near them has signed up and is
 * expecting to hear from them.
 *
 * Sent on email verification rather than on registration, so holders are never
 * sent chasing a mistyped address.
 */
class NewPickerAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $picker)
    {
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
        $message = (new MailMessage)
            ->subject('A new volunteer near you: '.$this->picker->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->picker->name.' has signed up as a litter picker and you are their nearest Purple Bag Holder. They have been told to expect to hear from you.')
            ->line('**Name:** '.$this->picker->name)
            ->line('**Email:** '.$this->picker->email)
            ->line('**Postcode:** '.Postcode::format((string) $this->picker->postcode));

        if ($this->distanceInMiles() !== null) {
            $message->line('**Roughly:** '.$this->distanceInMiles().' miles from you');
        }

        // The Onboarding resource, not the Users list — bag holders have no
        // ViewAny:User, so a link there would land them on a 403. Resolved
        // through the resource so a slug change cannot silently break it.
        return $message
            ->action('Open your onboarding list', OnboardingResource::getUrl())
            ->line('Once you have made contact and sorted them out with purple bags, mark them as onboarded so we know they are up and running.');
    }

    /**
     * Straight-line miles, to one decimal place. Null when either party has no
     * coordinates — a bag holder can be created in the panel without one.
     */
    protected function distanceInMiles(): ?float
    {
        $holder = $this->picker->assignedBagHolder;

        if (! $holder instanceof User
            || $holder->latitude === null
            || $this->picker->latitude === null) {
            return null;
        }

        $earthRadiusMiles = 3959;
        $latFrom = deg2rad((float) $this->picker->latitude);
        $latTo = deg2rad((float) $holder->latitude);
        $lngDelta = deg2rad((float) $holder->longitude - (float) $this->picker->longitude);

        $cosine = min(1.0, max(-1.0,
            sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lngDelta)
        ));

        return round($earthRadiusMiles * acos($cosine), 1);
    }
}
