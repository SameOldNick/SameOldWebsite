<?php

namespace App\Components\Analytics;

use App\Components\Analytics\Traits\CreatesCarbons;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class AnalyticsRequest extends FormRequest
{
    use CreatesCarbons;

    const DEFAULT_START = '-5 years';

    const DEFAULT_END = 'today';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // TODO: Ensure to/from are at least 30 days apart.
        return [
            'start' => [
                'sometimes',
                'date',
                'before:end',
            ],
            'end' => [
                'sometimes',
                'date',
                'after:start',
            ],
        ];
    }

    /**
     * Gets the start date/time
     *
     * @return Carbon
     */
    public function start()
    {
        return new Carbon($this->start ?? static::DEFAULT_START);
    }

    /**
     * Gets the end date/time
     *
     * @return Carbon
     */
    public function end()
    {
        return new Carbon($this->end ?? static::DEFAULT_END);
    }

    /**
     * Gets the interval unit
     *
     * @param integer $count
     * @return string
     */
    public function unit(int $count = 5)
    {
        return $this->determineIntervalUnit($this->start(), $this->end(), $count);
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        //
    }
}
