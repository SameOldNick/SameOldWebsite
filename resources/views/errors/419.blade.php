@extends('errors.layouts.full')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('The page you were trying to access has expired.'))
@section('content')
    <div class="text-center">
        <p>{{ __('This may be caused by sending a request to the wrong URL scheme.') }}</p>
        <p>
            {{ __('Please ensure you\'re accessing :https instead of :http.', [
                'https' => str_replace('http://', 'https://', url()->previous()),
                'http' => str_replace('https://', 'http://', url()->previous())
            ]) }}
        </p>
    </div>
@endsection
