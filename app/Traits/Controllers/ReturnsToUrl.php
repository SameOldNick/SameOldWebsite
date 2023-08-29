<?php

namespace App\Traits\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

trait ReturnsToUrl
{
    /**
     * Checks if can return to URL
     *
     * @param Request $request
     * @param string|null $returnUrl URL to return to. If null, return value of getReturnUrl is used. (default: null)
     * @return bool
     */
    protected function canReturnTo(Request $request, string $returnUrl = null)
    {
        $returnUrl = $returnUrl ?? $this->getReturnUrl($request);
        $root = $this->getUrlGenerator()->formatRoot($this->getUrlGenerator()->formatScheme());

        return ! $request->wantsJson() && Str::startsWith($returnUrl, $root);
    }

    /**
     * Safely returns to URL (by first checking if can return to URL)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|null Redirect response or null (if unsafe)
     */
    protected function returnToSafeResponse(Request $request)
    {
        $returnUrl = $this->getReturnUrl($request);

        return is_string($returnUrl) && $this->canReturnTo($request, $returnUrl) ? $this->returnToResponse($returnUrl) : null;
    }

    /**
     * Generates redirect response to return URL
     *
     * @param string $returnUrl
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function returnToResponse(string $returnUrl)
    {
        return redirect($returnUrl);
    }

    /**
     * Gets the URL to return to
     *
     * @param Request $request
     * @return string|null
     */
    protected function getReturnUrl(Request $request)
    {
        return $request->get('return_url');
    }

    /**
     * Gets URL Generator instance
     *
     * @return UrlGenerator
     */
    protected function getUrlGenerator()
    {
        return app(UrlGenerator::class);
    }
}
