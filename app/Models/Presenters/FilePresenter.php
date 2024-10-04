<?php

namespace App\Models\Presenters;

use App\Models\File;
use Illuminate\Support\Facades\URL;

class FilePresenter extends Presenter
{
    public function __construct(
        protected readonly File $file
    ) {}

    /**
     * Creates public URL to access this file.
     * Note: The 'is_public' attribute needs to be true in order for this URL to work.
     *
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @param  bool  $withExt  If true, includes file extension in URL. (default: true)
     * @return string
     */
    public function publicUrl(bool $absolute = true, bool $withExt = true)
    {
        $url = URL::route('file', ['file' => $this->file], $absolute);

        if (! $withExt || ! ($ext = $this->file->path_info['extension'])) {
            return $url;
        }

        return sprintf('%s.%s', $url, $ext);
    }

    /**
     * Creates temporary signed URL to this file
     *
     * @param  int  $minutes  Minutes until URL expires (default: 30)
     * @param  bool  $absolute  If true, absolute URL is returned. (default: true)
     * @return string
     */
    public function privateUrl(int $minutes = 30, bool $absolute = true)
    {
        // Can't insert file extension because it will ruin integrity of signature
        return URL::temporarySignedRoute('file', $minutes * 60, ['file' => $this->file], $absolute);
    }
}
