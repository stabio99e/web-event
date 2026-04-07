@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition  ">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Tambah Halaman</h1>
                <p></p>
            </div>

            <div class="row">

                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <form method="POST" action="{{ route('admin.pages.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Title</label>
                                    <input type="text" name="title" value="{{ old('title') }}"
                                        class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Content</label>
                                    <textarea id="editor" name="content" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                                        class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Meta Description</label>
                                    <input type="text" name="meta_description" value="{{ old('meta_description') }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>


            </div>
        </div>

    </div><!-- End Container-->
    </div><!-- End Content-->
@endsection
@section('script')
<script src="{{ asset('assets/modules/ckeditor/ckeditor.js') }}"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            removePlugins: ['ImageUpload', 'MediaEmbed', 'Table', 'TableToolbar'],
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection
