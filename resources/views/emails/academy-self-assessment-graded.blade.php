<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Self Assessment graded</title>
</head>
<body style="margin:0;padding:0;background:#f3f5f7;color:#263746;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3f5f7;padding:28px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border:1px solid #dde4ea;border-radius:10px;overflow:hidden;">
                <tr>
                    <td style="height:6px;background:#d4a72c;font-size:0;line-height:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:30px 32px 14px;">
                        <div style="font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#527086;margin-bottom:10px;">Winnipeg FIR Academy</div>
                        <h1 style="margin:0;color:#102f48;font-size:24px;line-height:1.3;">Your Self Assessment has been graded</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 30px;font-size:15px;line-height:1.65;color:#425563;">
                        <p style="margin:0 0 16px;">Hello {{ $student->fullName('F') }},</p>
                        <p style="margin:0 0 20px;">Your <strong>{{ $course->title }} Self Assessment</strong> has been graded. Your results and any feedback are now available in the Winnipeg FIR Academy.</p>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
                            <tr>
                                <td style="border-radius:6px;background:#123f60;">
                                    <a href="{{ $resultsUrl }}" style="display:inline-block;padding:12px 21px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">View Results</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:0;">If you have any questions about your result, please contact the Chief Instructor or FIR Chief.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 32px;background:#f8fafb;border-top:1px solid #e8edf1;color:#83909a;font-size:11px;line-height:1.5;">
                        This message was sent automatically by the Winnipeg FIR Academy. Please do not reply to this email.<br>
                        <span style="font-size:9px;color:#a5afb6;">For Flight Simulation Use Only.</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
