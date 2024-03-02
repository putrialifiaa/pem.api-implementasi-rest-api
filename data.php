<?php
require_once('connection.php');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT' || $method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $input_vars);
}

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id !== null) {
            $query = "SELECT * FROM Mahasiswa WHERE id = $id";
        } else {
            $query = "SELECT * FROM Mahasiswa";
        }

        $result = mysqli_query($connection, $query);

        $data = array();

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        mysqli_close($connection);

        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false) {
            echo "<!DOCTYPE html>";
            echo "<html lang='en'>";
            echo "<head>";
            echo "<meta charset='UTF-8'>";
            echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
            echo "<title>Data Mahasiswa</title>";
            echo "</head>";
            echo "<body>";
            echo "<h1>Data Mahasiswa</h1>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Nama</th><th>No. Telepon</th><th>Alamat</th></tr>";

            foreach ($data as $item) {
                echo "<tr>";
                echo "<td>" . $item["id"] . "</td>";
                echo "<td>" . $item["nama"] . "</td>";
                echo "<td>" . $item["no_telepon"] . "</td>";
                echo "<td>" . $item["alamat"] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            echo "</body>";
            echo "</html>";
        } else {
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        break;

    case 'POST':
        $nama = isset($_POST['nama']) ? $_POST['nama'] : null;
        $no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : null;
        $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : null;

        if ($nama && $no_telepon && $alamat) {
            $query = "SELECT MAX(id) AS max_id FROM Mahasiswa";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_assoc($result);
            $max_id = $row['max_id'];

            $new_id = $max_id + 1;

            $query = "INSERT INTO Mahasiswa (id, nama, no_telepon, alamat) VALUES ('$new_id', '$nama', '$no_telepon', '$alamat')";

            if (mysqli_query($connection, $query)) {
                echo "Data Mahasiswa berhasil ditambahkan.";
            } else {
                echo "Gagal menambahkan data Mahasiswa: " . mysqli_error($connection);
            }
        } else {
            echo "Data Mahasiswa tidak lengkap.";
        }
        break;

    case 'PUT':
        $id = isset($input_vars['id']) ? $input_vars['id'] : null;
        $nama = isset($input_vars['nama']) ? $input_vars['nama'] : null;
        $no_telepon = isset($input_vars['no_telepon']) ? $input_vars['no_telepon'] : null;
        $alamat = isset($input_vars['alamat']) ? $input_vars['alamat'] : null;

        if ($id && $nama && $no_telepon && $alamat) {
            $query = "UPDATE Mahasiswa SET nama='$nama', no_telepon='$no_telepon', alamat='$alamat' WHERE id=$id";

            if (mysqli_query($connection, $query)) {
                echo "Data Mahasiswa berhasil diperbarui.";
            } else {
                echo "Gagal memperbarui data Mahasiswa: " . mysqli_error($connection);
            }
        } else {
            echo "Data Mahasiswa tidak lengkap.";
        }
        break;

    case 'DELETE':
        $id = isset($input_vars['id']) ? $input_vars['id'] : null;

        if ($id) {
            $query = "DELETE FROM Mahasiswa WHERE id=$id";

            if (mysqli_query($connection, $query)) {
            echo "Data Mahasiswa berhasil dihapus. ";

                $query_update = "UPDATE Mahasiswa SET id = id - 1 WHERE id > $id";
                if (mysqli_query($connection, $query_update)) {
                    echo "ID data-data yang lebih besar dari ID yang dihapus telah diperbarui.";
                } else {
                    echo "Gagal memperbarui ID data-data yang lebih besar dari ID yang dihapus: " . mysqli_error($connection);
                }
            } else {
                echo "Gagal menghapus data Mahasiswa: " . mysqli_error($connection);
            }
        } else {
            echo "ID Mahasiswa tidak diberikan.";
        }
        break;

    default:
        echo "Metode tidak didukung.";
}
?>