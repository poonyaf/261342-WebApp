<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function updateProfilePhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  // ✅ แก้
    ]);

    $user = Auth::user();

    if ($user->image && File::exists(storage_path('app/profile_photos/' . $user->image))) {
        File::delete(storage_path('app/profile_photos/' . $user->image));
    }

    $fileName = time() . '_' . $user->id . '_' . $request->file('photo')->getClientOriginalName();  // ✅ แก้
    $filePath = $request->file('photo')->storeAs('profile_photos', $fileName, 'local');  // ✅ แก้

    $user->image = $fileName;
    $user->save();

    return redirect()->route('profile.edit')->with('status', 'profile-photo-updated');
}

    public function showProfilePhoto($filename)
    {
        // Step 1: Get the currently authenticated user
        $user = auth()->user();

        // Step 2: Check if the user is trying to access their own photo
        if ($user->image!== $filename) {
            abort(403); // Prevent access to others' photos
        }

        // Step 3: Construct the file path
        $path = storage_path('app/private/profile_photos/' . $filename);

        // Step 4: Check if the file exists at the specified path
        if (!File::exists($path)) {
            abort(404);
        }

        // Step 5: Return the file response to the browser
        return response()->file($path);
    }

    //not sure to add birthdate functions here
    public function UpdateBirthdate(Request $request)
    {
        $request->validate([
            'birthdate' => 'nullable|date',
        ]);

        $user = Auth::user();
        $user->birthdate = $request->input('birthdate');
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'birthdate-updated');
    }
    public function ShowBirthdateForm()
    {
        $user = Auth::user();
        return view('profile.partials.update-birthdate-form', ['user' => $user]);
    }
    public function showBio()
{
    $user = Auth::user(); // Retrieve the currently authenticated user
    $bio = $user->bio; // Access the related bio for the user via function bio()
    return view('profile.show-bio', compact('user', 'bio')); //Pass both user and bio data to the Blade view
}
public function updateBio(Request $request)
{
    $user = Auth::user();
    $bio = $user->bio;

    $request->validate([
        'bio' => 'required|string',
    ]);

    if ($bio) {
        $bio->update([
            'bio' => $request->input('bio'),
        ]);
    } else {
        $user->bio()->create([
            'bio' => $request->input('bio'),
        ]);
    }

    return redirect()->route('profile.show-bio')
                    ->with('status', 'Bio updated successfully!');
}

}
