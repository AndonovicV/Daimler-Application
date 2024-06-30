<?php
require('fpdf.php');
require('src/autoload.php');

use setasign\Fpdi\Fpdi;

function downloadFile($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Follow redirects if any
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/pdf',
    ));
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Error downloading file from $url: " . $error);
    }
    curl_close($ch);

    // Debugging: Save the response to a file for inspection
    $debugFile = tempnam(sys_get_temp_dir(), 'debug_') . '.pdf';
    file_put_contents($debugFile, $data);
    echo "Debug: Response saved to $debugFile\n";

    if (substr($data, 0, 4) !== '%PDF') {
        throw new Exception("Downloaded file from $url does not appear to be a valid PDF.");
    }

    return $data;
}

function mergePdfs($urls, $outputFile) {
    $pdf = new FPDI();

    foreach ($urls as $url) {
        $pdfContent = downloadFile($url);
        $tmpFile = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tmpFile, $pdfContent);

        $pageCount = $pdf->setSourceFile($tmpFile);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }

        unlink($tmpFile);
    }

    $pdf->Output('F', $outputFile);
}

// Example URLs
$urls = [
    'http://localhost/Daimler/mt_agenda.php?export=2&agenda_id=120',
    'http://localhost/Daimler/protokol.php?export=2&protokol_id=120'
];

$outputFile = 'merged.pdf';

try {
    mergePdfs($urls, $outputFile);

    // Serve the file for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
    readfile($outputFile);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
