<?php

// Koneksi database start
include 'connect.php';
// Koneksi database end

if (isset($_POST['no_registrasi']) || isset($_POST['nama'])) {

    if ($_POST['no_registrasi'] == '' || $_POST['nama'] == '') {
        echo json_encode([
            "success" => false,
            "message" => "Inputan Wajib Diisi!"
        ]);

        return false;
    }

    $no_registrasi = htmlspecialchars($_POST["no_registrasi"]);
    $nama = htmlspecialchars($_POST["nama"]);
    $waktu_bermain = "10 Menit";

    $sql = "SELECT * FROM mainan";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["nama"] == $nama || $row["no_registrasi"] == $no_registrasi) {
                echo json_encode([
                    "success" => false,
                    "message" => "Input yang anda masukkan sudah terdaftar!"
                ]);

                return false;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO mainan (no_registrasi, nama, waktu_bermain) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $no_registrasi, $nama, $waktu_bermain);
    $stmt->execute();

    $id = mysqli_insert_id($conn);

    echo json_encode([
        "success" => true,
        "datas" => [
            "id" => $id,
            "no_registrasi" => $no_registrasi,
            "nama" => $nama,
            "waktu_bermain" => $waktu_bermain
        ],
        "message" => "Data berhasil ditambahkan!"
    ]);

    $stmt->close();
    $conn->close();

    exit();
}

if (isset($_POST["valueText"])) {
    $valueText = $_POST["valueText"];

    $myfile = fopen("testing.txt", "w") or die("Unable to open file!");
    $txt = $valueText;
    fwrite($myfile, $txt);
    fclose($myfile);

    echo json_encode([
        "success" => false,
        "datas" => $valueText,
        "message" => "Inputan Wajib Diisi!"
    ]);

    exit;
}

if(isset($_POST["valueIdUser"])) {
    $valueIdUser = $_POST["valueIdUser"];

    $sql = "DELETE FROM mainan WHERE id=" . $valueIdUser ."";
    $result = $conn->query($sql);

    if($result === TRUE) {
        $message = "Data Berhasil Dihapus";
    }
    else {
        $message = "Data Gagal Dihapus";
    }

    echo json_encode([
        "message" => $message
    ]);

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wahana</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Style Website -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none;
            border: none;
            text-decoration: none;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: yellow;
        }

        .container {
            padding: 2rem;
            width: 100%;
        }

        form {
            width: 100%;
        }

        form .input-form,
        form .btn-form {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form .input-form input {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }

        form .btn-form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        form .btn-form button {
            width: 120px;
            padding: 1rem;
            font-size: 1.4rem;
            background-color: black;
            color: white;
            cursor: pointer;

            /* Transition */
            transition: 0.1s;
        }

        form .btn-form button:hover,
        form .btn-form button:focus {
            background-color: white;
            color: black;
        }

        .data {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 3rem;
        }

        .data table,
        tr,
        td {
            padding: 1rem;
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 0.8rem;
            text-align: center;
            background-color: white;
        }

        .begin {
            width: 90px;
            padding: 1rem;
            font-size: 0.8rem;
            background-color: rgb(0, 140, 255);
            color: white;
            cursor: pointer;
            /* Transition */
            transition: 0.1s;
        }

        .begin:hover,
        .begin:focus {
            background-color: white;
            color: black;
        }

        .delete {
            display: inline-block;
            width: 90px;
            padding: 1rem;
            font-size: 0.8rem;
            background-color: red;
            color: white;
            cursor: pointer;
            /* Transition */
            transition: 0.1s;
        }

        .delete:hover,
        .delete:focus {
            background-color: white;
            color: black;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="margin-bottom: 3rem;">
            <h3><?= date('d-m-Y'); ?></h3>
        </div>

        <!-- Form input start -->
        <form id="formData">
            <div class="input-form">
                <input type="text" name="no_registrasi" placeholder="No Registrasi" autocomplete="off" autofocus>
            </div>
            <div class="input-form">
                <input type="text" name="nama" placeholder="Nama" autocomplete="off" autofocus>
            </div>
            <div class="btn-form">
                <button type="reset">Reset</button>
                <button type="submit">Tambah</button>
            </div>
        </form>
        <!-- Form input end -->

        <!-- Data start -->
        <div class="data">
            <table>
                <thead>
                    <tr>
                        <td>No</td>
                        <td>No Registrasi</td>
                        <td>Nama</td>
                        <td>Waktu Bermain</td>
                        <td>Durasi</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody id="datas">
                </tbody>
            </table>
        </div>
        <!-- Data end -->
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>

        // Notifikasi nodemcu start
        sendTextFile("<b>=> Wahana Bermain</b>")
        // Notifikasi nodemcu end

        var no = 1
        $(document).ready(function() {

            $("#formData").on("submit", function(e) {
                e.preventDefault()

                $.ajax({
                    url: "",
                    type: "POST",
                    data: $("#formData").serialize(),
                    success: function(response) {
                        const data = JSON.parse(response)

                        if (data.success) {
                            $("input").val("")

                            $("#datas").append(`
                                <tr>
                                    <td>` + (no++) + `</td>
                                    <td>` + data.datas.no_registrasi + `</td>
                                    <td>` + data.datas.nama + `</td>
                                    <td>` + data.datas.waktu_bermain + `</td>
                                    <td id="waktu_bermain" class="textCounter` + data.datas.id + `"></td>
                                    <td>
                                        <a href="javascript:void(0)" class="delete" onclick="hapusDataPengunjung(event, ` + data.datas.id + `)">Hapus</a>
                                    </td>
                                </tr>
                            `)

                            const nodeList = document.querySelectorAll("#waktu_bermain")
                            var tmp = new Array();
                            for (let i = 0; i < nodeList.length; i++) {

                                // Durasi bermain start
                                let duration = 1;
                                // Durasi bermain end

                                const startTime = Date.now();
                                const eventTime = duration * 60 * 1000;
                                const eventDuration = new Date(startTime + eventTime);

                                // Timer & Counter
                                tmp[i] = setInterval(() => {
                                    const timer = eventDuration.getTime() - Date.now();
                                    const minutes = Math.floor((timer % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((timer % (1000 * 60)) / 1000);

                                    // Clear and reset when done
                                    if (timer <= 0) {
                                        $(".textCounter" + data.datas.id).text("0 Menit, 0 Detik").css({
                                            "color": "red",
                                            "font-weight": "bold"
                                        });
                                        clearInterval(tmp[i]);

                                        sendTextFile("<b>=> " + data.datas.nama + " Habis" + "          " + "</b>")
                                        // Kirim data Nodemcu
                                    } else {
                                        $(".textCounter" + data.datas.id).text(minutes + " Menit, " + seconds + " Detik")
                                    }
                                }, 1000);
                            }

                            alert(data.message)
                        } else {
                            alert(data.message)
                        }
                    }
                })
            })
        })

        function hapusDataPengunjung(event, id) {
            var confirmation = confirm("Hapus Data Pengunjung?")

            if(confirmation) {
                $.ajax({
                    url: "",
                    type: "POST",
                    data: {
                        "valueIdUser": id
                    }, 
                    success: function(response) {
                        // const data = JSON.parse(response)
                        event.target.parentElement.parentElement.remove()
                        // console.log(data.message)
                    }
                })
            }
        }

        // Notifikasi nodemcu start
        function sendTextFile(value) {
            $.ajax({
                url: "",
                type: "POST",
                data: {
                    "valueText": value
                },
                success: function(response) {
                    const data = JSON.parse(response)


                }
            })
        }
        // Notifikasi nodemcu end
    </script>
</body>

</html>