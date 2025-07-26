<?php
$modelos = [
    'iphone12' => [
        'nombre' => 'iPhone 12',
        'mockup' => __DIR__ . '/mockups/iPhone 12.png'
    ],
    'samsungs22fe' => [
        'nombre' => 'Samsung S22 FE',
        'mockup' => __DIR__ . '/mockups/Samsung S22 FE.png'
    ],
];

$resultado = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = $_POST['model'] ?? '';
    if (isset($modelos[$modelo]) && isset($_FILES['design']) && $_FILES['design']['error'] === UPLOAD_ERR_OK) {
        $mockupPath = $modelos[$modelo]['mockup'];
        $userImgTmp = $_FILES['design']['tmp_name'];
        $userImgExt = pathinfo($_FILES['design']['name'], PATHINFO_EXTENSION);
        $userImgPath = __DIR__ . '/uploads/' . uniqid('userimg_') . '.' . $userImgExt;
        move_uploaded_file($userImgTmp, $userImgPath);

        $outputPath = __DIR__ . '/uploads/' . uniqid('resultado_') . '.png';

        $cmd = "python " . escapeshellarg(__DIR__ . '/../Code/compose_case.py') . " " .
            escapeshellarg($mockupPath) . " " .
            escapeshellarg($userImgPath) . " " .
            escapeshellarg($outputPath);

        exec($cmd, $output, $return_var);

        if ($return_var === 1 && file_exists($outputPath)) {
            $resultado = 'uploads/' . basename($outputPath);
        } else {
            $error = "Error al generar la imagen.";
        }
    } else {
        $error = "Selecciona un modelo y sube una imagen válida.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mockup de Celular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Open+Sans&family=Pacifico&family=Anton&family=Dancing+Script&family=Great+Vibes&family=Oswald&family=Playfair+Display&family=Lobster&family=Raleway&family=Indie+Flower&family=Monoton&family=Bebas+Neue&family=Courgette&family=Shadows+Into+Light&family=Orbitron&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .font-preview { font-size: 20px; margin-bottom: 5px; }
        #canvas { border: 2px dashed #dee2e6; max-width: 100%; height: auto; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Diseñador de Carcasas</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="model-select" class="form-label fw-bold">Modelo:</label>
                        <select name="model" id="model-select" class="form-select" required>
                            <option value="">Selecciona un modelo</option>
                            <?php foreach ($modelos as $key => $info): ?>
                                <option value="<?= $key ?>" <?= (isset($modelo) && $modelo === $key) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($info['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="design-upload" class="form-label fw-bold">Subir imagen:</label>
                        <input type="file" name="design" id="design-upload" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success px-4">Generar Vista Previa</button>
                </div>
            </form>

            <?php if ($resultado): ?>
                <hr>
                <h5 class="mb-3">Editor de diseño</h5>

                <div class="row">
                    <div class="col-12 col-md-5 text-center mb-3">
                        <canvas id="canvas" width="300" height="600" class="rounded shadow w-100" style="max-width:300px;"></canvas>
                    </div>

                    <div class="col-12 col-md-7">
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-6">
                                <input type="text" id="text-input" class="form-control" placeholder="Texto a agregar" oninput="updatePreview()">
                            </div>
                            <div class="col-md-6">
                                <select id="font-selector" class="form-select" onchange="updatePreview()">
                                    <option value="Roboto">Roboto</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Pacifico">Pacifico</option>
                                    <option value="Anton">Anton</option>
                                    <option value="Dancing Script">Dancing Script</option>
                                    <option value="Great Vibes">Great Vibes</option>
                                    <option value="Oswald">Oswald</option>
                                    <option value="Playfair Display">Playfair Display</option>
                                    <option value="Lobster">Lobster</option>
                                    <option value="Raleway">Raleway</option>
                                    <option value="Indie Flower">Indie Flower</option>
                                    <option value="Monoton">Monoton</option>
                                    <option value="Bebas Neue">Bebas Neue</option>
                                    <option value="Courgette">Courgette</option>
                                    <option value="Shadows Into Light">Shadows Into Light</option>
                                    <option value="Orbitron">Orbitron</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="color" id="color-picker" class="form-control form-control-color" value="#000000" onchange="updatePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="number" id="font-size" class="form-control" value="30" min="10" max="100" onchange="updatePreview()">
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column align-items-start">
                                    <span id="text-preview" class="font-preview">Vista previa</span>
                                    <button onclick="addText()" type="button" class="btn btn-outline-success btn-sm">Agregar</button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button onclick="deleteSelected()" class="btn btn-outline-danger btn-sm">Eliminar</button>
                            <button onclick="duplicateSelected()" class="btn btn-outline-info btn-sm">Duplicar</button>
                            <button onclick="rotateSelected(15)" class="btn btn-outline-secondary btn-sm">Rotar +15°</button>
                            <button onclick="rotateSelected(-15)" class="btn btn-outline-secondary btn-sm">Rotar -15°</button>
                            <button onclick="toggleBold()" class="btn btn-outline-dark btn-sm">Negrita</button>
                            <button onclick="toggleItalic()" class="btn btn-outline-dark btn-sm">Cursiva</button>
                            <button onclick="toggleUnderline()" class="btn btn-outline-dark btn-sm">Subrayado</button>
                            <button onclick="toggleLinethrough()" class="btn btn-outline-dark btn-sm">Tachado</button>
                            <button onclick="toggleShadow()" class="btn btn-dark btn-sm">Sombra</button>
                            <button onclick="toggleStroke()" class="btn btn-dark btn-sm">Borde texto</button>
                            <button onclick="downloadImage()" class="btn btn-primary btn-sm">Descargar imagen</button>
                        </div>
                    </div>
                </div>

                <script>
                    const canvas = new fabric.Canvas('canvas');
                    const resultado = '<?= htmlspecialchars($resultado) ?>';

                    fabric.Image.fromURL(resultado, function(img) {
                        const maxWidth = window.innerWidth < 400 ? 250 : 300;
                        img.scaleToWidth(maxWidth);
                        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                        canvas.setHeight(img.getScaledHeight());
                    });

                    let previewBold = false;
                    let previewItalic = false;

                    function updatePreview() {
                        const text = document.getElementById('text-input').value;
                        const font = document.getElementById('font-selector').value;
                        const color = document.getElementById('color-picker').value;
                        const size = document.getElementById('font-size').value;
                        const preview = document.getElementById('text-preview');
                        preview.textContent = text || 'Vista previa';
                        preview.style.fontFamily = font;
                        preview.style.color = color;
                        preview.style.fontSize = size + 'px';
                        preview.style.fontWeight = previewBold ? 'bold' : 'normal';
                        preview.style.fontStyle = previewItalic ? 'italic' : 'normal';
                    }

                    function addText() {
                        const text = document.getElementById('text-input').value || 'Texto';
                        const font = document.getElementById('font-selector').value;
                        const color = document.getElementById('color-picker').value;
                        const size = parseInt(document.getElementById('font-size').value);
                        const fontWeight = previewBold ? 'bold' : 'normal';
                        const fontStyle = previewItalic ? 'italic' : 'normal';
                        const textbox = new fabric.Textbox(text, {
                            left: 50, top: 50,
                            fontFamily: font, fill: color, fontSize: size,
                            fontWeight, fontStyle,
                            editable: true, borderColor: 'blue', cornerColor: 'green',
                            transparentCorners: false, padding: 5,
                        });
                        canvas.add(textbox).setActiveObject(textbox);
                    }

                    function deleteSelected() {
                        const obj = canvas.getActiveObject();
                        if (obj) canvas.remove(obj);
                    }

                    function duplicateSelected() {
                        const obj = canvas.getActiveObject();
                        if (obj) {
                            obj.clone(function(cloned) {
                                cloned.set({ left: obj.left + 10, top: obj.top + 10, evented: true });
                                canvas.add(cloned).setActiveObject(cloned);
                            });
                        }
                    }

                    function rotateSelected(degrees) {
                        const obj = canvas.getActiveObject();
                        if (obj) {
                            obj.rotate((obj.angle + degrees) % 360);
                            canvas.renderAll();
                        }
                    }

                    function toggleBold() {
                        const obj = canvas.getActiveObject();
                        if (obj?.type === 'textbox') {
                            obj.set('fontWeight', obj.fontWeight === 'bold' ? 'normal' : 'bold');
                            canvas.renderAll();
                        }
                    }

                    function toggleItalic() {
                        const obj = canvas.getActiveObject();
                        if (obj?.type === 'textbox') {
                            obj.set('fontStyle', obj.fontStyle === 'italic' ? 'normal' : 'italic');
                            canvas.renderAll();
                        }
                    }

                    function toggleUnderline() {
                        const obj = canvas.getActiveObject();
                        if (obj?.type === 'textbox') {
                            obj.set('underline', !obj.underline);
                            canvas.renderAll();
                        }
                    }

                    function toggleLinethrough() {
                        const obj = canvas.getActiveObject();
                        if (obj?.type === 'textbox') {
                            obj.set('linethrough', !obj.linethrough);
                            canvas.renderAll();
                        }
                    }

                    function toggleShadow() {
                        const obj = canvas.getActiveObject();
                        if (obj) {
                            obj.set('shadow', obj.shadow ? null : new fabric.Shadow({ color: 'rgba(0,0,0,0.4)', blur: 5, offsetX: 3, offsetY: 3 }));
                            canvas.renderAll();
                        }
                    }

                    function toggleStroke() {
                        const obj = canvas.getActiveObject();
                        if (obj) {
                            if (obj.stroke) {
                                obj.set({ stroke: null, strokeWidth: 0 });
                            } else {
                                obj.set({ stroke: 'black', strokeWidth: 1 });
                            }
                            canvas.renderAll();
                        }
                    }

                    function downloadImage() {
                        const dataURL = canvas.toDataURL({ format: 'png', quality: 1, multiplier: 3 });
                        const link = document.createElement('a');
                        link.href = dataURL;
                        link.download = 'mockup_editado.png';
                        link.click();
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
