<?php

namespace App\Models\Collections;

use App\Models\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends Collection<int, PrivateChannel>
 */
class PrivateChannelCollection extends Collection
{
    /**
     * Gets the channel names
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function channels()
    {
        return $this->map(fn (PrivateChannel $privateChannel) => $privateChannel->channel ?? false)->filter()->unique();
    }

    /**
     * Gets active channels
     *
     * @return static
     */
    public function active()
    {
        return $this->filter(fn (PrivateChannel $privateChannel) => ! $privateChannel->isExpired());
    }

    /**
     * Gets expired channels
     *
     * @return static
     */
    public function expired()
    {
        return $this->filter(fn (PrivateChannel $privateChannel) => $privateChannel->isExpired());
    }

    /**
     * Gets private channels with UUID and name
     *
     * @param  string  $uuid  Expected channel UUID
     * @param  string  $channel  Expected name of channel. Channels with no name are included.
     * @return static
     */
    public function lookup(string $uuid, string $channel)
    {
        return $this->filter(fn (PrivateChannel $privateChannel) => $privateChannel->uuid === $uuid && (is_null($privateChannel->channel) || $privateChannel->channel === $channel));
    }
}
