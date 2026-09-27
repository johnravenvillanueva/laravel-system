<footer class="footer">
  <div class="footer-inner">
    <div>
      <h4>☕ Issang Macchiatos</h4>
      <p class="text-sm" style="opacity:.75">Freshly brewed coffee and pastries, made with love.</p>
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
      <h4>Contact</h4>
      <a href="#">📍 Store Location</a>
      <a href="#">📞 0912 345 6789</a>
    </div>
  </div>
  <div class="footer-bottom">© {{ date('Y') }} Issang Macchiatos. All rights reserved.</div>
</footer>