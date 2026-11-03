<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-24
//
// Generador mínimo de archivos Excel (.xlsx) sin librerías externas.
// Usa ZipArchive. Escribe las filas a un archivo temporal, por lo que
// soporta exportaciones grandes sin cargar todo en memoria.
//
// Uso:
//   $x = new XlsxSimple('Auditoría', ['Col 1', 'Col 2'], [12, 30]);
//   $x->fila(['a', 123]);
//   $x->descargar('archivo.xlsx');
// ************************************************************

class XlsxSimple {

    private $hoja;
    private $tmp;
    private $fh;
    private $n = 0;           // filas escritas (incluye encabezado)
    private $columnas;

    public function __construct(string $hoja, array $encabezados, array $anchos = []) {
        $this->hoja     = mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $hoja), 0, 31);
        $this->columnas = count($encabezados);
        $this->tmp      = tempnam(sys_get_temp_dir(), 'xlsx');
        $this->fh       = fopen($this->tmp, 'w');

        fwrite($this->fh, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>');

        if ($anchos) {
            fwrite($this->fh, '<cols>');
            foreach ($anchos as $i => $w) {
                $c = $i + 1;
                fwrite($this->fh, '<col min="' . $c . '" max="' . $c . '" width="' . (float) $w . '" customWidth="1"/>');
            }
            fwrite($this->fh, '</cols>');
        }
        fwrite($this->fh, '<sheetData>');
        $this->escribir($encabezados, 1);
    }

    /** Agrega una fila de datos. */
    public function fila(array $valores): void {
        $this->escribir(array_values($valores), 0);
    }

    private static function col(int $i): string {
        $s = '';
        for ($i++; $i > 0; $i = intdiv($i - 1, 26)) { $s = chr(65 + ($i - 1) % 26) . $s; }
        return $s;
    }

    private static function texto($v): string {
        $v = (string) $v;
        $v = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $v) ?? '';
        if (mb_strlen($v) > 32000) { $v = mb_substr($v, 0, 32000) . '…'; }   // límite de Excel por celda
        return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function escribir(array $valores, int $estilo): void {
        $this->n++;
        $r = $this->n;
        $xml = '<row r="' . $r . '">';
        foreach ($valores as $i => $v) {
            if ($v === null || $v === '') continue;
            $ref = self::col($i) . $r;
            $s   = $estilo ? ' s="' . $estilo . '"' : '';
            if (!$estilo && (is_int($v) || is_float($v))) {
                $xml .= '<c r="' . $ref . '"' . $s . '><v>' . $v . '</v></c>';
            } else {
                $xml .= '<c r="' . $ref . '"' . $s . ' t="inlineStr"><is><t xml:space="preserve">' . self::texto($v) . '</t></is></c>';
            }
        }
        fwrite($this->fh, $xml . '</row>');
    }

    /** Envía el archivo al navegador y limpia los temporales. */
    public function descargar(string $nombre): void {
        $ultima = self::col(max(0, $this->columnas - 1)) . max(1, $this->n);
        fwrite($this->fh, '</sheetData><autoFilter ref="A1:' . $ultima . '"/></worksheet>');
        fclose($this->fh);

        $zipPath = tempnam(sys_get_temp_dir(), 'xlsz');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/workbook.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::texto($this->hoja) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '<definedNames><definedName name="_xlnm._FilterDatabase" localSheetId="0" hidden="1">\'' . self::texto($this->hoja) . '\'!$A$1:$' . preg_replace('/(\d+)$/', '\$$1', $ultima) . '</definedName></definedNames>'
            . '</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');

        // Estilo 0 = normal, 1 = encabezado (negrita, fondo celeste)
        $zip->addFromString('xl/styles.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE8F0F7"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>');

        $zip->addFile($this->tmp, 'xl/worksheets/sheet1.xml');
        $zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: max-age=0');
        readfile($zipPath);

        @unlink($zipPath);
        @unlink($this->tmp);
    }
}
