{{-- resources/views/auth/verify-email.blade.php --}}

@extends('layouts.main')

@section('container')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                </div>
            @endif

            <div class="alert alert-warning" role="alert">
                {{ __('Sebelum melanjutkan, periksa email Anda untuk tautan verifikasi.') }}
                {{ __('Jika Anda tidak menerima email') }},
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('klik di sini untuk meminta lagi') }}</button>.
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
