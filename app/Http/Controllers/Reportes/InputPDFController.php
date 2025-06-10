<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Input;
use TCPDF;

class InputPDFController extends Controller
{
    public function exportPDF()
    {
        // Obtener los datos de los insumos y convertirlos en un array para facilitar el manejo
        $inputs = Input::orderBy('id', 'asc')->get();

        $inputsArray = $inputs->map(function ($input) {
            return [
                'id' => $input->id,
                'name' => $input->name,
                'description' => $input->description,
                'price' => $input->price,
                'quantity' => $input->quantity,
                'almacen_name' => $input->almacen->name,
                'supplier_name' => $input->supplier->name,
                'state' => $input->state == 1 ? 'Activo' : 'Inactivo',
                'created_at' => $input->created_at,
                'updated_at' => $input->updated_at,
            ];
        })->toArray();
        // Crear el objeto TCPDF
        $pdf = new TCPDF(); // 'L' para orientación horizontal

        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Insumos');
        $pdf->SetSubject('Reporte de Insumos');

        // Configuración de márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10); // Asegura que haya espacio entre las filas y el pie de la página

        // Eliminar la línea de encabezado (borde superior)
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);

        // Personalizar el pie de página (eliminar línea predeterminada)
        $pdf->setFooterData(array(0, 0, 0), array(255, 255, 255));

        // Agregar la primera página
        $pdf->AddPage();

        // Encabezado del PDF
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Insumos', 0, 1, 'C');

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(242, 242, 242);  // Color de fondo para los encabezados

        $header = ['ID', 'Nombre', 'Descripcion', 'Precio', 'Cantidad', 'Almacen', 'Proveedor', 'Estado', 'Creación', 'Actualización'];
        $widths = [5, 17, 35, 12, 13, 23, 30, 12, 23, 23]; // Tamaños adecuados para las celdas
        // Establecer los encabezados de la tabla en la primera página
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 7, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();  // Salto de línea después del encabezado

        // Imprimir los datos de los insumos
        $pdf->SetFont('helvetica', '', 6);

        foreach ($inputsArray as $input) {
            // Si la posición Y está cerca del final de la página, añadir una nueva página y repetir los encabezados
            if ($pdf->GetY() > 250) {
                $pdf->AddPage(); // Añadir una nueva página
                // Repetir los encabezados en la nueva página
                $pdf->SetFont('helvetica', 'B', 6);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 6, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }

            // Asegurarse de que las celdas no se sobrepasen
            $pdf->SetFont('helvetica', '', 6);
            $pdf->MultiCell($widths[0], 6, $input['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 6, $input['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 6, $input['description'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 6, 'S/.' . number_format($input['price'], 2), 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 6, $input['quantity'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 6, $input['almacen_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 6, $input['supplier_name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 6, $input['state'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[8], 6, $input['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[9], 6, $input['updated_at'], 1, 'C', 0, 0);
            $pdf->Ln();  // Salto de línea después de cada fila
        }
        // Detenemos cualquier salida previa si la hay
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Generar el PDF y devolverlo al navegador
        $pdfOutput = $pdf->Output('Insumos.pdf', 'S'); // "S" = string, no lo imprime directamente
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="Insumos.pdf"');
    }
}
