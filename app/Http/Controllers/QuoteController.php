<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\Configuration;
use App\Services\ConfiguratorService;
use App\Services\ExportService;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function __construct(
        protected ConfiguratorService $configuratorService,
        protected ExportService $exportService
    ) {}

    public function index()
    {
        $quotes = Quote::where('user_id', Auth::id())
            ->with(['configuration', 'configuration.wheelCategory', 'configuration.wheelHub'])
            ->get();
        return response()->json($quotes);
    }

    public function store(StoreQuoteRequest $request)
    {
        $configuration = Configuration::where('user_id', Auth::id())
            ->findOrFail($request->configuration_id);

        $quoteDetails = $this->configuratorService->generateQuoteDetails($configuration);

        $quote = Quote::create([
            'configuration_id' => $configuration->id,
            'user_id' => Auth::id(),
            'total_amount' => $configuration->total_price,
            'status' => 'draft',
            'notes' => $request->notes ?? null,
        ]);

        return response()->json([
            'quote' => $quote,
            'details' => $quoteDetails,
        ], 201);
    }

    public function show(string $id)
    {
        $quote = Quote::where('user_id', Auth::id())
            ->with(['configuration.wheelCategory', 'configuration.wheelHub', 'configuration.components'])
            ->findOrFail($id);

        $quoteDetails = $this->configuratorService->generateQuoteDetails($quote->configuration);

        return response()->json([
            'quote' => $quote,
            'details' => $quoteDetails,
        ]);
    }

    public function update(UpdateQuoteRequest $request, string $id)
    {
        $quote = Quote::where('user_id', Auth::id())->findOrFail($id);

        $quote->update([
            'notes' => $request->notes ?? $quote->notes,
        ]);

        $quoteDetails = $this->configuratorService->generateQuoteDetails($quote->configuration);

        return response()->json([
            'quote' => $quote,
            'details' => $quoteDetails,
        ]);
    }

    public function destroy(string $id)
    {
        $quote = Quote::where('user_id', Auth::id())->findOrFail($id);
        $quote->delete();
        return response()->json(null, 204);
    }

    /**
     * Esporta un preventivo (marca come esportato e genera file)
     */
    public function export(string $id)
    {
        $quote = Quote::where('user_id', Auth::id())
            ->with(['configuration.wheelCategory', 'configuration.wheelHub', 'configuration.components', 'user'])
            ->findOrFail($id);

        $quote->update(['status' => 'exported']);

        $pdf = $this->exportService->generatePDF($quote);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="preventivo_' . $quote->id . '.pdf"');
    }
}
