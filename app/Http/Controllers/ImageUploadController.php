<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\ImageUpload;
use Illuminate\Support\Facades\Storage;
use Auth;
use Cloudinary;


class ImageUploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $username = $user->name;

        $folder = $username;
        if($request->hasFile('image')){
            Cloudinary::uploadApi();
            $result =  $request->file('image')->storeOnCloudinary($folder)->getSecurePath();
        }else{
                    return back()->with('error', 'Please select a file to upload');
        }
        
        // $folder = $username;
        // $result = $request->file('image')->storeOnCloudinary($folder, ['visibility' => 'public']);
        // $uploadedImage = $result->getPath();
        $imageUpload = new ImageUpload();
        $imageUpload->user_id = Auth()->user()->id;
        $imageUpload->image = $result;
        $imageUpload->save();

        return redirect('images')->with('success', 'Image uploaded successfully');
    }

    public function showImages(Request $request){
        $images = ImageUpload::where('user_id', Auth::id())->get();
        
        return view('images', compact('images'));
    }
    
    public function moveImages(Request $request){
        $image_url = $request->input('ImageUrl');
    
        $downloadedImage = file_get_contents($image_url);
    
        $s3Bucket = 'myawsfileup';
        $s3Path = 'receipts/' . basename($image_url);
        Storage::disk('s3')->put($s3Path, $downloadedImage);
        
        $user = auth()->user();
        $imagePath =  explode('/',$image_url);
        $images = str_replace('.jpg','',$imagePath[8]);
        try {
            $result = cloudinary()->uploadApi()->destroy("zali8xbki1igdclbq25t");
            dd($result);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
        $images = ImageUpload::where('user_id', Auth::id())->get();

        return view('images', compact('images'))->with('success', 'Image Moved Successfully');


    }


}
