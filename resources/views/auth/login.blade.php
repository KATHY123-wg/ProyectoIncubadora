<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login | HUEVSySTEM</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <style>
    .captcha-container {
      display: flex;
      justify-content: center;
      margin: 20px 0; /* separa el captcha del input y del botón */
    }
  </style>
</head>
<body>
  <div class="container">
    <form method="POST" action="/login" class="form">
      @csrf
      <div class="logo-container">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo Huevapp" class="logo">
      </div>
      <h2 class="title">Iniciar Sesión en HUEVSySTEM</h2>

      <label class="label">Usuario</label>
      <input type="text" name="usuario" value="{{ old('usuario') }}" class="input" required>

      <label class="label">Contraseña</label>
      <input type="password" name="password" class="input" required>

      <!-- Captcha centrado -->
      <div class="captcha-container">
        <div>
          {!! NoCaptcha::display() !!}
          @if ($errors->has('g-recaptcha-response'))
            <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
          @endif
        </div>
      </div>

      <button type="submit" class="button">Ingresar</button>

      @if ($errors->any())
        <p class="error">
          {{ $errors->first('usuario') ?? $errors->first('password') ?? $errors->first() }}
        </p>
      @endif
    </form>
  </div>
  {!! NoCaptcha::renderJs() !!}
</body>
</html>
