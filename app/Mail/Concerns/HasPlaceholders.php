<?php

namespace App\Mail\Concerns;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

trait HasPlaceholders
{
    protected function getDefaultPlaceholders() {
        $dateTime = app(Kernel::class)->requestStartedAt() ?? now();

        return [
            'date-time' => $dateTime->toIso8601String(),
            'user-agent' => request()->userAgent(),
            'ip-address' => request()->ip()
        ];
    }

    /**
     * Builds the tags for the templates.
     *
     * @param Request $request
     * @param array $extras Extra tags
     * @return array
     */
    protected function buildPlaceholders(array $placeholders, bool $applyDefault = true) {
        $default = $applyDefault ? $this->getDefaultPlaceholders() : [];

        return [...$default, ...$placeholders];
    }

    /**
     * Finds and replaces tags
     *
     * @param array $tags
     * @param string $original
     * @return string
     */
    private function fillPlaceholders(array $placeholders, string $original) {
        $formatted = $original;

        foreach ($placeholders as $placeholder => $value) {
            $search = sprintf('[%s]', $placeholder);
            $formatted = str_replace($search, $value, $formatted);
        }

        return $formatted;
    }
}
