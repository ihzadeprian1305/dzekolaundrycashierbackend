<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link
            rel="stylesheet"
            href="{{ public_path('assets/css/style.css') }}"
            media="all"
        />
    </head>
    <body>
        <header class="clearfix">
            <div id="logo">
                <img src="{{ public_path('assets/images/image_wash_machine.png') }}" />
            </div>
            <h1>ID Transaksi : {{ $transaction->id }}</h1>
            <div id="company" class="clearfix">
                <div><b>Dzeko Laundry</b></div>
                <div>
                    Jl. Bendungan Wonogiri,<br />
                    Kel. Sumbersari, Kec. Lowokwaru
                </div>
                <div>Kota Malang</div>
                <div>0822 3336 1544 / 0821 2404 4740</div>
            </div>
            <div id="project">
                <div>
                    <span>Nama Pelanggan</span>
                    {{ $transaction->customers->name }}
                </div>
                <div>
                    <span>Nomor Telepon</span> {{ $transaction->customers->phone_number }}</div>
                <div><span>DATE</span> {{ $transaction->created_at }}</div>
            </div>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th class="service">PAKET</th>
                        <th class="desc">JENIS PAKET</th>
                        <th>HARGA PAKET</th>
                        <th>KUANTITAS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->transaction_items as $ti)
                        <tr>
                            <td class="service">{{ $ti->packages->name }}</td>
                            <td class="desc">{{ $ti->packages->type }}</td>
                            <td class="total">Rp. {{ $ti->packages->price }}</td>
                            <td class="qty">{{ $ti->quantity }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="grand total">TOTAL KESELURUHAN</td>
                        <td class="grand total">Rp. {{ $transaction->total_price }}</td>
                    </tr>
                </tbody>
            </table>
            <div id="notices">
                <div>Catatan :</div>
                <div class="notice">
                   - Cucian yang rusak karena bahan / kain bukan tanggung jawab kami.
                </div>
                <div class="notice">
                   - Komplain maksimal 5 jam setelah pengambilan, lewat dari waktu tersebut bukan tanggung jawab kami.
                </div>
                <div class="notice">
                   - Hilangnya benda berharga yang ditinggal di pakaian bukan jadi tanggung jawab kami.
                </div>
                <div class="notice">
                   - Pengambilan barang harus menunjukkan nota ini.
                </div>
            </div>
            <div class="name">
                {{ $transaction->customers->name }}
            </div>
        </main>
        <footer>
            Pembuat Transaksi oleh {{ $transaction->cb->user_data->name }} pada {{ $transaction->created_at }} <br>           
            Dzeko Laundry
        </footer>
    </body>
</html>
