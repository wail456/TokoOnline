<!DOCTYPE html>
<html>
<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<form id="ongkirForm">
    <select id="province">
        <option value="">Pilih Provinsi</option>
    </select>

    <select id="city">
        <option value="">Pilih Kota</option>
    </select>

    <input type="number" id="weight" placeholder="Berat (gram)">

    <select id="courier">
        <option value="">Pilih Kurir</option>
        <option value="jne">JNE</option>
        <option value="tiki">TIKI</option>
        <option value="pos">POS Indonesia</option>
    </select>

    <button type="submit">Cek Ongkir</button>
</form>

<div id="result"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ================= PROVINCE =================
    fetch('/provinces')
        .then(res => res.json())
        .then(data => {
            let provinsi = data.data;
            let select = document.getElementById('province');

            provinsi.forEach(p => {
                let opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                select.appendChild(opt);
            });
        });

    // ================= CITY =================
    document.getElementById('province').addEventListener('change', function () {
        let provinceId = this.value;

        fetch(`/cities?province_id=${provinceId}`)
            .then(res => res.json())
            .then(data => {
                let kota = data.data;
                let select = document.getElementById('city');
                select.innerHTML = '<option>Pilih Kota</option>';

                kota.forEach(c => {
                    let opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    select.appendChild(opt);
                });
            });
    });

    // ================= ONGKIR =================
    document.getElementById('ongkirForm').addEventListener('submit', function(e) {
        e.preventDefault();

        fetch('/cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                origin: 501, // ganti sesuai kota asal
                destination: document.getElementById('city').value,
                weight: document.getElementById('weight').value,
                courier: document.getElementById('courier').value
            })
        })
        .then(res => res.json())
        .then(data => {
            let resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '';

            let ongkir = data.data[0].costs;

            ongkir.forEach(o => {
                let div = document.createElement('div');
                div.innerHTML = `
                    ${o.service} : Rp ${o.cost[0].value} 
                    (${o.cost[0].etd} hari)
                `;
                resultDiv.appendChild(div);
            });
        });
    });

});
</script>

</body>
</html>