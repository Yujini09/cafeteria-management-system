@component('mail::message')
# Hello {{ $user_name }},

An admin account has been created for you. Use the temporary password below to sign in.
You will be required to change your password after login.

@component('mail::panel')
Temporary Password: **{{ $temporary_password }}**
@endcomponent

@component('mail::button', ['url' => $login_url])
Sign In
@endcomponent

If you did not expect this email, please contact support.

Thanks,<br>
{{ $app_name }}
@endcomponent
