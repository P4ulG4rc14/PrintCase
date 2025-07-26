<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editor de Mockups Avanzado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
  <style>
    #canvas-container {
      border: 1px solid #ccc;
      margin-top: 15px;
      position: relative;
      width: 100%;
    }
    canvas {
      width: 100% !important;
      height: auto !important;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="text-center mb-4">🎨 Print Case</h2>

  <div class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">📱 Modelo de teléfono:</label>
        <select class="form-select" id="modeloSelect">
          <option value="">-- Selecciona --</option>
          <option value="iphone12">iPhone 12</option>
          <option value="samsungs22fe">Samsung S22 FE</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">🖼 Imagen del usuario:</label>
        <input class="form-control" type="file" id="userimg" accept="image/*" />
      </div>

      <div class="col-md-6">
        <label class="form-label">✏️ Texto personalizado:</label>
        <input class="form-control" type="text" id="textoPersonalizado" placeholder="Escribe algo..." />
      </div>

      <div class="col-md-6 d-flex align-items-end">
        <button class="btn btn-secondary w-100" onclick="agregarTexto()">➕ Agregar Texto</button>
      </div>

      <div class="col-md-12">
        <label class="form-label">🎉 Stickers:</label>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-outline-primary" onclick="agregarSticker('🔥')">🔥</button>
          <button class="btn btn-outline-primary" onclick="agregarSticker('💖')">💖</button>
          <button class="btn btn-outline-primary" onclick="agregarSticker('🎓')">🎓</button>
          <button class="btn btn-outline-primary" onclick="agregarSticker('✅')">✅</button>
          <button class="btn btn-outline-primary" onclick="agregarSticker('📱')">📱</button>
        </div>
      </div>

      <div class="col-md-12 d-grid gap-2 d-md-flex justify-content-md-end mt-3">
        <button class="btn btn-primary" onclick="insertarMockup()">📌 Insertar en Mockup</button>
        <button class="btn btn-success" onclick="descargar()">📥 Descargar</button>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div id="canvas-container" class="text-center">
        <canvas id="canvas"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
  const modelos = {
    iphone12: "mockups/IPhone 12.png",
    samsungs22fe: "mockups/Samsung S22 FE.png",
  };

  const canvas = new fabric.Canvas("canvas", {
    preserveObjectStacking: true,
  });

  function ajustarCanvasAlContenedor(width, height) {
    const container = document.getElementById("canvas-container");
    const maxWidth = container.offsetWidth;

    const escala = maxWidth / width;
    canvas.setWidth(width * escala);
    canvas.setHeight(height * escala);
    canvas.setZoom(escala);
  }

  async function insertarMockup() {
    const modelo = document.getElementById("modeloSelect").value;
    const userFile = document.getElementById("userimg").files[0];

    if (!modelo || !userFile) {
      alert("⚠️ Debes seleccionar un modelo y una imagen.");
      return;
    }

    canvas.clear();
    const mockupPath = modelos[modelo];

    const reader = new FileReader();
    reader.onload = function (e) {
      fabric.Image.fromURL(e.target.result, function (userImg) {
        fabric.Image.fromURL(mockupPath, function (mockupImg) {
          // Ajustar el canvas al mockup
          ajustarCanvasAlContenedor(mockupImg.width, mockupImg.height);

          // Área para imagen del usuario (80% ancho y alto)
          const areaWidth = mockupImg.width * 0.8;
          const areaHeight = mockupImg.height * 0.8;

          // Escalar la imagen proporcionalmente al área (sin deformar)
          const scaleX = areaWidth / userImg.width;
          const scaleY = areaHeight / userImg.height;
          const scale = Math.min(scaleX, scaleY);

          userImg.set({
            originX: "center",
            originY: "center",
            left: mockupImg.width / 2,
            top: mockupImg.height / 2,
            scaleX: scale,
            scaleY: scale,
            cornerStyle: "circle",
            transparentCorners: false,
            lockUniScaling: true,
          });

          // Añadir imágenes al canvas
          canvas.add(userImg);
          canvas.setActiveObject(userImg);

          mockupImg.set({
            selectable: false,
            evented: false,
          });
          canvas.add(mockupImg);
          mockupImg.moveTo(canvas.getObjects().length);

          canvas.renderAll();
        });
      });
    };
    reader.readAsDataURL(userFile);
  }

  function agregarTexto() {
    const texto = document.getElementById("textoPersonalizado").value;
    if (!texto.trim()) return;

    const text = new fabric.Textbox(texto, {
      left: 50,
      top: 50,
      fill: "#000",
      fontSize: 24,
      fontFamily: "Arial",
      editable: true,
    });

    canvas.add(text);
    canvas.setActiveObject(text);
    canvas.renderAll();
  }

  function agregarSticker(emoji) {
    const sticker = new fabric.Text(emoji, {
      left: 100,
      top: 100,
      fontSize: 48,
      selectable: true,
    });
    canvas.add(sticker);
    canvas.setActiveObject(sticker);
    canvas.renderAll();
  }

  function descargar() {
    const link = document.createElement("a");
    link.download = "resultado_mockup.png";
    link.href = canvas.toDataURL({
      format: "png",
      multiplier: 1,
    });
    link.click();
  }

  window.addEventListener("resize", () => {
    const activeMockup = canvas.getObjects().find((obj) => !obj.selectable);
    if (activeMockup) {
      ajustarCanvasAlContenedor(activeMockup.width, activeMockup.height);
    }
  });
</script>

</body>
</html>
