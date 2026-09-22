@component('mail::message')
# 🎉 Congratulations! Your Application Has Been Approved

Hello **{{ $registration->fullname }}**,

Great news! Your ACSES Portal registration has been **approved** by our administrators. Welcome to the ACSES community!

## Your Account Details

| Field | Value |
|:------|:------|
| **Full Name** | {{ $user->fullname }} |
| **Username** | {{ $user->username }} |
| **Email** | {{ $user->email }} |
| **Program** | {{ $user->class }} |
| **Year** | Year {{ $user->year }} |

@component('mail::panel')
**Important Security Notice**: Your password is the one you provided during registration. For your security, we never store or display passwords in plain text.
@endcomponent

## Get Started Now

You can now log in to the ACSES Portal and access all student features:

- 📢 View announcements and updates
- 📅 Check upcoming events
- 💳 Manage your dues and payments
- 📚 Access academic resources
- 💡 Submit suggestions

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Login to ACSES Portal
@endcomponent

## Need Help?

If you have any questions or encounter any issues, please don't hesitate to reach out to the ACSES team through the portal's suggestion system.

Welcome aboard!

Best regards,<br>
**The ACSES Team**

---
*This is an automated message from the ACSES Portal. Please do not reply directly to this email.*
@endcomponent
