@component('mail::message')
# New Contact Message

You received a new contact message.

**From:** {{ $name }}  
**Email:** {{ $email }}  
**Sent:** {{ $sent_at }}

@component('mail::panel')
{{ $user_message }}
@endcomponent

Reply directly to this email to respond to the sender.

Thanks,<br>
{{ $app_name }}
@endcomponent
