@extends('admin.layouts.app')

@section('content')
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Edit Halaman</h1>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <form method="POST" action="{{ route('admin.pages.update', $page->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <div class="mb-3">
                                    <label>Title</label>
                                    <input type="text" name="title" value="{{ old('title', $page->title) }}"
                                        class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Content</label>
                                    <textarea id="editor" name="content" class="form-control @error('content') is-invalid @enderror">{{ old('content', $page->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title"
                                        value="{{ old('meta_title', $page->meta_title) }}" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Meta Description</label>
                                    <input type="text" name="meta_description"
                                        value="{{ old('meta_description', $page->meta_description) }}" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Order</label>
                                    <input type="number" name="order" value="{{ old('order', $page->order) }}"
                                        class="form-control">
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published"
                                        value="1"
                                        {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        Terbitkan Halaman
                                    </label>
                                </div>


                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
