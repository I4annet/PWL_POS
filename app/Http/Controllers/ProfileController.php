<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
class ProfileController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Profil Pengguna',
            'list' => ['Home', 'Profil']
        ];  
        $page = (object) [
            'title' => 'Halaman profil pengguna'
        ];
    
        $activeMenu = 'profile'; 
    
        
        $user = Auth::user();
    
        return view('profile.index',
        [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'users' => $user,
            'activeMenu' => $activeMenu
        ]);
    }

    public function import() {
        return view('profile.import');
    }

    public function import_ajax(Request $request) {
            $user = Auth::user();
    
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048' // max 2MB
                ]);
    
                if ($user->image && Storage::disk('public')->exists('profile/' . $user->image)) {
                    if ($user->image !== 'default-user.png') {
                        Storage::disk('public')->delete('profile/' . $user->image);
                    }
                }    

                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('profile', $filename, 'public');
    
                /** @var \App\Models\UserModel $user */
                $user->image = $filename;
                $user->save();
    
                return response()->json(['success' => true, 'message' => 'Foto berhasil diunggah.']);
            }
    
            return response()->json(['success' => false, 'message' => 'Tidak ada file yang diunggah.']);
        }
    }
