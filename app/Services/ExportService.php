<?php

namespace App\Services;

use App\Models\Quote;

class ExportService
{
    /**
     * Generate PDF export for quote
     */
    public function generatePDF(Quote $quote): string
    {
        $components = $this->buildComponentRows($quote);
        $componentsTotal = array_sum(array_column($components, 'price'));
        $subtotal = $componentsTotal;
        $surcharge = $subtotal * 0.12;

        return $this->buildModernPDF($quote, $components, $componentsTotal, $subtotal, $surcharge);
    }

    private function buildComponentRows(Quote $quote): array
    {
        $rows = [[
            'label' => 'Mozzo',
            'name' => $quote->configuration->wheelHub->name,
            'meta' => '',
            'price' => (float) $quote->configuration->wheelHub->price,
        ]];

        foreach ($quote->configuration->components as $optional) {
            $rows[] = [
                'label' => $this->componentLabel($optional),
                'name' => $this->componentName($optional->name),
                'meta' => $this->componentMeta($quote, $optional),
                'price' => $this->getOptionalEffectivePrice($quote, $optional),
            ];
        }

        usort($rows, function (array $a, array $b) {
            $order = ['Profilo' => 0, 'Mozzo' => 1, 'Raggi' => 2, 'Componente' => 3];

            return ($order[$a['label']] ?? 9) <=> ($order[$b['label']] ?? 9);
        });

        return $rows;
    }

    private function componentLabel($optional): string
    {
        if ($this->isProfileOptional($optional)) {
            return 'Profilo';
        }

        if ($this->isSpokeOptional($optional)) {
            return 'Raggi';
        }

        return 'Componente';
    }

    private function componentName(string $name): string
    {
        return preg_replace(['/^Profilo\s+/i', '/\s+mm/i'], ['', 'mm'], $name) ?? $name;
    }

    private function componentMeta(Quote $quote, $optional): string
    {
        if (!$this->isSpokeOptional($optional)) {
            return '';
        }

        return 'EUR ' . number_format((float) $optional->price, 2, ',', '.') . ' cad. x ' .
            $this->getSpokeCount($quote) . ' pz';
    }

    private function isProfileOptional($optional): bool
    {
        $name = strtolower($optional->name);
        $category = strtolower($optional->category);

        return $category === 'profilo' || str_contains($name, 'profilo');
    }

    private function displayModelName(string $name): string
    {
        return match ($name) {
            'Strada' => 'Strada',
            'Gravel' => 'Gravel',
            'MTB' => 'MTB',
            default => $name,
        };
    }

    private function getOptionalEffectivePrice(Quote $quote, $optional): float
    {
        $price = (float) $optional->price;

        return $this->isSpokeOptional($optional)
            ? $price * $this->getSpokeCount($quote)
            : $price;
    }

    private function getSpokeCount(Quote $quote): int
    {
        return str_contains(strtolower($quote->configuration->wheelCategory->name ?? ''), 'mtb') ? 56 : 48;
    }

    private function isSpokeOptional($optional): bool
    {
        $name = strtolower($optional->name);

        return $optional->exclusive_group === 'spoke'
            || str_contains($name, 'sapim')
            || str_contains($name, 'raggi')
            || str_contains($name, 'berd');
    }

    private function buildModernPDF(
        Quote $quote,
        array $components,
        float $componentsTotal,
        float $subtotal,
        float $surcharge
    ): string {
        $logoPath = public_path('assets/antwheels-nero.jpg');
        $logo = is_file($logoPath) ? $this->readJpeg($logoPath) : null;
        $lineName = $this->displayModelName($quote->configuration->wheelCategory->name);

        $content = '';
        $content .= $this->rect(0, 0, 595, 842, '1 1 1');

        if ($logo) {
            $content .= "q\n180 0 0 120 42 704 cm\n/Im1 Do\nQ\n";
        }
        $content .= $this->text('Cliente', 42, 652, 9, true, '0.45 0.45 0.45');
        $content .= $this->text($this->customerFullName($quote), 42, 633, 13, true, '0.08 0.08 0.08');
        $content .= $this->text($quote->user->email, 42, 615, 9, false, '0.42 0.42 0.42');

        $content .= $this->text('Configurazione', 334, 652, 9, true, '0.45 0.45 0.45');
        $content .= $this->text('Coppia ruote ' . $lineName, 334, 630, 15, true, '0.08 0.08 0.08');
        $content .= $this->text('Componenti', 42, 544, 17, true, '0.08 0.08 0.08');
        $content .= $this->text('Prezzo', 486, 544, 9, false, '0.45 0.45 0.45');

        $y = 506;
        foreach ($components as $component) {
            $content .= $this->componentLine($component, 42, $y);
            $y -= 60;
        }

        $summaryY = max(120, $y - 70);
        $content .= $this->text('Riepilogo', 42, $summaryY + 48, 14, true, '0.08 0.08 0.08');
        $content .= $this->priceRow('Componenti', $componentsTotal, 42, $summaryY + 22);
        $content .= $this->priceRow('Maggiorazione 12%', $surcharge, 42, $summaryY + 2);
        $content .= $this->text('Totale', 42, $summaryY - 46, 18, true, '0.08 0.08 0.08');
        $content .= $this->text('EUR ' . number_format((float) $quote->total_amount, 2, ',', '.'), 410, $summaryY - 46, 18, false, '0.08 0.08 0.08');

        if ($quote->notes) {
            $content .= $this->line(42, 82, 553, 82, '0.90 0.90 0.90');
            $content .= $this->text('Note', 42, 60, 9, true, '0.45 0.45 0.45');
            $content .= $this->text($quote->notes, 42, 43, 10, false, '0.18 0.18 0.18');
        }

        return $this->buildPDF($content, $logo);
    }

