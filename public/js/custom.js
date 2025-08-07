document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('a[href="https://github.com/hexters/hexa-docs"]').forEach(function (link) {
        const parentDiv = link.closest("div");
        if (parentDiv) {
            parentDiv.style.display = "none";
        }
    });
});
