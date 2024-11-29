<?php

namespace App\Http\Controllers\Api\Backup;

use App\Http\Controllers\Controller;
use App\Models\BackupConfig;
use App\Models\FilesystemConfiguration;
use App\Models\FilesystemConfigurationFTP;
use App\Models\FilesystemConfigurationSFTP;
use App\Rules\Slugified;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Backup\Config\Config;

class BackupDestinationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-manage-backups');
    }

    /**
     * List all configurations
     *
     * @return void
     */
    public function index(Config $backupConfig)
    {
        // Pull disks indirectly through Spatie Backup config
        $enabled = $backupConfig->backup->destination->disks;

        return response()->json(FilesystemConfiguration::all()->map(fn (FilesystemConfiguration $config) => [
            'enable' => in_array($config->driver_name, $enabled),
            ...$config->toArray(),
        ]));
    }

    /**
     * Store a new configuration
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'enable' => 'required|boolean',
            'name' => [
                'required',
                'string',
                'max:255',
                new Slugified,
                Rule::unique(FilesystemConfiguration::class),
            ],
            'type' => 'required|in:ftp,sftp',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'auth_type' => 'nullable|required_if:type,sftp|string|in:password,key',
            'password' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'ftp' || ($request->type === 'sftp' && $request->auth_type === 'password')),
                'string',
                'max:255',
            ],
            'root' => 'nullable|string|max:255',
            'private_key' => [
                'nullable',
                Rule::requiredIf(fn () => $request->type === 'sftp' && $request->auth_type === 'key'),
                'string',
            ],
            'passphrase' => 'nullable|string|max:255',
            'extra' => 'nullable|array',
        ]);

        $config = match ($request->type) {
            'ftp' => FilesystemConfigurationFTP::create($request->only([
                'host',
                'port',
                'username',
                'password',
                'root',
                'extra',
            ])),
            'sftp' => FilesystemConfigurationSFTP::create($request->only([
                'host',
                'port',
                'username',
                'password',
                'private_key',
                'passphrase',
                'root',
                'extra',
            ])),
            default => null
        };

        // The validator shouldn't allow this, but just in case.
        if (! $config) {
            return response()->json(['message' => 'Type is invalid.'], 500);
        }

        $fsConfig = new FilesystemConfiguration([
            'name' => $request->name,
            'disk_type' => $request->type,
        ]);

        $fsConfig->configurable()->associate($config);

        $fsConfig->save();

        if ($request->boolean('enable')) {
            $this->enableDisk($fsConfig->driver_name);
        }

        return response()->json([
            'message' => 'Backup destination created successfully.',
            'configuration' => $fsConfig,
        ], 201);
    }

    /**
     * Show a specific configuration
     *
     * @return mixed
     */
    public function show(Config $backupConfig, FilesystemConfiguration $destination)
    {
        $config = $destination->configurable;

        if (! $config) {
            return response()->json(['message' => 'Configuration not found.'], 404);
        }

        // Pull disks indirectly through Spatie Backup config
        $enabled = $backupConfig->backup->destination->disks;

        return [
            'enable' => in_array($config->driver_name, $enabled),
            ...$destination->toArray(),
        ];
    }

    /**
     * Update an existing configuration
     *
     * @return mixed
     */
    public function update(Request $request, FilesystemConfiguration $destination)
    {
        $request->validate([
            'enable' => 'nullable|boolean',
            'name' => [
                'nullable',
                'string',
                'max:255',
                new Slugified,
                Rule::unique(FilesystemConfiguration::class)->ignore($destination),
            ],
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'auth_type' => 'nullable|string|in:password,key',
            'password' => [
                'nullable',
                'required_if:auth_type,password',
                'string',
                'max:255',
            ],
            'private_key' => [
                'nullable',
                'required_if:auth_type,key',
                'string',
            ],
            'passphrase' => 'nullable|string|max:255',
            'root' => 'nullable|string|max:255',

            'extra' => 'nullable|array',
        ]);

        $config = $destination->configurable;

        if (! $config) {
            return response()->json(['message' => 'Backup destination not found.'], 404);
        }

        $input = $request->except(['enable']);

        // Only include enable if it exists
        if ($request->has('enable')) {
            // Convert it to a boolean
            $input['enable'] = $request->boolean('enable');
        }

        $this->performUpdate($destination, $input);

        return [
            'message' => 'Backup destination updated successfully.',
            'configuration' => $destination,
        ];
    }

    /**
     * Updates multiple configurations
     *
     * @return mixed
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'destinations' => 'required|array',
            'destinations.*.id' => [
                'required',
                'numeric',
                Rule::exists(FilesystemConfiguration::class),
            ],
            'destinations.*.enable' => 'nullable|boolean',
            'destinations.*.name' => Rule::forEach(fn (?string $value, string $attribute) => [
                'nullable',
                'string',
                'max:255',
                new Slugified,
                Rule::unique(FilesystemConfiguration::class)->ignore($value, 'name'),
            ]),
            'destinations.*.host' => 'nullable|string|max:255',
            'destinations.*.port' => 'nullable|integer|min:1|max:65535',
            'destinations.*.username' => 'nullable|string|max:255',
            'destinations.*.auth_type' => 'nullable|string|in:password,key',
            'destinations.*.password' => [
                'nullable',
                'required_if:auth_type,password',
                'string',
                'max:255',
            ],
            'destinations.*.private_key' => [
                'nullable',
                'required_if:auth_type,key',
                'string',
            ],
            'destinations.*.passphrase' => 'nullable|string|max:255',
            'destinations.*.root' => 'nullable|string|max:255',

            'destinations.*.extra' => 'nullable|array',
        ]);

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validated['destinations'] as $data) {
                $destination = FilesystemConfiguration::find($data['id']);

                $this->performUpdate($destination, Arr::except($data, 'id'));
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to update destinations.'], 500);
        }

        return [
            'message' => 'Backup destinations updated successfully.',
        ];
    }

    /**
     * Delete a configuration
     *
     * @return mixed
     */
    public function destroy(FilesystemConfiguration $destination)
    {
        $this->performDestroy($destination);

        return ['message' => 'Backup destination deleted successfully.'];
    }

    /**
     * Delete multiple configurations
     *
     * @return mixed
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'destinations' => 'required|array',
            'destinations.*' => [
                'required',
                'numeric',
                Rule::exists(FilesystemConfiguration::class, 'id'),
            ],
        ]);

        // Use a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            foreach ($validated['destinations'] as $id) {
                $destination = FilesystemConfiguration::find($id);

                $this->performDestroy($destination);
            }

            // Commit the transaction if all updates succeed
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction if anything fails
            DB::rollBack();

            return response()->json(['error' => 'Failed to delete destinations.'], 500);
        }

        return ['message' => 'Backup destinations deleted successfully.'];
    }

    /**
     * Enables disk configuration
     *
     * @return void
     */
    protected function enableDisk(string $diskName)
    {
        $existing = BackupConfig::where('key', 'backup_disks')->first();

        if ($existing) {
            $value = ! Str::contains($existing->value, $diskName) ? $existing->value.';'.$diskName : $existing->value;
        } else {
            $value = $diskName;
        }

        BackupConfig::updateOrCreate(
            ['key' => 'backup_disks'],
            ['value' => Str::trim($value, ';')]
        );
    }

    /**
     * Disables disk configuration
     *
     * @return void
     */
    protected function disableDisk(string $diskName)
    {
        $existing = BackupConfig::where('key', 'backup_disks')->first();

        if ($existing) {
            $disks = array_diff(explode(';', $existing->value), [$diskName]);

            BackupConfig::where('key', 'backup_disks')->update(
                ['value' => implode(';', $disks)]
            );
        }
    }

    /**
     * Updates a configuration
     *
     * @return FilesystemConfiguration
     */
    protected function performUpdate(FilesystemConfiguration $destination, array $input)
    {
        $data = Arr::only($input, [
            'host',
            'port',
            'username',
            'passphrase',
            'extra',
        ]);

        if (Arr::has($input, 'name')) {
            $destination->name = Arr::get($input, 'name');

            $destination->save();
        }

        $diskType = $destination->disk_type;
        $authType = Arr::get($input, 'auth_type');

        if (
            Arr::has($input, 'password') && (
                $authType &&
                ($diskType === 'ftp' || ($diskType === 'sftp' && $authType === 'password'))
            )
        ) {
            $data['private_key'] = null;
            $data['passphrase'] = null;
            $data['password'] = Arr::get($input, 'password');
        } elseif (
            Arr::has($input, 'private_key') && (
                $authType && $diskType === 'sftp' && $authType === 'key'
            )
        ) {
            $data['password'] = null;
            $data['private_key'] = Arr::get($input, 'private_key');
            $data['passphrase'] = Arr::get($input, 'passphrase');
        }

        if ($diskType === 'ftp' && Arr::has($input, 'root')) {
            $data['root'] = Arr::get($input, 'root');
        }

        $destination->configurable->update($data);

        if (Arr::has($input, 'enable')) {
            if ((bool) Arr::get($input, 'enable')) {
                $this->enableDisk($destination->driver_name);
            } else {
                $this->disableDisk($destination->driver_name);
            }
        }

        return $destination;
    }

    /**
     * Removes a configuration
     *
     * @return void
     */
    protected function performDestroy(FilesystemConfiguration $destination)
    {
        $destination->configurable->delete();
        $destination->delete();
    }
}
