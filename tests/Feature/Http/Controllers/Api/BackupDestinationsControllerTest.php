<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\FilesystemConfiguration;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use Spatie\Backup\Config\Config;
use Tests\Feature\Traits\CreatesUser;
use Tests\Feature\Traits\InteractsWithJWT;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BackupDestinationsControllerTest extends TestCase
{
    use CreatesUser;
    use InteractsWithJWT;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Tests getting all destinations
     *
     * @return void
     */
    #[Test]
    public function get_all_destinations()
    {
        FilesystemConfiguration::factory(5)->ftp()->create();
        FilesystemConfiguration::factory(5)->sftp('password')->create();
        FilesystemConfiguration::factory(5)->sftp('key')->create();

        $response = $this->actingAs($this->admin)->getJson('/api/backup/destinations');

        $response
            ->assertSuccessful()
            ->assertJsonIsArray()
            ->assertJsonCount(15)
            ->assertJsonStructure([
                '*' => [
                    'enable',
                    'id',
                    'name',
                    'type',
                    'host',
                    'port',
                    'auth_type',
                    'username',
                ],
            ]);
    }

    /**
     * Tests a destination is retrieved.
     *
     * @return void
     */
    #[Test]
    public function retrieve_destination()
    {
        FilesystemConfiguration::factory()->ftp()->create();
        FilesystemConfiguration::factory()->sftp('password')->create();
        FilesystemConfiguration::factory()->sftp('key')->create();

        $configuration = FilesystemConfiguration::all()->random();

        $response = $this->actingAs($this->admin)->getJson(sprintf('/api/backup/destinations/%s', $configuration->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'enable',
                'id',
                'name',
                'type',
                'host',
                'port',
                'auth_type',
                'username',
            ])
            ->assertJson(['id' => $configuration->getKey()]);
    }

    /**
     * Tests creating FTP destination that is enabled for backup.
     */
    #[Test]
    public function create_destination_ftp_enabled(): void
    {
        $data = [
            'enable' => true,
            'name' => $this->faker->slug,
            'type' => 'ftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'password' => $this->faker->unique()->password,
            'root' => $this->faker->boolean ? implode('/', $this->faker->words($this->faker->numberBetween(1, 4))) : null,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertHasBackupDisks([$fsConfig->driver_name], true);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, FtpAdapter::class);
    }

    /**
     * Tests creating destination FTP that isn't enabled for backup.
     */
    #[Test]
    public function create_destination_ftp_disabled(): void
    {
        $data = [
            'enable' => false,
            'name' => $this->faker->slug,
            'type' => 'ftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'password' => $this->faker->unique()->password,
            'root' => $this->faker->boolean ? implode('/', $this->faker->words($this->faker->numberBetween(1, 4))) : null,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertEquals($data['name'], $fsConfig->name);
        $this->assertEquals($data['host'], $fsConfig->configurable->host);
        $this->assertMissingBackupDisks([$fsConfig->driver_name]);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, FtpAdapter::class);
    }

    /**
     * Tests creating SFTP destination with password authentication that is enabled for backup.
     */
    #[Test]
    public function create_destination_sftp_password_enabled(): void
    {
        $data = [
            'enable' => true,
            'name' => $this->faker->slug,
            'type' => 'sftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'auth_type' => 'password',
            'password' => $this->faker->unique()->password,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertEquals($data['name'], $fsConfig->name);
        $this->assertEquals($data['host'], $fsConfig->configurable->host);
        $this->assertHasBackupDisks([$fsConfig->driver_name], true);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, SftpAdapter::class);
    }

    /**
     * Tests creating SFTP destination with password authentication that isn't enabled for backup.
     */
    #[Test]
    public function create_destination_sftp_password_disabled(): void
    {
        $data = [
            'enable' => false,
            'name' => $this->faker->slug,
            'type' => 'sftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'auth_type' => 'password',
            'password' => $this->faker->unique()->password,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertEquals($data['name'], $fsConfig->name);
        $this->assertEquals($data['host'], $fsConfig->configurable->host);
        $this->assertMissingBackupDisks([$fsConfig->driver_name]);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, SftpAdapter::class);
    }

    /**
     * Tests creating SFTP destination with key authentication that is enabled for backup.
     */
    #[Test]
    public function create_destination_sftp_key_enabled(): void
    {
        $data = [
            'enable' => true,
            'name' => $this->faker->slug,
            'type' => 'sftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'auth_type' => 'key',
            'private_key' => $this->faker->unique()->sha256,
            'passphrase' => $this->faker->boolean ? $this->faker->unique()->password : null,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertEquals($data['name'], $fsConfig->name);
        $this->assertEquals($data['host'], $fsConfig->configurable->host);
        $this->assertHasBackupDisks([$fsConfig->driver_name], true);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, SftpAdapter::class);
    }

    /**
     * Tests creating SFTP destination with key authentication that isn't enabled for backup.
     */
    #[Test]
    public function create_destination_sftp_key_disabled(): void
    {
        $data = [
            'enable' => false,
            'name' => $this->faker->slug,
            'type' => 'sftp',
            'host' => $this->faker->boolean ? $this->faker->unique()->ipv4 : $this->faker->unique()->domainName,
            'port' => $this->faker->boolean(90) ? 21 : $this->faker->numberBetween(1000, 9999),
            'username' => $this->faker->unique()->userName,
            'auth_type' => 'key',
            'private_key' => $this->faker->unique()->sha256,
            'passphrase' => $this->faker->boolean ? $this->faker->unique()->password : null,
            'extra' => $this->faker->boolean ? [] : null,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/backup/destinations', $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertEquals($data['name'], $fsConfig->name);
        $this->assertEquals($data['host'], $fsConfig->configurable->host);
        $this->assertMissingBackupDisks([$fsConfig->driver_name]);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, SftpAdapter::class);
    }

    /**
     * Tests enabling FTP destination as backup disk
     */
    #[Test]
    public function update_destination_basic(): void
    {
        FilesystemConfiguration::factory()->ftp()->create();
        FilesystemConfiguration::factory()->sftp('password')->create();
        FilesystemConfiguration::factory()->sftp('key')->create();

        $existing = FilesystemConfiguration::all()->random();

        $data = [
            'name' => $this->faker()->unique()->slug,
            'host' => $this->faker()->boolean ? $this->faker()->unique()->domainName : $this->faker()->unique()->ipv4,
            'port' => $this->faker()->unique()->numberBetween(1, 65534),
            'username' => $this->faker()->unique()->userName,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);

        $stored = $fsConfig->toArray();

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $stored[$key]);
        }
    }

    /**
     * Tests destinations basic info is updated.
     */
    #[Test]
    public function update_destinations_basic(): void
    {
        FilesystemConfiguration::factory(2)->ftp()->create();
        FilesystemConfiguration::factory(2)->sftp('password')->create();
        FilesystemConfiguration::factory(2)->sftp('key')->create();

        $destinations = [];

        foreach (FilesystemConfiguration::all() as $existing) {
            array_push($destinations, [
                'id' => $existing->getKey(),
                'name' => $this->faker()->unique()->slug,
                'host' => $this->faker()->boolean ? $this->faker()->unique()->domainName : $this->faker()->unique()->ipv4,
                'port' => $this->faker()->unique()->numberBetween(1000, 9999),
                'username' => $this->faker()->unique()->userName,
            ]);
        }

        $response = $this->actingAs($this->admin)->putJson('/api/backup/destinations', ['destinations' => $destinations]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
            ]);

        foreach ($destinations as $destination) {
            $stored = FilesystemConfiguration::find($destination['id'])->toArray();

            foreach ($stored as $key => $value) {
                $this->assertEquals($value, $stored[$key]);
            }
        }
    }

    /**
     * Tests enabling FTP destination as backup disk
     */
    #[Test]
    public function update_destination_ftp_enable(): void
    {
        $existing = FilesystemConfiguration::factory()->ftp()->create();

        $data = [
            'enable' => true,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertHasBackupDisks([$fsConfig->driver_name], true);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, FtpAdapter::class);
    }

    /**
     * Tests enabling SFTP destination as backup disk
     */
    #[Test]
    public function update_destination_sftp_enable(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('key')->create();

        $data = [
            'enable' => true,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $fsConfig = FilesystemConfiguration::find($response->json('configuration.id'));

        $this->assertNotNull($fsConfig);
        $this->assertHasBackupDisks([$fsConfig->driver_name], true);
        $this->assertStorageAdapterLoaded($fsConfig->driver_name, SftpAdapter::class);
    }

    /**
     * Tests changing FTP authentication from password to key
     */
    #[Test]
    public function update_destination_ftp_password_to_key(): void
    {
        $existing = FilesystemConfiguration::factory()->ftp()->create();

        $data = [
            'auth_type' => 'key',
            'private_key' => $this->faker()->sha256(),
            'passphrase' => $this->faker()->boolean ? $this->faker()->password() : null,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $diskConfig = FilesystemConfiguration::find($response->json('configuration.id'))->getFilesystemConfig();

        $this->assertNotNull($diskConfig['password']);
        $this->assertArrayNotHasKey('privateKey', $diskConfig);
    }

    /**
     * Tests changing SFTP authentication from password to key
     */
    #[Test]
    public function update_destination_sftp_password_to_key(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('password')->create();

        $data = [
            'auth_type' => 'key',
            'private_key' => $this->faker()->sha256(),
            'passphrase' => $this->faker()->boolean ? $this->faker()->password() : null,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $diskConfig = FilesystemConfiguration::find($response->json('configuration.id'))->getFilesystemConfig();

        $this->assertNull($diskConfig['password']);
        $this->assertEquals($data['private_key'], $diskConfig['privateKey']);
        $this->assertEquals($data['passphrase'], $diskConfig['passphrase']);
    }

    /**
     * Tests changing SFTP authentication from password to key but password is missing
     */
    #[Test]
    public function update_destination_sftp_password_to_key_missing(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('key')->create();

        $data = [
            'auth_type' => 'key',
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertJsonValidationErrorFor('private_key');
    }

    /**
     * Tests changing SFTP authentication from key to password
     */
    #[Test]
    public function update_destination_sftp_key_to_password(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('key')->create();

        $data = [
            'auth_type' => 'password',
            'password' => $this->faker()->password(),
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $diskConfig = FilesystemConfiguration::find($response->json('configuration.id'))->getFilesystemConfig();

        $this->assertNull($diskConfig['privateKey']);
        $this->assertNull($diskConfig['passphrase']);
        $this->assertEquals($data['password'], $diskConfig['password']);
    }

    /**
     * Tests changing SFTP authentication from key to password but password is missing
     */
    #[Test]
    public function update_destination_sftp_key_to_password_missing(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('key')->create();

        $data = [
            'auth_type' => 'password',
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertJsonValidationErrorFor('password');
    }

    /**
     * Tests changing root for SFTP destination
     */
    #[Test]
    public function update_destination_sftp_root(): void
    {
        $existing = FilesystemConfiguration::factory()->sftp('password')->create();

        // Root only exists in FTP configuration
        $data = [
            'root' => 'folder1/folder2',
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $this->assertEquals(
            FilesystemConfiguration::find($response->json('configuration.id'))->toArray(),
            $existing->toArray()
        );
    }

    /**
     * Tests changing root for FTP destination
     */
    #[Test]
    public function update_destination_ftp_change_root(): void
    {
        $existing = FilesystemConfiguration::factory()->ftp()->create();

        $data = [
            'root' => 'folder1/folder2',
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $diskConfig = FilesystemConfiguration::find($response->json('configuration.id'))->getFilesystemConfig();

        $this->assertEquals($data['root'], $diskConfig['root']);
    }

    /**
     * Tests removing root for FTP destination
     */
    #[Test]
    public function update_destination_ftp_remove_root(): void
    {
        $existing = FilesystemConfiguration::factory()->ftp()->create();

        $data = [
            'root' => null,
        ];

        $response = $this->actingAs($this->admin)->putJson(sprintf('/api/backup/destinations/%d', $existing->getKey()), $data);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'configuration',
            ]);

        $diskConfig = FilesystemConfiguration::find($response->json('configuration.id'))->getFilesystemConfig();

        $this->assertNull($diskConfig['root']);
    }

    /**
     * Tests destination is deleted.
     *
     * @return void
     */
    #[Test]
    public function delete_destination()
    {
        FilesystemConfiguration::factory()->ftp()->create();
        FilesystemConfiguration::factory()->sftp('password')->create();
        FilesystemConfiguration::factory()->sftp('key')->create();

        $fsConfig = FilesystemConfiguration::all()->random();

        $response = $this->actingAs($this->admin)->deleteJson(sprintf('/api/backup/destinations/%d', $fsConfig->getKey()));

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertNull(FilesystemConfiguration::find($fsConfig->getKey()));
        $this->assertMissingBackupDisks([$fsConfig->driver_name]);
        $this->assertStorageAdapterNotLoaded($fsConfig->driver_name);
    }

    /**
     * Tests destinations are deleted.
     */
    #[Test]
    public function delete_destinations(): void
    {
        FilesystemConfiguration::factory(2)->ftp()->create();
        FilesystemConfiguration::factory(2)->sftp('password')->create();
        FilesystemConfiguration::factory(2)->sftp('key')->create();

        $ids = FilesystemConfiguration::all()->map(fn($destination) => $destination->getKey());

        $response = $this->actingAs($this->admin)->deleteJson('/api/backup/destinations', ['destinations' => $ids]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
            ]);

        $found = FilesystemConfiguration::whereIn('id', $ids)->get();

        $this->assertEmpty($found);
    }

    /**
     * Assert storage adapter is loaded.
     *
     * @param  class-string  $adapterClass
     * @return void
     */
    protected function assertStorageAdapterLoaded(string $diskName, string $adapterClass)
    {
        try {
            $disk = Storage::disk($diskName);

            $this->assertInstanceOf(FilesystemAdapter::class, $disk);
            $this->assertInstanceOf($adapterClass, $disk->getAdapter());
        } catch (InvalidArgumentException $ex) {
            $this->fail($ex->getMessage());
        }
    }

    /**
     * Assert storage adapter is not loaded.
     *
     * @param  class-string  $adapterClass
     * @return void
     */
    protected function assertStorageAdapterNotLoaded(string $diskName)
    {
        $this->assertThrows(function () use ($diskName) {
            $disk = Storage::disk($diskName);

            $this->assertNull($disk);
        }, InvalidArgumentException::class);
    }

    /**
     * Asserts backup disk exists
     *
     * @param [type] $diskNames
     * @return void
     */
    protected function assertHasBackupDisks($diskNames, bool $strict = false)
    {
        $diskNames = Arr::wrap($diskNames);

        $disks = $this->app->make(Config::class)->backup->destination->disks;

        if ($strict) {
            sort($diskNames);
            sort($disks);

            $this->assertEquals($diskNames, $disks, 'One or more backup disks is missing.');
        } else {
            foreach ($diskNames as $diskName) {
                $this->assertContains($diskName, $disks);
            }
        }
    }

    /**
     * Asserts backup disk is missing
     *
     * @param [type] $diskNames
     * @return void
     */
    protected function assertMissingBackupDisks($diskNames)
    {
        $diskNames = Arr::wrap($diskNames);

        $disks = $this->app->make(Config::class)->backup->destination->disks;

        foreach ($diskNames as $diskName) {
            $this->assertNotContains($diskName, $disks);
        }
    }
}
