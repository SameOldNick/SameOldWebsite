<?php

namespace App\Traits\Console;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ModifiesModel
{
    /**
     * Pulls up a Tinker session with Model variable for user to use.
     *
     * @param string $class Full qualified class name of model
     * @param mixed $id ID of model to bring up
     * @param string|null $variableName Variable name to use. If null, camel case version of Model class name is used. (default: null)
     * @return int Return code
     */
    protected function modifyModel(string $class, $id, string $variableName = null)
    {
        $model = call_user_func([$class, 'find'], $id);

        if (is_null($model)) {
            $this->error(sprintf('No \'%s\' model with ID %d found.', $class, $id));

            return 1;
        }

        $variableName = $variableName ?? Str::camel(Str::shortName($class));

        $filename = Str::random().'.php';
        $contents =
            '<?php'.PHP_EOL.
            sprintf('$%s = %s::find(%s);', $variableName, $class, $id);

        Storage::put($filename, $contents);

        $this->info(sprintf('Use the $%s variable to perform CRUD operations.', $variableName));

        $this->call('tinker', ['include' => [Storage::path($filename)]]);

        Storage::delete($filename);

        return 0;
    }
}
