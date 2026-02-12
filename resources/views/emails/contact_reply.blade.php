@component('mail::message')
# Hello {{ $recipient_name }},

You have received a reply to your message.

@component('mail::panel')
{{ $reply_message }}
@endcomponent

**Replied by:** {{ $admin_name }}  
**Replied at:** {{ $replied_at }}

**Your Original Message:**

@component('mail::panel')
{{ $original_message }}
@endcomponent

If you need further assistance, just reply to this email.

Thanks,<br>
{{ $app_name }}
@endcomponent
