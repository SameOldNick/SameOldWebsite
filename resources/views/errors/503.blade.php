@extends('errors.layouts.basic')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('The server is currently undergoing maintenance or is overloaded.'))
