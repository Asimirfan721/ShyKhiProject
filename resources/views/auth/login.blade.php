<!DOCTYPE html>
<html lang="en">

<head>
  <title>E-SHOP || Login Page</title>
  @include('backend.layouts.head')
  <style>
    body {
      background: linear-gradient(120deg, #4e73df, #1cc88a);
      font-family: Arial, sans-serif;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .card {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      display: flex;
      flex-direction: row;
      padding: 0;
    }

    .bg-login-image {
      background: url('https://via.placeholder.com/400') center center / cover no-repeat;
      min-height: 100%;
      width: 50%;
    }

    .form-container {
      padding: 50px;
      width: 50%;
    }

    .text-center h1 {
      color: #333;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .form-control {
      border-radius: 50px;
      padding: 15px;
    }

    .btn-primary {
      background: #1cc88a;
      border: none;
      border-radius: 50px;
      padding: 10px 20px;
      font-size: 16px;
      transition: background 0.3s;
    }

    .btn-primary:hover {
      background: #17a673;
    }

    .form-check-label {
      font-size: 14px;
    }

    .btn-link {
      font-size: 14px;
      color: #4e73df;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .invalid-feedback {
      font-size: 13px;
      color: red;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="bg-login-image"></div>
        <div class="form-container">
          <div class="text-center">
            <h1>Welcome Back!</h1>
          </div>
          <form class="user" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
              <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter Email Address..." required autocomplete="email" autofocus>
              @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
            <div class="form-group">
              <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" placeholder="Password" name="password" required autocomplete="current-password">
              @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
            <div class="form-group">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                  {{ __('Remember Me') }}
                </label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-user btn-block">
              Login
            </button>
          </form>
          <hr>
          <div class="text-center">
            @if (Route::has('password.request'))
            <a class="btn btn-link small" href="{{ route('password.request') }}">
              {{ __('Forgot Your Password?') }}
            </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
