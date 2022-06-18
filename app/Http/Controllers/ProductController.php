<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use Illuminate\Support\Facades\Storage;
use DataTables ,File ,DB;

class ProductController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $data = Product::select('products.id','products.name' ,'products.price' ,'products.stock','shop.name AS shop_name' ,'products.status')->leftjoin('shop' ,'products.shop_id' ,'shop.id')->orderBy('products.id' ,'desc')->get();

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($data){
                return ' <div class="btn-group">
                                <a href="'.route('products.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                    <i class="fa fa-eye"></i>
                                </a> &nbsp;
                                <a href="'.route('products.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                    <i class="fa fa-edit"></i>
                                </a> &nbsp;
                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bars"></i>
                                </a> &nbsp;
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="active" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Active</a></li>
                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="inactive" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Inactive</a></li>
                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                </ul>
                            </div>';
            })


            ->editColumn('status', function($data) {
                if($data->status == 'active')
                    return '<span class="badge badge-pill badge-success">Active</span>';
                else if($data->status == 'inactive')
                    return '<span class="badge badge-pill badge-warning">Inactive</span>';
                else if($data->status == 'deleted')
                    return '<span class="badge badge-pill badge-danger">Delete</span>';
                else
                    return '-';
            })

            ->rawColumns(['action' ,'status'])
            ->make(true);
        }
        return view('product.index');
    }

    public function create(Request $request){
        $data = Shop::where('status' ,'active')->get();
        return view('product.create')->with(['data' => $data]);
    }

    public function insert(ProductRequest $request){
        if ($request->ajax()) { return true; }
        $data = [
            'shop_id' => $request->shop_id,
            'name' => ucfirst($request->name),
            'price' => $request->price ?? NULL,
            'stock' => $request->stock ?? NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => auth()->user()->id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];

        if(!empty($request->file('video'))){
            $file = $request->file('video');
            $filenameWithExtension = $request->file('video')->getClientOriginalName();
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('video')->getClientOriginalExtension();
            $filenameToStore = time()."_".$filename.'.'.$extension;

            $data["video"] = $filenameToStore;
        }

        $process = Product::insertGetId($data);

        if($process){
            if(!empty($request->file('video'))){
                File::copy($request->file('video'), public_path('/uploads/product'.'/'.$filenameToStore));
            }
            return redirect()->route('products')->with('success', 'Record inserted successfully');
        }else{
            return redirect()->back()->with('error', 'Failed to insert record')->withInput();
        }
    }

    public function edit(Request $request){
        if(isset($request->id) && $request->id != '' && $request->id != null){
            $id = base64_decode($request->id);
        }else{
            return redirect()->route('products')->with('error', 'Something went wrong');
        }
        $path = URL('/uploads/product').'/';
        $shop = Shop::where(['status' => 'active'])->get();
        $data = Product::select('id' ,'name' ,'price' ,'stock' ,'shop_id' , 
                        DB::Raw("CASE
                            WHEN ".'video'." != '' THEN CONCAT("."'".$path."'".", ".'video'.")
                            ELSE null
                            END as video"))
                    ->where(['id' => $id])->first();

        return view('product.edit')->with(['data' => $data ,'shop' => $shop]);
    }

    public function update(ProductRequest $request){
        if ($request->ajax()) { return true; }
        
        $exst_rec = Product::where(['id' => $request->id])->first();
        
        $folder_to_upload = public_path().'/uploads/product/';
        if (!File::exists($folder_to_upload)){
            File::makeDirectory($folder_to_upload, 0777, true, true);
        }

        $data = [
            'shop_id' => $request->shop_id,
            'name' => ucfirst($request->name),
            'price' => $request->price ?? NULL,
            'stock' => $request->stock ?? NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];

        if(!empty($request->file('video'))){
            $file = $request->file('video');
            $filenameWithExtension = $request->file('video')->getClientOriginalName();
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('video')->getClientOriginalExtension();
            $filenameToStore = time()."_".$filename.'.'.$extension;

            $data["video"] = $filenameToStore;
        }else{
            $data["video"] = $exst_rec->video;
        }
        $process = Product::where(['id' => $request->id])->update($data);

        if($process){

            if(!empty($request->file('video'))){
                $file->move($folder_to_upload, $filenameToStore);
            }
            
            $file_path = null;
            if($exst_rec->video != null || $exst_rec->video != ''){
                $file_path = public_path().'/uploads/product/'.$exst_rec->video;
            }
                if(File::exists($file_path) && $file_path != ''){
                    @unlink($file_path);
                }
                return redirect()->route('products')->with('success', 'Record updated successfully');
        }else{
            return redirect()->back()->with('error', 'Failed to update record')->withInput();
        }
    }

    public function view(Request $request, $id = ''){
        if(isset($request->id) && $request->id != '' && $request->id != null){
            $id = base64_decode($request->id);
        }else{
            return redirect()->route('products')->with('error', 'Something went wrong');
        }
        $path = URL('/uploads/product').'/';
        $shop = Shop::where(['status' => 'active'])->get();
        $data = Product::select('id' ,'name' ,'price' ,'stock' ,'shop_id' , 
                        DB::Raw("CASE
                            WHEN ".'video'." != '' THEN CONCAT("."'".$path."'".", ".'video'.")
                            ELSE null
                            END as video"))
                    ->where(['id' => $id])->first();


        return view('product.view')->with(['data' => $data ,'shop' =>$shop]);
    }

    public function change_status(Request $request){
        if(!$request->ajax()){ exit('No direct script access allowed'); }
        // dd($request);
        if(!empty($request->all())){
            $id = base64_decode($request->id);
            $status = $request->status;

            $data = Product::where(['id' => $id])->first();

            if(!empty($data)){
                $product = Product::where(['id' => $id])->update(['status' => $status, 'updated_by' => auth()->user()->id]);
                if($product)
                    return response()->json(['code' => 200]);
                else
                    return response()->json(['code' => 201]);
            } else {
                return response()->json(['code' => 202]);
            }
        } else {
            return response()->json(['code' => 203]);
        }
    }


    public function remove_image(Request $request){
        if(!$request->ajax()){ exit('No direct script access allowed'); }

        if(!empty($request->all())){
            $id = base64_decode($request->id);
            $data = Shop::find($id);

            if($data){
                if($data->image != ''){
                    $file_path = public_path().'/uploads/shop/'.$data->image;

                    if(File::exists($file_path) && $file_path != ''){
                        if($data->image != 'default.png')
                            @unlink($file_path);
                    }

                    $update = Shop::where(['id' => $id])->limit(1)->update(['image' => null]);

                    if($update)
                        return response()->json(['code' => 200]);
                    else
                        return response()->json(['code' => 201]);
                }else{
                    return response()->json(['code' => 200]);
                }
            }else{
                return response()->json(['code' => 201]);
            }
        }else{
            return response()->json(['code' => 201]);
        }
    }

    public function import(Request $request){
        Excel::import(new ProductImport, $request->file('file')->store('files'));
        return redirect()->route('products')->with('success' ,'File Imported Successfully');
    }

    public function export(Request $request){
 
        $name = 'Product'.Date('YmdHis').'.xlsx';
        $excel = Excel::store(new ProductExport, $name, 'excel_store');
        if($excel){
            if(Storage::disk('excel_store')->exists($name)){
                $path = public_path().'/uploads/excel/'.$name;
                return Excel::download(new ProductExport, $name);
            }else{
                return redirect()->route('products')->with('error' ,'Faild to Export File');
            }
        }else{
            return redirect()->route('products')->with('error' ,'error');
        }
    }

}
