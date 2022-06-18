@extends('layout.app')

@section('meta')
@endsection

@section('title')
Create Product
@endsection

@section('styles')
<link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Product</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products') }}" class="text-muted">Product</a></li>
                        <li class="breadcrumb-item text-muted active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('products.insert') }}" name="form" id="form" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="shop_id">Shop Name</label>
                                <select name="shop_id" id="shop_id" class="form-control">
                                    <option value="" hidden>Select Shop</option>
                                    @if($data->isNotEmpty())
                                    @foreach($data AS $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <span class="kt-form__help error shop_id"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ @old('name') }}">
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control digit" placeholder="Plese enter price" value="{{ @old('price') }}">
                                <span class="kt-form__help error price"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="stock">Stock</label>
                                <input type="text" name="stock" id="stock" class="form-control digit" placeholder="Plese enter stock" value="{{ @old('stock') }}">
                                <span class="kt-form__help error stock"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="video">Video</label>
                                <input type="file" class=" dropify" id="video" name="video" accept="video/mp4,video/x-m4v,video/*" data-allowed-file-extensions='["mp4", "3gp" ,"mkv"]' data-max-file-size-preview="5M" data-show-remove="false">
                                <span class="kt-form__help error video"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn waves-effect waves-light btn-rounded btn-outline-primary">Submit</button>
                            <a href="{{ route('products') }}" class="btn waves-effect waves-light btn-rounded btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/dropify.min.js') }}"></script>
<script src="{{ asset('assets/js/promise.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#price').keyup(function(e) {
            if (/\D/g.test(this.value)) {
                this.value = this.value.replace(/\D/g, '');
            }
        });
        $('#stock').keyup(function(e) {
            if (/\D/g.test(this.value)) {
                this.value = this.value.replace(/\D/g, '');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        var drEvent = $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop video here or click',
                'remove': 'Remove',
                'error': 'Ooops, something wrong happended.'
            }
        });

        var dropifyElements = {};
        $('.dropify').each(function() {
            dropifyElements[this.id] = false;
        });

        drEvent.on('dropify.beforeClear', function(event, element) {
            console.log('asd');
            id = event.target.id;
            if (!dropifyElements[id]) {
                var url = "{!! route('products.remove.image') !!}";
                <?php if (isset($data) && isset($data->id)) { ?>
                    var id_encoded = "{{ base64_encode($data->id) }}";

                    Swal.fire({
                        title: 'Are you sure want delete this image?',
                        text: "",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                url: url,
                                type: "POST",
                                data: {
                                    id: id_encoded,
                                    _token: "{{ csrf_token() }}"
                                },
                                dataType: "JSON",
                                success: function(data) {
                                    if (data.code == 200) {
                                        Swal.fire('Deleted!', 'Deleted successfully.', 'success');
                                        dropifyElements[id] = true;
                                        element.clearElement();
                                    } else {
                                        Swal.fire('', 'Failed to delete', 'error');
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.fire('', 'Failed to delete', 'error');
                                }
                            });
                        }
                    });

                    return false;
                <?php } else { ?>
                    Swal.fire({
                        title: 'Are you sure want delete this video?',
                        text: "",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then(function(result) {
                        if (result.value) {
                            Swal.fire('Deleted!', 'Deleted successfully.', 'success');
                            dropifyElements[id] = true;
                            element.clearElement();
                        } else {
                            Swal.fire('Cancelled', 'Discard last operation.', 'error');
                        }
                    });
                    return false;
                <?php } ?>
            } else {
                dropifyElements[id] = false;
                return true;
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        var form = $('#form');
        $('.kt-form__help').html('');
        form.submit(function(e) {
            $('.help-block').html('');
            $('.m-form__help').html('');
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: new FormData($(this)[0]),
                dataType: 'json',
                async: false,
                processData: false,
                contentType: false,
                success: function(json) {
                    return true;
                },
                error: function(json) {
                    if (json.status === 422) {
                        e.preventDefault();
                        var errors_ = json.responseJSON;
                        $('.kt-form__help').html('');
                        $.each(errors_.errors, function(key, value) {
                            $('.' + key).html(value);
                        });
                    }
                }
            });
        });
    });
</script>
@endsection