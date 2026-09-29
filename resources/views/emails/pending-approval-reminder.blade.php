<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Approval</title>
</head>
<body style="margin:0;background:#f3f4f6;color:#1f2937;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:24px 32px;border-bottom:1px solid #e5e7eb;">
                            <img src="{{ $logoUrl }}" alt="BIB Admin" width="150" style="display:block;max-width:150px;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 16px;font-size:22px;line-height:1.3;color:#111827;">Reminder Approval</h1>
                            <p style="margin:0 0 20px;line-height:1.6;">Halo {{ $approver->name ?: 'Approver' }}, masih ada dokumen yang membutuhkan approval Anda.</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;border-collapse:collapse;">
                                <tr><td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;">Contract</td><td align="right" style="padding:10px 12px;border-bottom:1px solid #e5e7eb;font-weight:bold;">{{ $pending['contracts'] }}</td></tr>
                                <tr><td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;">Debit Note</td><td align="right" style="padding:10px 12px;border-bottom:1px solid #e5e7eb;font-weight:bold;">{{ $pending['debit_notes'] }}</td></tr>
                                <tr><td style="padding:10px 12px;">Credit Note</td><td align="right" style="padding:10px 12px;font-weight:bold;">{{ $pending['credit_notes'] }}</td></tr>
                            </table>

                            <p style="margin:0 0 24px;">
                                <a href="{{ $approvalUrl }}" style="display:inline-block;padding:12px 20px;background:#0d6efd;color:#ffffff;text-decoration:none;font-weight:bold;">Buka daftar approval</a>
                            </p>
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#6b7280;">Jika tombol tidak dapat dibuka, gunakan link berikut:</p>
                            <p style="margin:4px 0 0;font-size:13px;line-height:1.6;word-break:break-all;"><a href="{{ $approvalUrl }}" style="color:#0d6efd;">{{ $approvalUrl }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;background:#f9fafb;color:#6b7280;font-size:12px;line-height:1.5;">Reminder ini dikirim setiap jam selama masih ada approval pending.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>