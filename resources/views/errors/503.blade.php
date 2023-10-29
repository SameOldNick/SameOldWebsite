@extends('errors::layout')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable! The server is currently undergoing maintenance or is overloaded.'))
