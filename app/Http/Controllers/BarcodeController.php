<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController extends Controller
{
    public function print(Request $request)
    {
        $q = trim($request->get('q', ''));

        $productos = Producto::query()
            ->when($q, fn ($qq) =>
                $qq->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%$q%")
                    ->orWhere('codigo', 'like', "%$q%");
                })
            )
            ->whereNotNull('codigo')
            ->orderByDesc('id')
            ->paginate(48)
            ->withQueryString();

        return view('barcodes.print', compact('productos', 'q'));
    }

    public function html(string $code)
    {
        $code = trim($code);
        abort_if($code === '', 404);

        $name = trim(request('name',''));

        return view('barcodes.label', compact('code','name'));
    }

    public function zpl(string $code)
    {
        $code = trim($code);
        abort_if($code === '', 404);

        $name = trim(request('name',''));

        $W = 464;
        $H = 320;

        $zpl = "^XA"
            ."^PW{$W}"
            ."^LL{$H}"
            ."^LH0,0"
            ."^CI28"
            ."^BY2,2,56"
            .($name !== ''
                ? "^FO20,10^FB".($W-40).",2,0,C,0^A0N,24,24^FD".$this->esc($name)."^FS"
                : "")
            ."^FO20,".($name!==''?110:70)
            ."^FB".($W-40).",1,0,C,0"
            ."^BCN,100,Y,N,N"
            ."^FD".$this->esc($code)."^FS"
            ."^FO20,".($name!==''?220:200)
            ."^FB".($W-40).",1,0,C,0^A0N,26,26^FD".$this->esc($code)."^FS"
            ."^PQ1"
            ."^XZ";

        return response($zpl, 200, [
            'Content-Type' => 'application/zpl',
            'Content-Disposition' => "attachment; filename=label_{$code}.zpl",
        ]);
    }

    private function esc(string $s): string
    {
        return str_replace(['^','~','\\'], ['\^','\~','\\\\'], $s);
    }

    public function preview(string $code)
    {
        $generator = new BarcodeGeneratorPNG();

        $barcode = $generator->getBarcode(
            $code,
            $generator::TYPE_CODE_128,
            2,
            60
        );

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response($barcode)
            ->header('Content-Type', 'image/png');
    }


}
