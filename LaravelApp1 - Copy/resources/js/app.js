import "./base";

// Conditionally load admin / customer scripts based on <body data-layout="">
const layout = document.body.dataset.layout || "public";

if (layout === "admin") {
    import("./admin");
}
if (layout === "public") {
    import("./customer");
}

  document.querySelectorAll('.logout-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      if (!confirm('Are you sure you want to log out?')) {
        e.preventDefault();
      }
    });
  });