@extends('errors.layouts.full')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'You do not have permission to access this page.'))

