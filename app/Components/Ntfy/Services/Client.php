<?php

namespace App\Components\Ntfy\Services;

use App\Components\Ntfy\DTOs\MessageResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Ntfy\Auth\Token;
use Ntfy\Auth\User;
use Ntfy\Exception\EndpointException;
use Ntfy\Exception\NtfyException;
use Ntfy\Message;
use Ntfy\Server;

class Client
{
    /**
     * @param  Server  $server  Server URI
     * @param  Auth\User|Auth\Token  $auth  Authentication class instance
     */
    public function __construct(
        protected readonly Server $server,
        protected readonly User|Token|null $auth = null
    ) {
        //
    }

    /**
     * Send a message via ntfy.
     *
     * @throws \Ntfy\Exception\NtfyException
     * @throws \Ntfy\Exception\EndpointException
     */
    public function send(Message $message): MessageResponse
    {
        try {
            $httpClient = Http::throw()
                ->asJson()
                ->acceptJson()
                ->maxRedirects(0)
                ->timeout(10);

            if ($this->auth instanceof User) {
                $httpClient = $httpClient->withBasicAuth(
                    $this->auth->getUsername(),
                    $this->auth->getPassword()
                );
            } elseif ($this->auth instanceof Token) {
                $httpClient = $httpClient->withToken(
                    $this->auth->getToken()
                );
            }

            $response = $httpClient->post($this->server->get(), $message->getData());

            return new MessageResponse($response->json());
        } catch (ConnectionException $e) {
            throw new NtfyException('Connection error: '.$e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            if ($e->response->header('Content-Type') === 'application/json') {
                $json = $e->response->json();

                if (isset($json['error'], $json['code'])) {
                    $message = sprintf(
                        '%s (error code: %s, http status: %s)',
                        $json['error'],
                        $json['code'],
                        $json['http'] ?? $e->response->status()
                    );

                    throw new EndpointException('Request error: '.$message, 0, $e);
                }
            }

            throw new EndpointException('Request error: '.$e->getMessage(), 0, $e);
        }

    }
}
