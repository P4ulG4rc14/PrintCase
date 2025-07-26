
<?php
$carpeta = 'mockups/';
$jsonFile = 'mockups.json';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $archivo = $_FILES['mockup'] ?? null;
    if (!$nombre || !$archivo) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $rutaFinal = $carpeta . $nombre . '.' . $extension;
    if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar']);
        exit;
    }
    $mockups = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    $mockups[$nombre] = $rutaFinal;
    file_put_contents($jsonFile, json_encode($mockups));
    echo json_encode(['success' => true, 'message' => 'Mockup subido']);
}
?>
