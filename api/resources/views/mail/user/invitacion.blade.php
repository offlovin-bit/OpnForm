{{-- OFFLOVIN: invitación sencilla, en español y sin la marca de OpnForm --}}
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Te han invitado a {{ $workspaceName }}</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;">
  <tr>
    <td align="left" style="padding:40px 20px;">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:480px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;font-size:16px;line-height:1.6;color:#16181a;">
        @if ($workspaceIcon)
        <tr>
          <td style="padding:0 0 24px;">
            <img src="{{ $workspaceIcon }}" width="44" height="44" alt="{{ $workspaceName }}" style="display:block;width:44px;height:44px;border-radius:10px;">
          </td>
        </tr>
        @endif
        <tr>
          <td style="padding:0 0 12px;font-size:22px;line-height:1.3;font-weight:600;">Te han invitado a {{ $workspaceName }}</td>
        </tr>
        <tr>
          <td style="padding:0 0 28px;">Crea tu usuario para ver las respuestas y crear formularios del espacio {{ $workspaceName }}.</td>
        </tr>
        <tr>
          <td style="padding:0 0 12px;">
            <a href="{{ $inviteLink }}" style="display:inline-block;background:#16181a;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;padding:13px 26px;border-radius:999px;">Crear mi usuario</a>
          </td>
        </tr>
        <tr>
          <td style="padding:0 0 40px;font-size:14px;color:#5f6368;">La contraseña la pones tú. Este enlace caduca el {{ $caduca }}.</td>
        </tr>
        <tr>
          <td style="border-top:1px solid #e6e8eb;padding:20px 0 0;font-size:13px;line-height:1.5;color:#80868b;">Formularios de OFFLOVIN<br>Si no esperabas esta invitación, puedes ignorar este correo.</td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
