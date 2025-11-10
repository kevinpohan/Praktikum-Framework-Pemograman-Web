<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Imagick;
use App\Models\Product;
use App\Models\Supplier;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mengambil input dari request (Searching & Sorting)
        $search = $request->input('search');

        // Default sorting: kolom 'id' secara 'asc' (ascending)
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Daftar kolom yang diizinkan untuk sorting
        $allowedSorts = ['id', 'product_name', 'unit', 'type', 'information', 'qty', 'producer'];

        $query = Product::with('supplier');

        // Menerapkan Searching
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('producer', 'like', '%' . $search . '%');
            });
        }

        // Menerapkan Sorting
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $data = $query->paginate(4);
        //return $data;

        // Tambahkan parameter searching dan sorting ke link pagination
        $data->appends($request->only('search', 'sort_by', 'sort_direction'));

        return view("master-data.product-master.index-product", compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view("master-data.product-master.create-product", compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validasi input data
        $validasi_data = $request->validate([
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'information' => 'nullable|string',
            'qty' => 'required|integer',
            'producer' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        // Proses simpan data kedalam database
        Product::create($validasi_data);

        return redirect()->route('product-index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view("master-data.product-master.detail-product", compact("product"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findorFail($id);
        $suppliers = Supplier::all();
        return view("master-data.product-master.edit-product", compact("product", "suppliers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'information' => 'nullable|string',
            'qty' => 'required|integer|min:1',
            'producer' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'product_name' => $request->product_name,
            'unit' => $request->unit,
            'type' => $request->type,
            'information' => $request->information,
            'qty' => $request->qty,
            'producer' => $request->producer,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()->route('product-index')->with('success', 'Product update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Product::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('product-index')->with(
                'success',
                'Product berhasil dihapus.'
            );
        }
        return redirect()->route('product-index')->with('error', 'Product tidak ditemukan.');
    }


    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'recap-product.xlsx');
    }

    public function exportPDF()
    {
        $products = Product::all();

        $pdf = Pdf::loadView('master-data.product-master.export-pdf', compact('products'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-produk.pdf');
    }


    public function exportJPG()
    {
        $products = Product::all();

        // Generate PDF first
        $pdf = Pdf::loadView('master-data.product-master.export-pdf', compact('products'))
            ->setPaper('A4', 'landscape');

        // Save to storage temporary
        $pdfPath = storage_path('app/public/report_temp.pdf');
        file_put_contents($pdfPath, $pdf->output());

        // Convert first page of PDF to JPG
        $image = new Imagick();
        $image->setResolution(200, 200);
        $image->readImage($pdfPath . '[0]');
        $image->setImageFormat('jpg');

        $jpgPath = storage_path('app/public/report.jpg');
        $image->writeImage($jpgPath);

        return response()->download($jpgPath, 'laporan-produk.jpg');
    }
}
