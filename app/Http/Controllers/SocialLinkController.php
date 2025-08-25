<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SocialLinkController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->socialLinks);
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|url',
            'order' => 'required|integer',
            'icon' => 'nullable|image|max:2048',
        ]);


        $socialLink = new SocialLink();
        $socialLink->title = $validated['title'];
        $socialLink->link = $validated['link'];
        $socialLink->order = $validated['order'];
        $socialLink->user_id = Auth::id();  
        
        if ($request->hasFile('icon')) {
    
            $path = $request->file('icon')->store('social-icons', 'public');
            $socialLink->icon = $path;
        }

        $socialLink->save();

        return response()->json([
            'message' => 'Social link updated successfully.',
            'data' => $socialLink,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        Log::info('Beérkezett PUT kérés: ' . var_export($request->all(), true));
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|url',
            'order' => 'required|integer',
            'icon' => 'nullable|image|max:2048',
            'remove_icon' => 'nullable|boolean'
        ]);

        $social = SocialLink::findOrFail($id);
        $social->fill($data);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('social-icons', 'public');
            $social->icon = $path;
        }
          // Ha a felhasználó jelölte, hogy törölni kell az ikont
        elseif ($request->boolean('remove_icon')) {
            if ($social->icon && Storage::disk('public')->exists($social->icon)) {
                Storage::disk('public')->delete($social->icon);
            }
            $social->icon = null;
        }

        $social->save();

        return response()->json(['message' => 'Updated']);
    }


    public function destroy(SocialLink $socialLink)
    {
    
        $socialLink->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function updateOrder(Request $request)
    {
        $orderData = $request->input('order'); 

        foreach ($orderData as $id => $order) {
            SocialLink::where('id', $id)->update(['order' => $order]);
        }

        return response()->json(['status' => 'success']);
    }
}
