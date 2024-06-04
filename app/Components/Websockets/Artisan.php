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
class Artisan {
    /**
     * Intitializes Artisan instance
     *
     * @param ProcessNotifier $notifier Used to send process related notifications
     */
    public function __construct(
        public readonly ProcessNotifier $notifier
    )
    {

    }

    /**
     * Calls Artisan command and routes output to notifier.
     *
     * @param string $command
     * @param array $parameters
     * @return int Error code
     */
    public function __invoke($command, array $parameters = []) {
        $this->notifier->begin();

        $errorCode = ArtisanFacade::call($command, $parameters, new OutputRedirector($this->notifier));

        $this->notifier->complete($errorCode);

        return $errorCode;
    }

    /**
     * Creates Artisan instance.
     *
     * @param object $notifiable Who to send output to
     * @param UuidInterface|null $uuid
     * @return self
     */
    public static function create(object $notifiable, ?UuidInterface $uuid = null): self {
        $uuid = $uuid ?? Uuid::getFactory()->uuid4();

        return new self(new ProcessNotifier($uuid, $notifiable));
    }

    /**
     * Invokes artisan command.
     *
     * @param object $notifiable
     * @param UuidInterface $uuid
     * @param string $command
     * @param array $parameters
     * @return int Error code
     */
    public static function call(object $notifiable, UuidInterface $uuid, $command, array $parameters = []) {
        return call_user_func(static::create($notifiable, $uuid), $command, $parameters);
    }
}
