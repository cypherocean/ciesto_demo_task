@extends('layout.app')

@section('meta')
@endsection

@section('title')
Shop
@endsection

@section('styles')
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
                        <li class="breadcrumb-item text-muted active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-5 align-self-center">

            <div class="customize-input float-right">
                <button type="button" class="btn waves-effect waves-light btn-rounded btn-outline-primary pull-right" data-toggle="modal" data-target="#exampleModal">
                    Import
                </button>
            </div>

            <div class="customize-input float-right">
                <a class="btn waves-effect waves-light btn-rounded btn-outline-primary pull-right" href="{{ route('shop.export') }}">Export</a>
            </div>

            <div class="customize-input float-right">
                <a class="btn waves-effect waves-light btn-rounded btn-outline-primary pull-right" href="{{ route('shop.create') }}">Add New</a>
            </div>

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('shop.import') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="file" class="form-control" name="file" placeholder="Plese select file to import">
                                <button type="submit"  class="btn waves-effect waves-light btn-rounded btn-outline-primary pull-right">Submit</button>
                            </form>
                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var datatable;

    $(document).ready(function() {
        if ($('#data-table').length > 0) {
            datatable = $('#data-table').DataTable({
                processing: true,
                serverSide: true,

                // "pageLength": 10,
                // "iDisplayLength": 10,
                "responsive": true,
                "aaSorting": [],
                // "order": [], //Initial no order.
                //     "aLengthMenu": [
                //     [5, 10, 25, 50, 100, -1],
                //     [5, 10, 25, 50, 100, "All"]
                // ],

                // "scrollX": true,
                // "scrollY": '',
                // "scrollCollapse": false,
                // scrollCollapse: true,

                // lengthChange: false,

                "ajax": {
                    "url": "{{ route('shop') }}",
                    "type": "POST",
                    "dataType": "json",
                    "data": {
                        _token: "{{csrf_token()}}"
                    }
                },
                "columnDefs": [{
                    //"targets": [0, 5], //first column / numbering column
                    "orderable": true, //set not orderable
                }, ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                    },
                ]
            });
        }
    });

    function change_status(object) {
        var id = $(object).data("id");
        var status = $(object).data("status");

        if (confirm('Are you sure?')) {
            $.ajax({
                "url": "{!! route('shop.change.status') !!}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    id: id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.code == 200) {
                        datatable.ajax.reload();
                        toastr.success('Record status changed successfully.', 'Success');
                    } else {
                        toastr.error('Failed to delete record.', 'Error');
                    }
                }
            });
        }
    }
</script>
@endsection