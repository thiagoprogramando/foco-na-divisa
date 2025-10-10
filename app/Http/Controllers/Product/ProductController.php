<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller {
    
    public function index(Request $request) {
        
        $query = Product::orderBy('name', 'ASC');

        if ($request->name) {
            $query->where('name', 'LIKE', "%{$request->name}%");
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->time) {
            $query->where('time', $request->time);
        }

        $totalViews     = $query->sum('views');
        $totalInvoices  = Invoice::whereIn('product_id', $query->pluck('id'))->count();
        $totalProducts  = $query->count(); 
        
        return view('app.Product.list-products', [
            'products'      => $query->paginate(10),
            'totalViews'    => $totalViews,
            'totalInvoices' => $totalInvoices,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function createForm() {
        return view('app.Product.create-product');
    }

    public function show($uuid) {

        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->route('products')->with('infor', 'Produto não encontrado!');
        }

        return view('app.Product.view-product', [
            'product' => $product
        ]);
    }

    public function store (Request $request) {

        $product                = new Product();
        $product->uuid          = Str::uuid();
        $product->name          = $request->name;
        $product->caption       = $request->caption;
        $product->description   = $request->description;
        $product->value         = $this->formatValue($request->value);
        $product->status        = $request->status ? 1 : 0;
        $product->type          = $request->type;
        $product->time          = $request->time;

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('product-images', 'public');
        }
        
        if ($product->save()) {
            return redirect()->route('product', ['uuid' => $product->uuid])->with('success', 'Produto cadastrado com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao cadastrar Produto, verifique os dados e tente novamente!');
    }

    public function update (Request $request, $uuid) {

        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não encontrado!'); 
        }

        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('caption')) {
            $product->caption = $request->caption;
        }
        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('value')) {
            $product->value = $this->formatValue($request->value);
        }
        if ($request->has('status')) {
            $product->status = $request->status;
        }
        if ($request->has('type')) {
            $product->type = $request->type;
        }
        if ($request->has('time')) {
            $product->time = $request->time;
        }
        if ($request->hasFile('image')) {
            
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('product-images', 'public');
        }

        if ($product->save()) {
            return redirect()->route('product', ['uuid' => $product->uuid])->with('success', 'Produto atualizado com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao atualizar Produto, verifique os dados e tente novamente!');
    }

    private function formatValue($valor) {
        
        $valor = preg_replace('/[^0-9,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valorFloat = floatval($valor);
    
        return number_format($valorFloat, 2, '.', '');
    }

    public function storeFile(Request $request, $uuid) {
        
        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não encontrado!'); 
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|max:5120',
        ], [
            'title.required' => 'O título é obrigatório.',
            'file.required'  => 'É necessário enviar um arquivo.',
            'file.file'      => 'O item enviado deve ser um arquivo válido.',
            'file.max'       => 'O arquivo não pode ter mais que 5MB.',
        ]);

        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Nenhum arquivo enviado!');
        }

        $path   = $request->file('file')->store('product-files', 'public');
        $url    = asset('storage/' . $path);

        $files = $product->files ?? [];

        $files[] = [
            'id'    => (string) Str::uuid(),
            'date'  => now()->toDateTimeString(),
            'title' => $request->title,
            'url'   => $url,
        ];

        $product->files = $files;
        
        if ($product->save()) {
            return back()->with('success', 'Arquivo adicionado com sucesso!');
        }

        return back()->with('infor', 'Não foi possível armazenar o Arquivo!');
    }

    public function destroyFile($uuid, $id) {

        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não encontrado!'); 
        }

        $files = collect($product->files ?? []);
        $fileToDelete = $files->firstWhere('id', $id);

        if ($fileToDelete && isset($fileToDelete['url'])) {
            
            $url        = $fileToDelete['url'];
            $publicPath = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

            if (Storage::disk('public')->exists($publicPath)) {
                Storage::disk('public')->delete($publicPath);
            }
        }

        $files = $files->reject(fn ($item) => $item['id'] === $id)->values()->toArray();
        $product->files = $files;
        $product->save();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    public function storePost(Request $request, $uuid) {
        $product = Product::where('uuid', $uuid)->first();

        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não encontrado!');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'nullable|string|max:250',
            'file'    => 'nullable|file|max:5120',
        ], [
            'title.required' => 'O título é obrigatório.',
            'title.max'      => 'O título não pode ter mais que 255 caracteres.',
            'message.max'    => 'A mensagem não pode ter mais que 250 caracteres.',
            'file.file'      => 'O item enviado deve ser um arquivo válido.',
            'file.max'       => 'O arquivo não pode ter mais que 5MB.',
        ]);

        $url = null;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('product-posts', 'public');
            $url = asset('storage/' . $path);
        }

        $posts = $product->posts ?? [];

        $posts[] = [
            'id'      => (string) Str::uuid(),
            'date'    => now()->toDateTimeString(),
            'title'   => $request->title,
            'message' => $request->message ?? '',
            'url'     => $url,
        ];

        $product->posts = $posts;

        if ($product->save()) {
            return back()->with('success', 'Post adicionado com sucesso!');
        }

        return back()->with('infor', 'Não foi possível armazenar o Post!');
    }

    public function destroyPost($uuid, $id) {
        
        $product = Product::where('uuid', $uuid)->first();
        if (!$product) {
            return redirect()->back()->with('infor', 'Produto não encontrado!');
        }

        $posts          = collect($product->posts ?? []);
        $postToDelete   = $posts->firstWhere('id', $id);

        if ($postToDelete && !empty($postToDelete['url'])) {

            $url        = $postToDelete['url'];
            $publicPath = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

            if (Storage::disk('public')->exists($publicPath)) {
                Storage::disk('public')->delete($publicPath);
            }
        }

        $posts = $posts->reject(fn($item) => $item['id'] === $id)->values()->toArray();
        $product->posts = $posts;
        $product->save();

        return back()->with('success', 'Post removido com sucesso!');
    }
}
