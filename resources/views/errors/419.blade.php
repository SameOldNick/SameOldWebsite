@extends('errors::layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired! The page you were trying to access has expired.'))
@section('content')
    <div class="text-center">
        <p>{{ __('Page Expired! The page you were trying to access has expired.') }}</p>
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">{{ __('Back to Home') }}</a>
    </div>
@endsection
