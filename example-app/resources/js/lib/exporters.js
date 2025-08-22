import html2canvas from "html2canvas";
import jsPDF from "jspdf";
import ExcelJS from "exceljs";
import { saveAs } from "file-saver";

/** Exporta una vista completa a PDF con paginación A4 */
export async function exportViewAsPDF(selector, filename = "archivo.pdf") {
  const el = document.querySelector(selector);
  if (!el) throw new Error(`No se encontró el selector: ${selector}`);

  // Captura en alta resolución
  const canvas = await html2canvas(el, {
    backgroundColor: "#fff",
    scale: Math.min(2, window.devicePixelRatio || 2),
    useCORS: true
  });

  const imgData = canvas.toDataURL("image/png");
  const pdf = new jsPDF("p", "mm", "a4");
  const pageWidth = pdf.internal.pageSize.getWidth();
  const pageHeight = pdf.internal.pageSize.getHeight();

  const imgWidth = pageWidth;
  const imgHeight = (canvas.height * imgWidth) / canvas.width;

  // Primera página
  pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);

  // Paginación si la imagen es más alta que una página
  let heightLeft = imgHeight - pageHeight;
  let pageCount = 1;
  while (heightLeft > 0) {
    pdf.addPage();
    const position = -(pageHeight * pageCount); // desplaza la misma imagen
    pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;
    pageCount++;
  }

  pdf.save(filename);
}

/** Exporta la vista como imagen dentro de un Excel (.xlsx) -> WYSIWYG */
export async function exportViewAsExcelImage(selector, filename = "archivo.xlsx") {
  const el = document.querySelector(selector);
  if (!el) throw new Error(`No se encontró el selector: ${selector}`);

  const canvas = await html2canvas(el, {
    backgroundColor: "#fff",
    scale: 2,
    useCORS: true
  });
  const imgData = canvas.toDataURL("image/png");

  const wb = new ExcelJS.Workbook();
  const ws = wb.addWorksheet("Vista");

  // Inserta la imagen tal cual (WYSIWYG)
  const imageId = wb.addImage({ base64: imgData, extension: "png" });

  // Ancla en A1 con tamaño en píxeles (exceljs hace la conversión)
  ws.addImage(imageId, {
    tl: { col: 0, row: 0 },
    ext: { width: canvas.width, height: canvas.height }
  });

  // Un pequeño ajuste para que Excel no recorte
  const cols = Math.ceil(canvas.width / 7) + 2; // 1 col ≈ 7px aprox
  ws.columns = Array.from({ length: cols }, () => ({ width: 12 }));

  const buffer = await wb.xlsx.writeBuffer();
  saveAs(
    new Blob([buffer], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    }),
    filename
  );
}

/** (Opcional) Excel en celdas reales con tus datos */
export async function exportDataToExcel({
  filename = "datos.xlsx",
  encabezado = {},
  preguntas = [],
  subtotalI = 0,
  subtotalII = 0,
  total10 = 0,
  comentario = ""
}) {
  const wb = new ExcelJS.Workbook();
  const ws = wb.addWorksheet("Datos");

  // Encabezado
  ws.addRow(["Nombre", encabezado.nombre || ""]);
  ws.addRow(["Puesto", encabezado.puesto || ""]);
  ws.addRow(["Evaluador", encabezado.evaluador || ""]);
  ws.addRow(["Periodo", encabezado.periodo || ""]);
  ws.addRow([]);

  // Tabla principal
  ws.addRow(["Factor", "Definición", "Calificación (1-5)"]);
  preguntas.forEach((p) =>
    ws.addRow([p.factor || "", p.definicion || "", Number(p.calificacion || 0)])
  );

  ws.addRow([]);
  ws.addRow(["Subtotal I (0–5)", "", Number(subtotalI || 0)]);
  ws.addRow(["Subtotal II (0–5)", "", Number(subtotalII || 0)]);
  ws.addRow(["Total (0–10)", "", Number(total10 || 0)]);
  ws.addRow([]);
  ws.addRow(["Comentario"]);
  ws.addRow([comentario || ""]);

  // Formatos simples
  ws.getColumn(3).numFmt = "0.0";
  ws.columns.forEach((c) => {
    let max = 10;
    c.eachCell({ includeEmpty: true }, (cell) => {
      const len = String(cell.value ?? "").length;
      if (len > max) max = len;
    });
    c.width = Math.min(max + 2, 80);
  });

  const buffer = await wb.xlsx.writeBuffer();
  saveAs(
    new Blob([buffer], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    }),
    filename
  );
}
