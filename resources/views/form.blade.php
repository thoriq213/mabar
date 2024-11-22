<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Cari Peserta</title>
    <style>
        /* Gaya overlay loading */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 1050;
            /* Lebih tinggi dari modal Bootstrap */
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div id="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div id="main-content d-none" style="padding: 0 1rem">
        <div class="my-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" placeholder="ISI NAMA LENGKAP">
        </div>
        <div class="mb-3">
            <label for="domisili" class="form-label">Domisili</label>
            <select class="form-select" aria-label="Default select example" id="domisili">
                <option selected>Open this select menu</option>
                <option value="Jatimekar">Jatimekar</option>
                <option value="Jatiluhur">Jatiluhur</option>
                <option value="Jatirasa">Jatirasa</option>
                <option value="Jatiasih">Jatiasih</option>
                <option value="Jatisari">Jatisari</option>
                <option value="Jatikramat">Jatikramat</option>
                <option value="Pondok melati">Pondok melati</option>
                <option value="jatisampurna">jatisampurna</option>
            </select>
        </div>
        <div class="">
            <div class="btn btn-primary" onclick="cari()">CARI</div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(window).on('load', function() {
            // Hilangkan overlay dengan animasi fade
            $('#loading-overlay').fadeOut('slow', function() {
                // Tampilkan konten utama setelah overlay hilang
                $('#main-content').removeClass('d-none');
                $('#main-content').fadeIn('slow');
            });
            $('#loading-overlay').addClass('d-none');
        });

        function cari() {
            const nama = $('#nama').val();
            const domisili = $('#domisili').val();

            if (nama == null || nama.trim() == '') {
                Swal.fire({
                    title: "GAGAL",
                    text: "Nama tidak boleh kosong!",
                    icon: "error"
                });

                return;
            }

            if (domisili == null || domisili == '') {
                Swal.fire({
                    title: "GAGAL",
                    text: "Domisili tidak boleh kosong!",
                    icon: "error"
                });

                return;
            }

            $('#loading-overlay').removeClass('d-none');
            $('#main-content').addClass('d-none');

            $.ajax({
                url: "{{ url('/peserta/get_data') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    responseType: 'blob' // Tangani sebagai file binary
                },
                data: {
                    nama: nama,
                    domisili: domisili
                }, // Mengambil semua data dari form
                success: function(blob) {
                    // Buat URL untuk blob
                    const url = window.URL.createObjectURL(blob);

                    // Buat elemen <a> untuk unduh
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'qrcode.png'; // Nama file yang diunduh
                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                    // Hapus URL setelah selesai
                    window.URL.revokeObjectURL(url);
                    $('#loading-overlay').addClass('d-none');
                        $('#main-content').removeClass('d-none');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: 'Data tidak ada, silahkan hubungi panitia jika ada kendala'
                    }).then((result) => {
                        $('#loading-overlay').addClass('d-none');
                        $('#main-content').removeClass('d-none');
                    }).catch((err) => {

                    });
                }
            });
        }
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    -->
</body>

</html>
