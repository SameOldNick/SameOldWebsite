<?php

namespace App\Components\Websockets;

use App\Components\Websockets\Console\OutputRedirector;
use App\Components\Websockets\Notifiers\ProcessNotifier;
use Illuminate\Support\Facades\Artisan as ArtisanFacade;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Pipes Artisan command to websocket.
 * TODO: Allow input piping.
 */
class Artisan
{
    /**
     * Intitializes Artisan instance
     *
     * @param  ProcessNotifier  $notifier  Used to send process related notifications
     */
    public function __construct(
        public readonly ProcessNotifier $notifier
    ) {}

    /**
     * Calls Artisan command and routes output to notifier.
     *
     * @param  string  $command
     * @return int Error code
     */
    public function __invoke($command, array $parameters = [])
    {
        $this->notifier->begin();

        $errorCode = ArtisanFacade::call($command, $parameters, new OutputRedirector($this->notifier));

        $this->notifier->complete($errorCode);

        return $errorCode;
    }

    /**
     * Creates Artisan instance.
     *
     * @param  object  $notifiable  Who to send output to
     * @param  ?UuidInterface $uuid UUID for notifications
     * @param ?int $maxLength Max. length of messages
     */
    public static function create(object $notifiable, ?UuidInterface $uuid = null, ?int $maxLength = null): self
    {
        return new self(ProcessNotifier::create($notifiable, $uuid, $maxLength));
    }

    /**
     * Invokes artisan command.
     *
     * @param  string  $command
     * @return int Error code
     */
    public static function call(object $notifiable, UuidInterface $uuid, $command, array $parameters = [])
    {
        return call_user_func(static::create($notifiable, $uuid), $command, $parameters);
    }
}
