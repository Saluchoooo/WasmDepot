<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return response()->json($items);
    }

    public function submit(Request $request)
    {
        try {
            // Validació de dades
            Log::info('Request Data:', ['data' => $request->all()]);
            $request->validate([
                'title' => 'required|string|max:255',
                'sinopsis' => 'required|string',
                'description' => 'required|string',
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'web' => 'required|url',
                'github' => 'required|url',
                'categories' => 'required|string',
                'type' => 'required|string',
                'language' => 'nullable|string', 
                'supportedPlatform' => 'nullable|string', 
                'compilerToolchain' => 'nullable|string', 
                'creatorId' => 'required|string',
                'creatorName' => 'required|string',
            ]);

            $url = 'http://localhost:9000/tfgbucket/';
            
            // Maneig de la imatge (si existeix)
            if ($request->hasFile('image')) {
                $imageName = $request->file('image')->store('images', 'minio');
                $imagePath = $url . $imageName;
            } else {
                $imagePath = null;
            }

            $formData = new Item();
            $formData->title = $request->title;
            $formData->sinopsis = $request->sinopsis;
            $formData->description = $request->description;
            $formData->image_path = $imagePath;
            $formData->web = $request->web;
            $formData->github = $request->github;
            $formData->categories = $request->categories;
            $formData->type = $request->type;
            $formData->language = $request->language; 
            $formData->supportedPlatform = $request->supportedPlatform; 
            $formData->compilerToolchain = $request->compilerToolchain; 
            $formData->creatorId = $request->creatorId;
            $formData->creatorName = $request->creatorName;
            $formData->save();

            return response()->json(['message' => 'Item created successfully', 'itemId' => $formData->id], 201);

        } catch (\Exception $e) {
            Log::error('Error creating item', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Error creating item'], 500);
        }
    }

    public function saveInstallation(Request $request, $id)
    {
        try {
            Log::info('Request Data:', ['data' => $request->all()]);

            // Validació de dades
            $request->validate([
                'installation' => 'nullable|string',
                'file' => 'nullable|file|max:10240',
            ]);

            // Trobar l'element per ID
            $item = Item::findOrFail($id);

            // Actualitzar les instruccions d'instal·lació si arriben dades
            if ($request->filled('installation')) {
                $item->installation = $request->input('installation');
            }

            // Maneig del fitxer d'instal·lació si arriba un fitxer nou
            if ($request->hasFile('file')) {
                // Eliminar l'arxiu antic si existeix
                if ($item->file_path) {
                    Storage::disk('minio')->delete($item->file_path);
                }

                // Guardar el nou fitxer
                $url = 'http://localhost:9000/tfgbucket/';
                $fileName = $request->file('file')->store('files', 'minio');
                $filePath = $url . $fileName;
                $item->file_path = $filePath;
            }

            // Guardar els canvis al model
            $item->save();

            return response()->json(['message' => 'Installation information saved successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Error saving installation info', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Error saving installation information'], 500);
        }
    }

    public function saveScreenshots(Request $request, $id)
    {
        try {
            Log::info('Request Data:', ['data' => $request->all()]);

            $request->validate([
                'screenshot' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $item = Item::findOrFail($id);
            
            // Decodifiquem objecte json per afegir mes
            $screenshotPaths = $item->screenshots ? json_decode($item->screenshots, true) : [];

            $url = 'http://localhost:9000/tfgbucket/';

            // Recórrer totes les captures de pantalla pujades
            if ($request->hasFile('screenshot')) {
                $imageName = $request->file('screenshot')->store('screenshots', 'minio');
                $image_path = $url . $imageName;
                $screenshotPaths[] = $image_path;
            }

            // Actualitzar l'element amb les noves captures de pantalla
            $item->screenshots = json_encode($screenshotPaths);
            $item->save();
            return response()->json(['message' => 'Screenshots saved successfully', 'screenshots' => $screenshotPaths], 200);

        } catch (\Exception $e) {
            Log::error('Error saving screenshots', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error saving screenshots'], 500);
        }
    }

    public function saveBenchmark(Request $request, $id)
    {
        try {
            Log::info('Request Data:', ['data' => $request->all()]);

            // Validació de dades
            $request->validate([
                'benchmark' => 'required|string',
            ]);

            $item = Item::findOrFail($id);

            // Actualitzar el text del benchmark
            if ($request->has('benchmark')) {
                $item->benchmark = $request->input('benchmark');
            }

            $item->save();

            return response()->json(['message' => 'Benchmark text saved successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Error saving benchmark text', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error saving benchmark text'], 500);
        }
    }

    public function uploadBenchmark(Request $request, $id)
    {
        try {
            Log::info('Request Data:', ['data' => $request->all()]);

            $request->validate([
                'benchmark' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $item = Item::findOrFail($id);

            // Decodifiquem objecte json per afegir mes
            $benchmarkPaths = $item->benchmarks ? json_decode($item->benchmarks, true) : [];

            $url = 'http://localhost:9000/tfgbucket/';

            // Recórrer totes les imatges de benchmark pujades
            if ($request->hasFile('benchmark')) {
                $imageName = $request->file('benchmark')->store('benchmarks', 'minio');
                $image_path = $url . $imageName;
                $benchmarkPaths[] = $image_path;
            }

            // Actualitzar l'element amb les noves imatges de benchmark
            $item->benchmarks = json_encode($benchmarkPaths);
            $item->save();

            return response()->json(['message' => 'Benchmark images saved successfully', 'benchmarks' => $benchmarkPaths], 200);

        } catch (\Exception $e) {
            Log::error('Error saving benchmark images', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error saving benchmark images'], 500);
        }
    }

    public function show($id)
    {
        $item = Item::find($id);
        $item->screenshots = $item->screenshots ? json_decode($item->screenshots, true) : [];
        $item->benchmarks = $item->benchmarks ? json_decode($item->benchmarks, true) : [];
        return response()->json($item);
    }

    public function destroy($id)
    {
        Item::destroy($id);
        return response()->json(['message' => 'Repository item deleted']);
    }
}
