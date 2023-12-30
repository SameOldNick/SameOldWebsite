@extends('errors.layouts.basic')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Something went wrong on my server.'))
