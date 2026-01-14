@component('mail::layout')

{{-- HEADER --}}
@slot('header')
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="background-color:#f4f7fb;padding:24px 10px;">

    <img src="https://www.sena.edu.co/Style%20Library/alayout/images/logoSena.png"
         alt="SENA"
         width="90"
         style="display:block;margin:0 auto 10px;">

    <span style="
        font-size:14px;
        font-weight:600;
        color:#0b2e4d;
        letter-spacing:0.3px;
    ">
        Semilleros · CIDE SENA
    </span>

</td>
</tr>
@endslot

{{-- BODY --}}
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="background-color:#f6f8fb;padding:30px 15px;">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:520px;background:#ffffff;
              border-radius:6px;
              border:1px solid #e5e9f0;">

<tr>
<td style="padding:32px;font-family:Arial,sans-serif;color:#1f2933;text-align:left;">

<p style="font-size:14px;color:#5f6c80;margin-bottom:6px;">
RECUPERACIÓN DE CONTRASEÑA
</p>

<h2 style="margin:0 0 16px 0;font-size:20px;color:#1f3a5f;">
¿Olvidaste tu contraseña?
</h2>

<p style="font-size:15px;color:#1f2933;margin-bottom:16px;">
Hola <strong>{{ $user->nombre }}</strong>,
</p>

<p style="font-size:15px;color:#5f6c80;margin-bottom:22px;">
Recibimos una solicitud para restablecer la contraseña de tu cuenta.
Para continuar, haz clic en el botón a continuación:
</p>

{{-- BOTÓN --}}
<table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:24px auto;">
<tr>
<td align="center" style="background-color:#77B900;border-radius:4px;">
<a href="{{ $url }}"
   style="display:inline-block;
          padding:12px 28px;
          font-size:14px;
          font-weight:600;
          color:#ffffff;
          text-decoration:none;">
Restablecer contraseña
</a>
</td>
</tr>
</table>

<p style="font-size:13px;color:#5f6c80;margin-top:20px;">
Este enlace expirará en <strong>60 minutos</strong>.
</p>

<p style="font-size:13px;color:#5f6c80;">
Si no solicitaste este cambio, puedes ignorar este correo.
</p>

<hr style="border:none;border-top:1px solid #e5e9f0;margin:26px 0;">

<p style="font-size:12px;color:#5f6c80;">
Este mensaje fue enviado automáticamente por el sistema de
<strong>Semilleros – CIDE SENA</strong>.
</p>

</td>
</tr>
</table>

</td>
</tr>
</table>

{{-- FOOTER --}}
@slot('footer')
<table width="100%" role="presentation">
<tr>
<td align="center" style="padding:18px;font-size:12px;color:#5f6c80;">
© {{ date('Y') }} SENA · CIDE · Semilleros  
</td>
</tr>
</table>
@endslot

@endcomponent
