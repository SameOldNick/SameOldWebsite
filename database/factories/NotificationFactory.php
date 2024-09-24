<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),  // Generate a UUID
            'type' => $this->faker->randomElement(['App\Notifications\SomeType', 'App\Notifications\AnotherType']),
            'notifiable_type' => $this->faker->randomElement([\App\Models\User::class]),
            'notifiable_id' => $this->faker->numberBetween(1, 100),
            'data' => json_encode([
                'message' => $this->faker->sentence,
                'url' => $this->faker->url,
            ]),
            'read_at' => $this->faker->optional()->dateTime(),  // Nullable timestamp
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Sets the notifiable
     *
     * @return static
     */
    public function notifiable(Model $notifiable)
    {
        return $this->state([
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
        ]);
    }

    /**
     * Randomly chooses a notifiable
     *
     * @param  \Illuminate\Database\Eloquent\Model[]  $notifiables
     * @return static
     */
    public function notifiables(array $notifiables)
    {
        return $this->state(function () use ($notifiables) {
            $notifiable = Arr::random($notifiables);

            return [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->getKey(),
            ];
        });
    }

    /**
     * Gets a random notifiable of type
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $class
     * @return static
     */
    public function notifiableType(string $class)
    {
        $models = $class::all();

        return $this->notifiables($models->all());
    }

    /**
     * Randomly assigns a notification type
     *
     * @param  string|string[]  $types
     * @return static
     */
    public function types($types)
    {
        $types = ! is_array($types) ? [$types] : $types;

        return $this->state(fn () => [
            'type' => $this->faker->randomElement($types),
        ]);
    }

    /**
     * Sets notifications as read
     *
     * @return static
     */
    public function read()
    {
        return $this->state(fn () => [
            'read_at' => $this->faker->dateTime(),
        ]);
    }

    /**
     * Sets notifications as unread
     *
     * @return static
     */
    public function unread()
    {
        return $this->state(['read_at' => null]);
    }

    /**
     * Creates a random message notification
     *
     * @return static
     */
    public function messageNotification()
    {
        return $this->types('6414fd8c-847a-492b-a919-a5fc539456e8')->state([
            'data' => json_encode([
                'addresses' => [
                    'to' => array_map(fn () => $this->generateAddress(), array_fill(0, $this->faker->numberBetween(1, 3), '')),
                    'cc' => array_map(fn () => $this->generateAddress(), array_fill(0, $this->faker->numberBetween(0, 3), '')),
                    'bcc' => array_map(fn () => $this->generateAddress(), array_fill(0, $this->faker->numberBetween(0, 3), '')),
                    'replyTo' => array_map(fn () => $this->generateAddress(), array_fill(0, $this->faker->numberBetween(0, 3), '')),
                ],
                'subject' => $this->faker->sentence(),
                'view' => [
                    'html' => $this->faker->randomHtml(),
                    'text' => $this->faker->paragraphs(3, true),
                ],
                'type' => $this->faker->randomElement([\App\Mail\Contacted::class, \App\Mail\ConfirmMessage::class]),
            ]),
        ]);
    }

    /**
     * Creates a random security alert notification
     *
     * @return static
     */
    public function securityAlert()
    {
        return $this->types('513a8515-ae2a-47d9-9052-212b61f166b0')->state([
            'data' => json_encode([
                'id' => $this->faker->sha1(),
                'issue' => [
                    'id' => $this->faker->uuid(),
                    'datetime' => $this->faker->dateTime(),
                    'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
                    'message' => $this->faker->sentence(),
                    'context' => [],
                ],
            ]),
        ]);
    }

    /**
     * Generates a name and address for message notfication
     */
    protected function generateAddress(): array
    {
        return ['name' => $this->faker->optional()->name, 'address' => $this->faker->email];
    }
}
