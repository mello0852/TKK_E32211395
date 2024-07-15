@extends('layouts.main')

@section('container')

<section class="pt-7 background-custom">
    <div class="container">
    <div class="row align-items-center">
        <div class="col-md-6 text-md-start text-center py-6">
        <h1 class="mb-2 fs-9 fw-bold">Monitoring Penggunaan Daya Listrik</h1>
        <p class="mb-6 lead text-secondary">Web Sistem Informasi Monitoring<br
            class="d-none d-xl-block" />Penggunan Daya Listrik Rumah Tangga Pascabayar<br
            class="d-none d-xl-block" />By Lutfi Ardiansyah</p>
        <div class="text-center text-md-start">
            <a class="btn btn-warning me-3 btn-lg" href="/dashboard" role="button">Mulai Monitoring</a>
        </div>
        </div>
        <div class="col-md-6 text-end"><img class="pt-7 pt-md-0 img-fluid" src="{{ asset('landpage/assets/img/hero/luffy.png') }}"
            alt="" /></div>
    </div>
    </div>
</section>

@endsection