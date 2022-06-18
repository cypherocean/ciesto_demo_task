<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopRequest;
use App\Models\Shop;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopImport;
use App\Exports\ShopExport;
use Illuminate\Support\Facades\Storage;
use DataTables ,File ,DB;

class ShopController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $data = Shop::orderBy('id' ,'desc')->get();

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($data){
                return ' <div class="btn-group">
                                <a href="'.route('shop.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                    <i class="fa fa-eye"></i>
                                </a> &nbsp;
                                <a href="'.route('shop.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
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

            ->editColumn('image', function($data) {
                $image_name = strval( $data->name ); 

                if($data->image != null || $data->image != '')
                    $image = url('uploads/shop').'/'.$data->image;
                else
                    $image = url('uploads/shop').'/default.png';
                
                return "<img onclick='open_image(this)' data-name='".$image_name."' data-id=".$image." src='$image' style='height: 30px; width: 30px'>";
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

            ->rawColumns(['action', 'image' ,'status'])
            ->make(true);
        }
        return view('shop.index');
    }

    public function create(Request $request){
        return view('shop.create');
    }

    public function insert(ShopRequest $request){
        if ($request->ajax()) { return true; }

        $data = [
            'name' => ucfirst($request->name),
            'address' => $request->address ?? NULL,
            'email' => $request->email ?? NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => auth()->user()->id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];

        if(!empty($request->file('image'))){
            $file = $request->file('image');
            $filenameWithExtension = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $filenameToStore = time()."_".$filename.'.'.$extension;

            $data["image"] = $filenameToStore;
        }else{
            $data["image"] = 'default.png';
        }

        $process = Shop::insertGetId($data);

        if($process){
            if(!empty($request->file('image'))){
                File::copy($request->file('image'), public_path('/uploads/shop'.'/'.$filenameToStore));
            }
            return redirect()->route('shop')->with('success', 'Record inserted successfully');
        }else{
            return redirect()->back()->with('error', 'Failed to insert record')->withInput();
        }
    }

    public function edit(Request $request){
        if(isset($request->id) && $request->id != '' && $request->id != null){
            $id = base64_decode($request->id);
        }else{
            return redirect()->route('shop')->with('error', 'Something went wrong');
        }
        $path = URL('/uploads/shop').'/';
        $data = Shop::select('id' ,'name' ,'address' ,'email' , 
                        DB::Raw("CASE
                            WHEN ".'image'." != '' THEN CONCAT("."'".$path."'".", ".'image'.")
                            ELSE CONCAT("."'".$path."'".", 'default.png')
                            END as image"))
                    ->where(['id' => $id])->first();

        return view('shop.edit')->with(['data' => $data]);
    }

    public function update(ShopRequest $request){
        if ($request->ajax()) { return true; }
        
        $exst_rec = Shop::where(['id' => $request->id])->first();
        
        $folder_to_upload = public_path().'/uploads/shop/';
        if (!File::exists($folder_to_upload)){
            File::makeDirectory($folder_to_upload, 0777, true, true);
        }

        $data = [
            'name' => ucfirst($request->name),
            'address' => $request->address ?? NULL,
            'email' => $request->email ?? NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];

        if(!empty($request->file('image'))){
            $file = $request->file('image');
            $filenameWithExtension = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $filenameToStore = time()."_".$filename.'.'.$extension;

            $data["image"] = $filenameToStore;
        }else{
            $data["image"] = $exst_rec->image;
        }
        $process = Shop::where(['id' => $request->id])->update($data);

        if($process){

            if(!empty($request->file('image'))){
                $file->move($folder_to_upload, $filenameToStore);
            }
            
            if($exst_rec->image != null || $exst_rec->image != ''){
                $file_path = public_path().'/uploads/shop/'.$exst_rec->image;
            }
                if(File::exists($file_path) && $file_path != ''){
                    if($exst_rec->image != 'default.png')
                    @unlink($file_path);
                }
                return redirect()->route('shop')->with('success', 'Record updated successfully');
        }else{
            return redirect()->back()->with('error', 'Failed to update record')->withInput();
        }
    }

    public function view(Request $request, $id = ''){
        if(isset($request->id) && $request->id != '' && $request->id != null){
            $id = base64_decode($request->id);
        }else{
            return redirect()->route('shop')->with('error', 'Something went wrong');
        }
        $path = URL('/uploads/shop').'/';
        $data = Shop::select('id' ,'name' ,'address' ,'email' , 
                        DB::Raw("CASE
                            WHEN ".'image'." != '' THEN CONCAT("."'".$path."'".", ".'image'.")
                            ELSE CONCAT("."'".$path."'".", 'default.png')
                            END as image"))
                    ->where(['id' => $id])->first();


        return view('shop.view')->with(['data' => $data]);
    }

    public function change_status(Request $request){
        if(!$request->ajax()){ exit('No direct script access allowed'); }

        if(!empty($request->all())){
            $id = base64_decode($request->id);
            $status = $request->status;

            $data = Shop::where(['id' => $id])->first();

            if(!empty($data)){
                $shop = Shop::where(['id' => $id])->update(['status' => $status, 'updated_by' => auth()->user()->id]);
                if($shop)
                    return response()->json(['code' => 200]);
                else
                    return response()->json(['code' => 201]);
            } else {
                return response()->json(['code' => 201]);
            }
        } else {
            return response()->json(['code' => 201]);
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
        Excel::import(new ShopImport, $request->file('file')->store('files'));
        return redirect()->route('shop')->with('success' ,'File Imported Successfully');
    }

    public function export(Request $request){
 
        $name = 'Shop'.Date('YmdHis').'.xlsx';
        $excel = Excel::store(new ShopExport, $name, 'excel_store');
        if($excel){
            if(Storage::disk('excel_store')->exists($name)){
                $path = public_path().'/uploads/excel/'.$name;
                return Excel::download(new ShopExport, $name);
            }else{
                return redirect()->route('shop')->with('error' ,'Faild to Export File');
            }
        }else{
            return redirect()->route('shop')->with('error' ,'error');
        }
    }


}
