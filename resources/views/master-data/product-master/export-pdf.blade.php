<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #0d6efd;
            color: white;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }

        .header-left {
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        .signature {
            margin-top: 40px;
            text-align: left;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <p class="header-left">Kevin Production</p>
    <p class="title">PT. KEVIN MAKMUR</p>
    <p class="title">Rekap Stock Produk Gudang</p>
    <p class="title">Periode November 2025</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Unit</th>
                <th>Category</th>
                <th>Description</th>
                <th>Stock</th>
                <th>Supplier</th>
                <th>Barang Masuk</th>
                <th>Barang Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->product_name }}</td>
                <td>{{ $p->unit }}</td>
                <td>{{ $p->type }}</td>
                <td>{{ $p->information }}</td>
                <td>{{ $p->qty }}</td>
                <td>{{ $p->producer }}</td>
                <td>{{ $p->created_at }}</td>
                <td>{{ $p->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <br><br>
        Diketahui oleh,<br><br><br><br>
        Kepala Logistik<br><br>
        _______________________<br>
    </div>

</body>

</html>