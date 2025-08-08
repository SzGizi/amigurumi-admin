<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Log;

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
        ]);

        $social = SocialLink::findOrFail($id);
        $social->fill($data);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('social-icons', 'public');
            $social->icon = $path;
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
