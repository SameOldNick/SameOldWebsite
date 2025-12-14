<?php

namespace App\Components\Ntfy\Services;

use App\Components\Ntfy\DTOs\MessageResponse;
use Ntfy\Auth\Token;
use Ntfy\Auth\User;
use Ntfy\Exception\NtfyException;
use Ntfy\Message;
use Ntfy\Server;

class Ntfy
{
    protected Client $client;

    public function __construct(private readonly array $config)
    {
        $this->client = $this->createClient();

    }

    /**
     * Send a message via ntfy.
     *
     * @throws \Ntfy\Exception\NtfyException
     * @throws \Ntfy\Exception\EndpointException
     */
    public function send(Message $message): MessageResponse
    {
        // If message doesn't have a topic, set the default
        $this->assignTopic($message);

        return $this->client->send($message);
    }

    /**
     * Assign the default topic to the message if not already set.
     */
    protected function assignTopic(Message $message): void
    {
        if ($defaultTopic = $this->getConfigValue('default_topic')) {
            try {
                $message->getData();
            } catch (NtfyException) {
                // Topic not set, will assign default
                $message->topic($defaultTopic);
            }
        }
    }

    /**
     * Create the Server instance.
     */
    protected function createServer(): Server
    {
        $server = new Server($this->getConfigValue('server_url', 'https://ntfy.sh/'));

        return $server;
    }

    /**
     * Create the Ntfy Client instance.
     */
    protected function createClient(): Client
    {
        $server = $this->createServer();

        $auth = match ($this->getConfigValue('auth_method')) {
            'user' => new User(
                $this->getConfigValue('auth_credentials.username'),
                $this->getConfigValue('auth_credentials.password')
            ),
            'token' => new Token(
                $this->getConfigValue('auth_token')
            ),
            default => null,
        };

        return new Client($server, $auth);
    }

    /**
     * Get a configuration value.
     *
     * @param  mixed|null  $default
     * @return mixed
     */
    protected function getConfigValue(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
