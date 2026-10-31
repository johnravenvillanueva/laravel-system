import "./base";

// Load page-specific scripts based on <body data-layout="">
const layout = document.body.dataset.layout || "public";

if (layout === "admin") {
    import("./admin");
}
if (layout === "public") {
    import("./customer");
}
