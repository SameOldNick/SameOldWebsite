@extends('errors.layouts.full')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('You do not have permission to view this page.'))

