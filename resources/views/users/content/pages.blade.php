@extends('users.layouts.app')

@section('meta_title', $page->meta_title ?? 'Judul Default')
@section('meta_description', $page->meta_description ?? 'Deskripsi default')

@section('content')
<section class="container mx-auto px-4 py-12">
    <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Content -->
            <div class="lg:col-span-6">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200">
                    <div class="p-8 md:p-12">
                        <!-- Title -->
                        <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>

                        <!-- Content -->
                        <div class="ck-content prose max-w-none">
                            {!! $page->safe_content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
