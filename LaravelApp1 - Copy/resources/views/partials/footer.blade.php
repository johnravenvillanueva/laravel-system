<footer class="footer">
  <div class="footer-inner">
    <div>
      <div class="footer-brand">
        <img src="{{ asset('images/products/isanglogoreal.jpg') }}" alt="" width="44" height="44">
        <span>Issang Macchiatos</span>
      </div>
      <p class="text-sm" style="max-width:24em">Freshly brewed coffee and pastries, made with love. Order ahead and skip the line.</p>
    </div>
    <div>
      <h4>Explore</h4>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('menu') }}">Menu</a>
    </div>
    <div>
      <h4>Account</h4>
      <a href="{{ route('login') }}">Login</a>
      <a href="{{ route('register') }}">Register</a>
    </div>
    <div>
      <h4>Visit us</h4>
      <a href="#">@include('partials.icon', ['ico' => 'map-pin']) Pinaod, San Ildefonso, Bulacan</a>
      <a href="#">@include('partials.icon', ['ico' => 'phone']) 0912 345 6789</a>
    </div>
  </div>
  <div class="footer-bottom">© {{ date('Y') }} Issang Macchiatos. All rights reserved.</div>
</footer>
