<?php

namespace App\Components\SweetAlert;

use Illuminate\Contracts\Support\Arrayable;

class SweetAlertBuilder implements Arrayable
{
    protected $data;

    public function __construct()
    {
        $this->data = collect();
    }

    /**
     * Sets the title
     *
     * @param  string  $title  Title
     * @param  bool  $html  If true, HTML will be rendered. (default: true)
     * @return $this
     */
    public function title(string $title, bool $html = true)
    {
        return $this->setOption($html ? 'title' : 'titleText', $title);
    }

    /**
     * Sets the content
     *
     * @param  string  $content  Content
     * @param  bool  $html  If true, HTML will be rendered. (default: true)
     * @return $this
     */
    public function content(string $content, bool $html = true)
    {
        return $this->setOption($html ? 'html' : 'text', $content);
    }

    /**
     * Sets the content as text.
     *
     * @return $this
     */
    public function text(string $text)
    {
        return $this->content($text, false);
    }

    /**
     * Sets the content as HTML.
     *
     * @return $this
     */
    public function html(string $html)
    {
        return $this->content($html, true);
    }

    /**
     * Sets the icon
     *
     * @param  string  $icon  A string, 'warning', 'error', 'success', 'info', or 'question'
     * @param  bool  $html  If true, icon HTML will be rendered. (default: false)
     * @return $this
     */
    public function icon(string $icon, bool $html = false)
    {
        return $this->setOption($html ? 'iconHtml' : 'icon', $icon);
    }

    /**
     * Sets the footer
     *
     * @param  string  $footer  Footer content (HTML will be rendered)
     * @return $this
     */
    public function footer(string $footer)
    {
        return $this->setOption('footer', $footer);
    }

    /**
     * Sets if backdrop should be displayed
     *
     * @param  bool  $enabled  Whether to enable or disable backdrop. (default: true)
     * @return $this
     */
    public function backdrop(bool $enabled = true)
    {
        return $this->setOption('backdrop', $enabled);
    }

    /**
     * Sets input to display (or not)
     *
     * @param  string|false  $input  If string, the input prompt. If false, doesn't display input.
     * @return $this
     */
    public function input($input)
    {
        if (is_string($input)) {
            $this->setOption('input', $input);
        } elseif (! $input) {
            $this->unsetOption('input');
        }

        return $this;
    }

    /**
     * Gets value for option
     *
     * @param  mixed  $default  Default value if not found (default: null)
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->data->get($key, $default);
    }

    /**
     * Sets option for SweetAlert
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setOption(string $key, $value)
    {
        $this->data->put($key, $value);

        return $this;
    }

    /**
     * Unsets option for SweetAlert
     *
     * @return $this
     */
    public function unsetOption(string $key)
    {
        unset($this->data[$key]);

        return $this;
    }

    public function __get($key)
    {
        return $this->getOption($key);
    }

    public function __set($key, $value)
    {
        $this->setOption($key, $value);
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        $this->unsetOption($key);
    }

    /**
     * Get all of the Sweetalert options.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data->all();
    }
}
