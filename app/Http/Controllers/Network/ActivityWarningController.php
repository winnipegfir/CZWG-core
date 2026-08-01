<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\ActivityWarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityWarningController extends Controller
{
    // Logs a warning straight from the Controller Activity page, using the
    // hours/requirement already computed and shown there for the range being
    // viewed -- avoids waiting on the quarterly cron for the common case of
    // a staff member spotting someone below requirement right now.
    public function store(Request $request)
    {
        $request->validate([
            'roster_member_id' => 'required|exists:roster,id',
            'quarter_label' => 'required|string',
            'quarter_start' => 'required|date',
            'hours_logged' => 'required|numeric',
            'hours_required' => 'required|numeric',
        ]);

        $rosterMember = RosterMember::findOrFail($request->input('roster_member_id'));

        $warning = ActivityWarning::firstOrCreate(
            ['roster_member_id' => $rosterMember->id, 'quarter_label' => $request->input('quarter_label')],
            [
                'cid' => $rosterMember->cid,
                'member_name' => $rosterMember->user ? trim($rosterMember->user->fname.' '.$rosterMember->user->lname) : $rosterMember->full_name,
                'quarter_start' => $request->input('quarter_start'),
                'hours_logged' => $request->input('hours_logged'),
                'hours_required' => $request->input('hours_required'),
            ]
        );

        return redirect()->route('network.warnings.index', ['quarter' => $warning->quarter_label])
            ->with('success', $warning->wasRecentlyCreated
                ? 'Warning logged for '.$warning->member_name.'.'
                : $warning->member_name.' already has a warning logged for this period -- edit it below.');
    }

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
