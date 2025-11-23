<h2>Hola {{ $user->name }}</h2>
<p>Tu cuenta ha sido creada en el sistema.</p>
<p>La contraseña temporal es: <strong>admin</strong></p>
<p>Por seguridad, debes cambiar tu contraseña haciendo clic aquí:</p>

<p>
    <a href="{{ $url }}" style="background:#2d6cdf;color:white;padding:10px 18px;text-decoration:none;border-radius:5px;">
        Cambiar contraseña
    </a>
</p>

<p>Si no solicitaste este acceso, ignora este mensaje.</p>
