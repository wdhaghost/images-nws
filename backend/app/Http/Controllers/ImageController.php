<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $images = $request->user()->images()->orderBy('created_at', 'desc')->get();

        return response()->json($images);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Enregistrer l'image sur le disque
        $path = $request->file('image')->store('images', 'public');

        // Créer l'entrée dans la base de données
        $image = Image::create([
            'user_id' => $request->user()->id,
            'url' => $path,
            'is_public' => true, // Par défaut l'image est publique
        ]);

        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $image = Image::where('id',$request->id)->first();
        if ($image){
            $image->is_public= $request->status;
            return response()->json();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Image $image)
    {
        // Vérifier que l'utilisateur est propriétaire de l'image
        if ($request->user()->id !== $image->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Supprimer l'image du stockage
        Storage::disk('public')->delete($image->url);
    
        // Supprimer l'entrée de la base de données
        $image->delete();
    
        return response()->json(['message' => 'Image deleted successfully']);
    }
    
}
