@extends('layout.app')

@section('meta')
@endsection

@section('title')
View Shop
@endsection

@section('styles')
<link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Shop</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-muted">Shop</a></li>
                        <li class="breadcrumb-item text-muted active" aria-current="page">View</li>
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
                    <form name="form" id="form" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ $data->name ?? '' }}" disabled>
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control digit" placeholder="Plese enter email" value="{{ $data->email ?? '' }}" disabled>
                                <span class="kt-form__help error email"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="address">address</label>
                                <textarea name="address" id="address" class="form-control" cols="30" rows="10" placeholder="Plese enter address" value="{{ @old('address') }}" disabled>{{ $data->address ?? '' }}</textarea>
                                <span class="kt-form__help error address"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="image">Image</label>
                                <input type="file" class=" dropify" id="image" name="image" data-default-file="{{ $data->image }}" data-allowed-file-extensions="jpg png jpeg" data-max-file-size-preview="5M" data-show-remove="false" disabled> 
                                <span class="kt-form__help error image"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('shop') }}" class="btn waves-effect waves-light btn-rounded btn-outline-secondary">Back</a>
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
        var drEvent = $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop profile image here or click',
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
                var url = "{!! route('shop.remove.image') !!}";
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
                        title: 'Are you sure want delete this image?',
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
@endsection