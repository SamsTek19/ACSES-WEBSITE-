@component('mail::message')
# Registration Status Update

Hello **{{ $registration->fullname }}**,

We have reviewed your ACSES Portal registration application. Unfortunately, we are **unable to approve** your registration at this time.

## Reason for Rejection

@component('mail::panel')
{{ $registration->rejection_reason ?: 'No specific reason was provided. Please contact the ACSES team for more information.' }}
@endcomponent

## Your Application Details

| Field | Value |
|:------|:------|
| **Full Name** | {{ $registration->fullname }} |
| **Username** | {{ $registration->username }} |
| **Email** | {{ $registration->email }} |
| **Program** | {{ $registration->class }} |
| **Year** | Year {{ $registration->year }} |
| **Reference Number** | {{ $registration->index_number }} |

## What You Can Do

If you believe this was an error or if you have addressed the issues mentioned above, you are welcome to submit a new registration application.

@component('mail::button', ['url' => $registerUrl, 'color' => 'primary'])
Submit New Application
@endcomponent

## Need Assistance?

If you have questions about this decision or need help with your registration, please contact the ACSES team directly.

We apologize for any inconvenience and hope to welcome you to the ACSES community soon.

Best regards,<br>
**The ACSES Team**

---
*This is an automated message from the ACSES Portal. Please do not reply directly to this email.*
@endcomponent
