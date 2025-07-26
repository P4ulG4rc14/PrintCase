
<?php
$nombre = $_GET['nombre'] ?? '';
$jsonFile = 'mockups.json';
if (!$nombre) {
    echo json_encode(['success' => false, 'message' => 'Nombre no proporcionado']);
    exit;
}
$mockups = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
if (!isset($mockups[$nombre])) {
    echo json_encode(['success' => false, 'message' => 'Mockup no encontrado']);
    exit;
}
unlink($mockups[$nombre]);
unset($mockups[$nombre]);
file_put_contents($jsonFile, json_encode($mockups));
echo json_encode(['success' => true, 'message' => 'Mockup eliminado']);
?>
