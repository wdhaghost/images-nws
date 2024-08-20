<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
    public function AllImages(){
        $images= Image::where('is_public',true) ->with('user')->get();
        return response()->json($images,200);
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
        $image = new Image();
        if($request->name){
            $image->name = $request->name;
        }

        $image->user_id = $request->user()->id;
        $image->path = $path;
        $image->is_public = true; 
        $image->save();

        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($url)
    {
        // Rechercher l'image dans la base de données via son URL unique
        $image = Image::where('url', $url)->with('user')->first();

        // Si l'image n'existe pas, renvoyer une erreur 404
        if (!$image) {
            return response()->json(['error' => 'Image inexistante.'], Response::HTTP_NOT_FOUND);
        }

        if (!$image->is_public) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Récupérer le chemin réel de l'image
        $path = $image->path;

        // Vérifier si le fichier existe sur le système de fichiers
        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'Image non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Retourner l'image en tant que fichier
        return response()->json($image, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $image = Image::where('id',$request->id)->first();
        if ($image){
            $image->is_public= $request->is_public;
            $image->save();
            return response()->json($image, 200);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // Vérifier que l'utilisateur est propriétaire de l'image
        $image = Image::findOrFail($request->id);

        
        if ($request->user()->id !== $image->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Supprimer l'image du stockage
        Storage::disk('public')->delete($image->path);
    
        // Supprimer l'entrée de la base de données
        $image->delete();
    
        return response()->json(['message' => 'Image deleted successfully']);
    }
    
}
