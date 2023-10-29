@extends('errors::layout')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Oops! The page you are looking for could not be found.'))
@section('content')
    <form action="{{ route('blog.search') }}" class="search-form mt-3">
        <div class="input-group">
            <input type="search" name="q" class="form-control" placeholder="{{ __('Search...') }}">
            <button type="submit" class="btn btn-secondary">{{ __('Search') }}</button>
        </div>
    </form>
@endsection
