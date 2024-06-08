<?php

namespace App\Models\Collections;

use App\Models\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;

/**
 * @mixin Collection<int, PrivateChannel>
 */
class PrivateChannelCollection extends Collection
{
    /**
     * Gets the channel names
     *
     * @return Collection<int, string>
     */
    public function channels()
    {
        return $this->map(fn (PrivateChannel $privateChannel) => $privateChannel->channel ?? false)->filter()->unique();
    }

    /**
     * Gets active channels
     *
     * @return PrivateChannelCollection
     */
    public function active()
    {
        return $this->filter(fn (PrivateChannel $privateChannel) => ! $privateChannel->isExpired());
    }

    /**
     * Gets expired channels
     *
     * @return PrivateChannelCollection
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
     * @return PrivateChannelCollection
     */
    public function lookup(string $uuid, string $channel)
    {
        return $this->filter(fn (PrivateChannel $privateChannel) => $privateChannel->uuid === $uuid && (is_null($privateChannel->channel) || $privateChannel->channel === $channel));
    }
}
