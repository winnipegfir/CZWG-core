<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\Network\ActivityWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityWarningController extends Controller
{
    public function index(Request $request)
    {
        $quarters = ActivityWarning::orderByDesc('quarter_start')->pluck('quarter_label')->unique()->values();

        $selectedQuarter = $request->get('quarter');
        if (! $selectedQuarter || ! $quarters->contains($selectedQuarter)) {
            $selectedQuarter = $quarters->first();
        }

        $warnings = ActivityWarning::where('quarter_label', $selectedQuarter)
            ->orderBy('member_name')
            ->get();

        return view('dashboard.network.warnings.index', compact('warnings', 'quarters', 'selectedQuarter'));
    }

    public function update(Request $request, ActivityWarning $warning)
    {
        $request->validate([
            'warning_sent' => 'nullable|boolean',
            'result' => 'required|in:'.implode(',', array_keys(ActivityWarning::RESULTS)),
            'notes' => 'nullable|string|max:2000',
        ]);

        $warningSent = $request->boolean('warning_sent');

        if ($warningSent && ! $warning->warning_sent) {
            // Only stamp who/when the first time this flips on, so re-saving the
            // form after adding a note doesn't keep bumping the sent timestamp.
            $warning->warning_sent_at = now();
            $warning->warning_sent_by = Auth::id();
            $warning->deadline = now()->addDays(ActivityWarning::WARNING_PERIOD_DAYS);
        } elseif (! $warningSent) {
            $warning->warning_sent_at = null;
            $warning->warning_sent_by = null;
            $warning->deadline = null;
        }

        $warning->warning_sent = $warningSent;
        $warning->result = $request->input('result');
        $warning->notes = $request->input('notes');
        $warning->save();

        return redirect()->route('network.warnings.index')->with('success', 'Warning updated.');
    }
}