    private function customerFullName(Quote $quote): string
    {
        return trim($quote->user->name . ' ' . ($quote->user->last_name ?? ''));
    }

    private function componentLine(array $component, int $x, int $y): string
    {
        $price = 'EUR ' . number_format($component['price'], 2, ',', '.');
        $name = $component['meta'] ? $component['name'] . ' - ' . $component['meta'] : $component['name'];

        $content = $this->text($component['label'], $x, $y, 8, true, '0.50 0.50 0.50');
        $content .= $this->text($name, $x, $y - 24, 10, false, '0.08 0.08 0.08');
        $content .= $this->text($price, $x + 402, $y - 24, 10, false, '0.08 0.08 0.08');
        $content .= $this->line($x, $y - 48, $x + 511, $y - 48, '0.92 0.92 0.92');

        return $content;
    }

    private function priceRow(string $label, float $value, int $x, int $y): string
    {
        return $this->text($label, $x, $y, 10, false, '0.38 0.38 0.38') .
            $this->text('EUR ' . number_format($value, 2, ',', '.'), $x + 402, $y, 10, false, '0.08 0.08 0.08');
    }

    private function card(int $x, int $y, int $w, int $h): string
    {
        return $this->rect($x, $y, $w, $h, '1 1 1') .
            $this->line($x, $y, $x + $w, $y, '0.86 0.86 0.86') .
            $this->line($x, $y + $h, $x + $w, $y + $h, '0.90 0.90 0.90') .
            $this->line($x, $y, $x, $y + $h, '0.90 0.90 0.90') .
            $this->line($x + $w, $y, $x + $w, $y + $h, '0.90 0.90 0.90');
    }

    private function rect(int $x, int $y, int $w, int $h, string $rgb): string
    {
        return "q
{$rgb} rg
{$x} {$y} {$w} {$h} re
f
Q
";
    }

    private function line(int $x1, int $y1, int $x2, int $y2, string $rgb): string
    {
        return "q
{$rgb} RG
0.7 w
{$x1} {$y1} m
{$x2} {$y2} l
S
Q
";
    }

    private function text(string $text, int $x, int $y, int $size = 10, bool $bold = false, string $rgb = '0 0 0'): string
    {
        $font = $bold ? 'F2' : 'F1';

        return "BT
{$rgb} rg
/{$font} {$size} Tf
{$x} {$y} Td
(" . $this->escapePDFText($text) . ") Tj
ET
";
    }

    private function buildPDF(string $content, ?array $logo): string
    {
        $resources = '<< /Font << /F1 4 0 R /F2 5 0 R >>';
        if ($logo) {
            $resources .= ' /XObject << /Im1 7 0 R >>';
        }
        $resources .= ' >>';

        $objects = [
            "1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
",
            "2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
",
            "3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources {$resources} /Contents 6 0 R >>
endobj
",
            "4 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
",
            "5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>
endobj
",
            "6 0 obj
<< /Length " . strlen($content) . " >>
stream
" . $content . "
endstream
endobj
",
        ];

        if ($logo) {
            $objects[] = "7 0 obj
<< /Type /XObject /Subtype /Image /Width {$logo['width']} /Height {$logo['height']} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($logo['data']) . " >>
stream
" . $logo['data'] . "
endstream
endobj
";
        }

        $pdf = "%PDF-1.4
";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref
0 " . (count($objects) + 1) . "
";
        $pdf .= "0000000000 65535 f 
";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n 
", $offsets[$i]);
        }

        $pdf .= "trailer
<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>
";
        $pdf .= "startxref
" . $xrefOffset . "
%%EOF";

        return $pdf;
    }

    private function readJpeg(string $path): ?array
    {
        $size = getimagesize($path);
        $data = file_get_contents($path);

        if (!$size || !$data) {
            return null;
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
            'data' => $data,
        ];
    }

    private function escapePDFText(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
