@extends('errors::layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests! You have exceeded the request limit.'))