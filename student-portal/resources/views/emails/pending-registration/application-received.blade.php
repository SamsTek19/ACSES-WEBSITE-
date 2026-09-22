@component('mail::message')
# Application Received

Hello **{{ $registration->fullname }}**,

Thank you for submitting your registration request to join the **ACSES Portal**. We have successfully received your application.

## Your Application Details

| Field | Value |
|:------|:------|
| **Full Name** | {{ $registration->fullname }} |
| **Username** | {{ $registration->username }} |
| **Email** | {{ $registration->email }} |
| **Program** | {{ $registration->class }} |
| **Year** | Year {{ $registration->year }} |
| **Reference Number** | {{ $registration->index_number }} |

## What Happens Next?

Your application is now **pending review** by an ACSES administrator. This process typically takes **1-2 business days**.

You will receive an email notification once your application has been reviewed with one of the following outcomes:

- ✅ **Approved**: You'll receive your login credentials and can start using the portal immediately.
- ❌ **Rejected**: You'll be informed of the reason and can submit a new application if needed.

@component('mail::panel')
**Note**: If you did not submit this registration request, please ignore this email or contact support if you have concerns.
@endcomponent

If you have any questions or need to update your application details, please reach out to the ACSES team.

Best regards,<br>
**The ACSES Team**

---
*This is an automated message from the ACSES Portal. Please do not reply directly to this email.*
@endcomponent
