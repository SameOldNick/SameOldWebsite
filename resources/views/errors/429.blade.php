@extends('errors.layouts.full')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('You have exceeded the request limit.'))
