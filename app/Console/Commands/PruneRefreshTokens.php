<?php

namespace App\Console\Commands;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Console\Command;

class PruneRefreshTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:refresh-tokens {--clear : Deletes all refresh tokens that are active or expired.} {--force : Skips prompts to delete tokens} {--user= : Removes refresh tokens for user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prunes JWT refresh tokens.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (! is_null($this->option('user'))) {
            $user = User::find($this->option('user'));

            if (is_null($user)) {
                $this->error(sprintf('User with ID %s could not be found.', $this->option('user')));

                return 1;
            }

            if ($this->option('clear')) {
                if ($user->refreshTokens->isEmpty()) {
                    $this->info(sprintf('No refresh tokens for user "%s" found.', $user->email));

                    return 0;
                }

                if (! $force && ! $this->confirm(sprintf('Do you want to delete %d refresh tokens for user "%s"?', $user->refreshTokens->count(), $user->email))) {
                    return 1;
                }

                $deleted = $user->refreshTokens->toQuery()->delete();

                $this->info(sprintf('Cleared %d refresh tokens for user "%s".', $deleted, $user->email));
            } else {
                $found = $user->refreshTokens()->where('expires_at', '<', now())->get();

                if ($found->isEmpty()) {
                    $this->info(sprintf('No expired refresh tokens for user "%s" found.', $user->email));

                    return 0;
                }

                if (! $force && ! $this->confirm(sprintf('Do you want to delete %d refresh tokens for user "%s"?', $found->count(), $user->email))) {
                    return 1;
                }

                $deleted = $found->toQuery()->delete();

                $this->info(sprintf('Deleted %d refresh tokens for user "%s".', $deleted, $user->email));
            }
        } else {
            $refreshTokens = $this->option('clear') ? RefreshToken::all() : RefreshToken::where('expires_at', '<', now())->get();

            if ($refreshTokens->isEmpty()) {
                $this->info('No refresh tokens found.');

                return 0;
            }

            if (! $force && ! $this->confirm(sprintf('Do you want to delete %d refresh tokens?', $refreshTokens->count()))) {
                return 1;
            }

            $deleted = $refreshTokens->toQuery()->delete();

            $this->info(sprintf('%s %d refresh tokens.', $this->option('clear') ? 'Cleared' : 'Removed', $deleted));
        }
    }
}
