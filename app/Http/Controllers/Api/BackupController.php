<?php

namespace App\Http\Controllers\Api;

use App\Components\Websockets\Notifiers\JobStatusNotifier;
use App\Http\Controllers\Controller;
use App\Jobs\BackupJob;
use App\Models\Backup;
use App\Models\Collections\BackupCollection;
use App\Models\PrivateChannel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-manage-backups');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'show' => 'sometimes|in:successful,failed,not-exists,deleted',
        ]);

        /**
         * Since the where() method creates WHERE clauses for actual columns, we can't use it for appended attributes.
         * Instead, we can use afterQuery() to filter the models after their pulled.
         */
        $query = Backup::query()->afterQuery(function (BackupCollection $found) use ($request) {
            $collection = match ((string) $request->str('show')) {
                'successful' => $found->status('successful'),
                'failed' => $found->status('failed'),
                'not-exists' => $found->status('not-exists'),
                'deleted' => $found->status('deleted'),
                default => null,
            };

            /**
             * The keys need to be reset so they are in sequence (0,1,2...)
             * Passing the keys without being in sequence causes issues with pagination.
             * It also causes JS to treat the data as an object, not an array.
             */
            return ! is_null($collection) ? $collection->values() : null;
        });

        /*$query = match ((string) $request->str('show')) {
            'successful' => $query->filter(fn($model) => $model->status === 'successful')
        };*/

        /*switch ($request->str('show', 'both')) {
            case 'successful': {
                $query = $query->where('status', 'successful');

                break;
            }

            case 'failed': {
                $query = $query->where('status', 'failed');

                break;
            }

            default: {
                break;
            }
        }*/

        return $query->paginate();
    }

    /**
     * Display the specified resource.
     */
    public function show(Backup $backup)
    {
        return $backup;
    }

    /**
     * Generates download link to backup file.
     *
     * @return array
     */
    public function generateDownloadLink(Backup $backup)
    {
        $url = $backup->file->createPrivateUrl(30);

        return [
            'url' => $url,
        ];
    }

    /**
     * Performs a backup
     */
    public function performBackup(Request $request)
    {
        $request->validate([
            'only' => 'sometimes|in:database,files',
        ]);

        /**
         * The backup is run using a job.
         * This allows it to run asynchronously (so websocket can handle it).
         */
        $only = $request->str('only');

        $notifier = JobStatusNotifier::create($request->user())->openChannel();

        BackupJob::dispatch($notifier, [
            '--only-db' => $only->exactly('database'),
            '--only-files' => $only->exactly('files'),
        ]);

        return ['uuid' => (string) $notifier->getUuid()];
    }
}
