<table class="" id="table1">
    <thead>
        <tr class="text-center">
            <th>Tanggal</th>
            <th>No Akun</th>
            <th>Nama Akun</th>
            <th>Debet</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jurnal as $v)
            <tr>
                <td>{{ $v->tanggal }}</td>
                <td>{{ $v->no_reff }}</td>
                <td>{{ $v->akun->akun }}</td>
                <td>{{ $v->debet }}</td>
                <td>{{ $v->kredit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
